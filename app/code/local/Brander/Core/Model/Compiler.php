<?php
class Brander_Core_Model_Compiler {
    public function extractor($node) {
        $modules = explode(';', $this->spaceRemover($node['modules']));
        $options = explode(';', $this->spaceRemover($node['options']));
        $conditions = explode(';', $this->spaceRemover($node['conditions']));

        if (count($options) < count($modules)) {
            Mage::throwException(Mage::helper('brander_core')->__('There is not enough option(s) for specified module(s).'));
        }
        if (count($options) > count($modules)) {
            Mage::throwException(Mage::helper('brander_core')->__('There is not enough module(s) for specified option(s).'));
        }

        $modulesNum = count($modules);
        $comparesRes = array();

        for ($i = 0; $i < $modulesNum; $i++) {
            $option[$i] = explode(',', $options[$i]);
            $condition[$i] = explode(',', $conditions[$i]);

            for ($j = 0, $optionNum = count($option[$i]); $j < $optionNum; $j++) {
                $ifconfig[$i][$j] = trim((string)$modules[$i]) . trim((string)$option[$i][$j]);
                $con[$i][$j] = isset($condition[$i][$j]) ? trim((string)$condition[$i][$j]) : true;
                $comparesRes[] = $this->getAdminConfig($ifconfig[$i][$j]) === $this->getXmlCondition($con[$i][$j]);
            }
        }

        return $comparesRes;
    }

    public function spaceRemover($string) {
        $string = preg_replace('/ +/', ' ', (string)$string);
        return trim($string);
    }

    public function getAdminConfig($configPath) {
        $config = (string)Mage::getStoreConfig($configPath);
        switch ($config) {
            case '':
            case '0':
            case 'false':
            case 'FALSE':
                $config = false;
                break;

            case '1':
            case 'true':
            case 'TRUE':
                $config = true;
                break;
        }
        return $config;
    }

    public function getXmlCondition($condition) {
        $condition = (string)$condition;
        switch ($condition) {
            case '0':
            case 'false':
            case 'FALSE':
                $condition = false;
                break;

            case '':
            case '1':
            case 'true':
            case 'TRUE':
                $condition = true;
                break;
        }
        return $condition;
    }

    public function validator($node) {
        $message = $node['operation'] . ' - Action\'s method: ' . $node['method'] . ', module(s): ' . $node['modules'] . ', option(s): ' . $node['options'];

        $operation = $this->spaceRemover((string)$node['operation']);
        $removedValids = preg_replace('/AND|and|OR|or|\.|\+|\d+|\(+|\)+/', '', $operation);
        $invalids = preg_replace('/\s+/', '', $removedValids);
        if (!empty($invalids)) {
            $invalids = $this->spaceRemover($removedValids);
            Mage::throwException("Invalid operator(s) ({$invalids}) in operation: {$message}");
        }

        $oParNum = substr_count($operation, '(');
        $cParNum = substr_count($operation, ')');
        if ($oParNum != $cParNum) {
            Mage::throwException("Missing parenthesis in operation: {$message}");
        }

        $operation = preg_replace('/\(+|\)+/', '', $operation);
        if (preg_match('/AND|and|OR|or|\.|\+/', $operation)) {
            $operatorsNum = preg_match_all('/AND|and|OR|or|\.|\+/', $operation, $preDigMatchs);
            $operandsNum = preg_match_all('/\d+/', $operation, $posDigMatchs);

            if ($operatorsNum >= $operandsNum) {
                Mage::throwException("Missing operand(s) in operation: {$message}");
            }
            if ($operatorsNum < $operandsNum - 1) {
                Mage::throwException("Missing operator(s) in operation: {$message}");
            }
            return true;
        }

        return is_numeric($operation) ? true : false;
    }

    public function getToken($operation) {
        return $this->_token($operation);
    }

