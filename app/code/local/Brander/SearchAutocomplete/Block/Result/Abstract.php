<?php
if ((string)Mage::getConfig()->getModuleConfig('GoMage_Navigation')->active=='true') {
    class Brander_SearchAutocomplete_Block_Result_Abstract extends GoMage_Navigation_Block_Search_Result {}
} else {
    class Brander_SearchAutocomplete_Block_Result_Abstract extends Mage_CatalogSearch_Block_Result {}
}
