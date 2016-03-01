<?php
class Brander_SearchAutocomplete_Model_CatalogSearch_Query extends Mage_CatalogSearch_Model_Query {

    public function prepare() {
        if (!$this->getId()) {
            $this->setIsCmspageProcessed(0);
        }
        return parent::prepare();
    }

}
