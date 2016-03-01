<?php

/**
 * Created by JetBrains PhpStorm.
 * User: turumburum
 * Date: 18.03.14
 * Time: 18:12
 * To change this template use File | Settings | File Templates.
 */
class Brander_Core_Helper_Data extends Mage_Core_Helper_Abstract
{
    const NAME_DIR_CSS = 'css/brander/core';
    const NAME_DIR_JS = 'jquery';
    const PLUGIN_DIR_JS = 'plugins';
    const XML_CONFIG_PATH_JS = 'frontend/layout/brander_js';
    const XML_CONFIG_PATH_CSS = 'frontend/layout/brander_css';

    protected $_filesJs = array();
    protected $_filesCss = array();


    /**
     * @param string $path
     * @return array
     * @throws Exception
     */
    protected function _parseConfig($path) {
        $res = Mage::app()->getConfig();
        $isDevMode = Mage::getIsDeveloperMode();
        $data = $res->getNode($path);
        if ($data === false) {
            $s = 'There is no configuration in: <' . str_replace('/', '><', $path) . '>... config.xml';
            if ($isDevMode) {
                throw new Exception(str_replace(array('<', '>'), array('&lt;', '&gt;'), $s));
            }
            Mage::log($s);
            return array();//silence is gold
        }
        $data = $data->asArray();
        usort($data, function($a, $b){
            $v1 = isset($a['priority']) ? (int)$a['priority'] : 0;
            $v2 = isset($b['priority']) ? (int)$b['priority'] : 0;
            if ($v1 < $v2) {
                return 1;
            } elseif ($v2 < $v1) {
                return -1;
            }
            return 0;
        });
        $res = array();
        foreach($data as $scriptId) {
            if (isset($scriptId['enabled']) &&
                ($scriptId['enabled'] === '0' || $scriptId['enabled'] === 'false' || $scriptId['enabled'] === 'off')) {
                continue;
            }
            if ($isDevMode && isset($scriptId['dev'])) {
                $res[] = (string)$scriptId['dev'];
            } else {
                $res[] = (string)$scriptId['prod'];
            }
        }
        return $res;
    }

    public function __construct() {
        $this->_filesJs = $this->_parseConfig(self::XML_CONFIG_PATH_JS);
        $this->_filesCss = $this->_parseConfig(self::XML_CONFIG_PATH_CSS);
    }

    protected function includePlugins() {
        $dirPath = Mage::getBaseDir() . DS . 'js' . DS . self::NAME_DIR_JS . DS . self::PLUGIN_DIR_JS;
        if (is_dir($dirPath)) {
            if ($dh = opendir($dirPath)) {
                while (($file = readdir($dh)) !== false) {

                    if (pathinfo($file, PATHINFO_EXTENSION) == 'js') {
                        array_push($this->_filesJs, self::PLUGIN_DIR_JS . '/' . $file);
                    }
                }
                closedir($dh);
            }
        }

    }

    public function getJQueryPath($file) {
        return self::NAME_DIR_JS . '/' . $file;
    }

    public function getCssPath($file) {
        return self::NAME_DIR_CSS . '/' . $file;
    }

    public function getFilesJs() {
        $this->includePlugins();

        return $this->_filesJs;
    }

    public function getFilesCss() {
        return $this->_filesCss;
    }

    public function getResizedImage($path, $pathCache, $fileName, $newFileName, $width, $height, $quality = 100) {

        $imageUrl = Mage::getBaseDir('media') . DS . $path . DS . $fileName;
        if (!is_file($imageUrl)) {
            return false;
        }

        $imageResized = Mage::getBaseDir('media') . DS . $pathCache . DS . $newFileName;
        if (!file_exists($imageResized) && file_exists($imageUrl) || file_exists($imageUrl) && filemtime($imageUrl) > filemtime($imageResized)) :
            $imageObj = new Varien_Image ($imageUrl);
            $imageObj->constrainOnly(false);
            $imageObj->keepFrame(true);
            $imageObj->backgroundColor(array(255, 255, 255));
            $imageObj->keepTransparency(true);
            $imageObj->keepAspectRatio(true);
            $imageObj->quality($quality);
            $imageObj->resize($width, $height);
            $imageObj->save($imageResized);
        endif;

        if (file_exists($imageResized)) {
            return Mage::getBaseUrl('media') . $pathCache . "/" . $newFileName;
        } else {
            return Mage::getBaseUrl('media') . $path . "/" . $fileName;
        }

    }

    public function getRelativePath($url) {
        return str_replace(Mage::getUrl('/'), '/', $url);
    }
}