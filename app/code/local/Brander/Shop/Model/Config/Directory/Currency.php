<?php
class Brander_Shop_Model_Config_Directory_Currency extends Mage_Directory_Model_Currency
{

    /**
     * Wrap symbol, use pattern
     *
     * @param float $price
     * @param int   $precision
     * @param array $options
     * @param bool  $includeContainer
     * @param bool  $addBrackets
     * @return string
     */
    public function formatPrecision($price, $precision, $options = array(), $includeContainer = true,
        $addBrackets = false)
    {

        if (!isset($options['precision'])) {
            $options['precision'] = $precision;
        }
        if ($includeContainer) {
            $priceText =  '<span class="price">' . ($addBrackets ? '[' : '') . $this->formatTxt($price, $options) .
            ($addBrackets ? ']' : '') . '</span>';

            $currency_code = Mage::app()->getStore()->getCurrentCurrencyCode();
            $symbol = Mage::app()->getLocale()->currency($currency_code)->getSymbol();

            $priceText = str_replace($symbol, '<span class="price-currency">'.$symbol.'</span>', $priceText);
            return $priceText;
        } else {
            $priceText =  ($addBrackets ? '[' : '') . $this->formatTxt($price, $options) . ($addBrackets ? ']' : '');
            $currency_code = Mage::app()->getStore()->getCurrentCurrencyCode();
            $symbol = Mage::app()->getLocale()->currency($currency_code)->getSymbol();
            $priceText = str_replace($symbol, '<span class="price-currency">'.$symbol.'</span>', $priceText);
            return $priceText;
        }

    }

}