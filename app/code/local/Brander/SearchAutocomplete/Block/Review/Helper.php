<?php
class Brander_SearchAutocomplete_Block_Review_Helper extends Mage_Review_Block_Helper {

    public function getSummaryHtml($product, $templateType, $displayIfNoReviews) {
        if (empty($this->_availableTemplates[$templateType])) {
            $templateType = 'default';
        }

        $actionName = Mage::app()->getRequest()->getActionName();
        $controllerName = Mage::app()->getRequest()->getControllerName();

        if ($actionName == 'suggest' && $controllerName == 'ajax') {
            $this->setTemplate('brander/searchautocomplete/summary_short.phtml');
        } else {
            $this->setTemplate($this->_availableTemplates[$templateType]);
        }

        $this->setDisplayIfEmpty($displayIfNoReviews);

        if (!$product->getRatingSummary()) {
            Mage::getModel('review/review')
                    ->getEntitySummary($product, Mage::app()->getStore()->getId());
        }
        $this->setProduct($product);

        return $this->toHtml();
    }

}