    private function _token($operation) {
        $len = strlen($operation);
        $tokens = array();

        for ($i = 0, $offset = 0; $i < $len;) {
            switch ($operation[$i]) {
                case '(':
                    $tokens[] = '(';
                    $offset++;
                    $i++;
                    break;

                case ')':
                    $tokens[] = ')';
                    $offset++;
                    $i++;
                    break;

                case '.':
                    $tokens[] = '&&';
                    $offset++;
                    $i++;
                    break;

                case '+':
                    $tokens[] = '||';
                    $offset++;
                    $i++;
                    break;

                default:
                    preg_match('/^AND|^and|^OR|^or|^\d+/', substr($operation, $offset), $match, PREG_OFFSET_CAPTURE);
                    if ($match) {
                        switch ($match[0][0]) {
                            case 'AND':
                            case 'and':
                                $tokens[] = '&&';
                                $i += strlen($match[0][0]);
                                $offset = $i;
                                break 2;

                            case 'OR':
                            case 'or':
                                $tokens[] = '||';
                                $i += strlen($match[0][0]);
                                $offset = $i;
                                break 2;

                            default:
                                $tokens[] = $match[0][0];
                                $i += strlen($match[0][0]);
                                $offset = $i;
                                break 2;
                        }
                    } else {
                        $offset++;
                        $i++;
                    }
                    break;
            }
        }
        return $tokens;
    }

    public function operation($conditions, $tokens) {
        return $this->_compare($this->_rpn($conditions, $tokens));
    }

    private function _compare($outputStack) {
        $res = array();
        $len = count($outputStack);

        if ($len > 1) {
            for ($i = 0; $i < $len; $i++) {
                if (is_bool($outputStack[$i])) {
                    continue;
                } else if ($outputStack[$i] === '&&') {
                    $con1 = isset($outputStack[$i - 2]) && is_bool($outputStack[$i - 2]);
                    $con2 = isset($outputStack[$i - 1]) && is_bool($outputStack[$i - 1]);

                    if ($con1 && $con2) {
                        $outputStack[$i] = $outputStack[$i - 2] && $outputStack[$i - 1];
                        unset($outputStack[$i - 2], $outputStack[$i - 1]);
                    } else {
                        return $this->_compare(array_values($outputStack));
                    }
                } else if ($outputStack[$i] === '||') {
                    $con1 = isset($outputStack[$i - 2]) && is_bool($outputStack[$i - 2]);
                    $con2 = isset($outputStack[$i - 1]) && is_bool($outputStack[$i - 1]);

                    if ($con1 && $con2) {
                        $outputStack[$i] = $outputStack[$i - 2] || $outputStack[$i - 1];
                        unset($outputStack[$i - 2], $outputStack[$i - 1]);
                    } else {
                        return $this->_compare(array_values($outputStack));
                    }
                }
            }
        }

        return end($outputStack);
    }

    private function _rpn($conditions, $tokens) {
        $outputStack = array();
        $operatorStack = array();

        if (empty($tokens)) {
            for ($i = 0, $conLen = count($conditions); $i < $conLen; $i++) {
                $tokens[] = $i;
                $tokens[] = '&&';
            }
            array_pop($tokens);
        }

        for ($i = 0, $len = count($tokens); $i < $len; $i++) {
            if (is_numeric($tokens[$i])) {
                $condition = $conditions[$tokens[$i]];
                $outputStack[] = $condition !== null ? $condition : Mage::throwException(Mage::helper('brander_core')->__('Specified operands are more than condition(s).'));
            } else {
                $stackEnd = end($operatorStack);
                $newToken = $tokens[$i];

                if ($stackEnd === false) {
                    $operatorStack[] = $newToken;
                } else {
                    $seScore = $this->_rating($stackEnd);
                    $ntScore = $this->_rating($newToken);

                    if ($ntScore > 0 && $ntScore <= 100) {
                        if ($ntScore > $seScore) {
                            $operatorStack[] = $newToken;
                        } else if ($ntScore <= $seScore) {
                            if ($seScore === 100) {
                                $operatorStack[] = $newToken;
                            } else {
                                $outputStack[] = array_pop($operatorStack);
                                $operatorStack[] = $newToken;
                            }
                        }
                    } else if ($ntScore === 0) {
                        while ($this->_rating(end($operatorStack)) < 100) {
                            $outputStack[] = array_pop($operatorStack);
                        }
                        array_pop($operatorStack);
                    }
                }
            }
        }
        for ($i = 0, $stackLen = count($operatorStack); $i < $stackLen; $i++) {
            $outputStack[] = array_pop($operatorStack);
        }

        return $outputStack;
    }

    private function _rating($token) {
        switch ($token) {
            case '(':
                return 100;
                break;

            case '&&':
                return 80;
                break;

            case '||':
                return 79;
                break;

            case ')':
                return 0;
                break;
        }
    }
}
