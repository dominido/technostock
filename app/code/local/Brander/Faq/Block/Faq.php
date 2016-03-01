<?php

class Brander_Faq_Block_Faq extends Mage_Core_Block_Template
{
    protected $_faqCollection = null;

    public function getCollection()
    {
        if(!$this->_faqCollection){
            $this->_faqCollection = Mage::helper('adminforms')->getCollection('branderfaqentities')
                    ->addAttributeToFilter('status', Brander_Faq_Model_Faq::BRANDER_FAQ_STATUS_APPROVED)
                    ->addAttributeToSort('position', 'ASC')
                    ->addAttributeToSort('entity_id', 'ASC');
        }
        return $this->_faqCollection;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $page = Mage::app()->getRequest()->getParam('page');
        $page = isset($page) ? $page : 1;
        $limit = Mage::helper('brander_faq')->getFaqLimit();
        $this->setFaqCollection($this->getCollection()
                ->setPageSize($limit)
                ->setCurPage($page));
        return $this;
    }



    public function getFaqConfig(){
        $config = array(
            'limit_faq'        => Mage::helper('brander_faq')->getFaqLimit(),
            'max_faq_size'     => $this->getCollectionSize(),
            'url_faq'          => Mage::getUrl('brander_faq/index/morefaq')
        );
        return Zend_Json::encode($config);
    }


    public function getCollectionSize(){
        return $this->getCollection()->getSize();
    }


    public function getButtonMore(){
        $limit = Mage::helper('brander_faq')->getFaqLimit();
        $collectionSize = $this->getCollectionSize();
        if($limit<$collectionSize){
            return $collectionSize-$limit<$limit ? $collectionSize-$limit : $limit;
        }
        return false;
    }

}