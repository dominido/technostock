<?php
class Brander_SearchAutocomplete_Model_Highlight {

    public function productAttribute($obj, $result, $params) {
        if (isset($params['attribute'])) {
            $helper = Mage::helper('searchautocomplete');
            $query = $helper->getLastQueryText();
            $attribute = $params['attribute'];
            if (strpos($attribute, 'meta') !== 0
                    && strpos($attribute, 'url') !== 0
                    && $attribute != 'image'
                    && $attribute != 'small_image'
                    && $attribute != 'thumbnail') {
                $result = $helper->highlightText($result, $query);
            }
        }
        return $result;
    }

}