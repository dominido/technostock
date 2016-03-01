<?php
class Brander_Faq_IndexController extends Mage_Core_Controller_Front_Action
{
    public function postAction() {
        $blockData = $this->getRequest()->getPost('faq');

        if ($blockData && count($blockData)) {
            try {

                if(isset($blockData['page_id'])) {
                    $blockData['page_id'] = Mage::helper('core')->decrypt($blockData['page_id']);
                    $page = Mage::getModel('cmsadvanced/page')->load($blockData['page_id']);
                    if(!$page instanceof Brander_Cms_Model_Page) {
                        throw new Mage_Core_Exception(Mage::helper('courteouscom_faq')->__('Faq page not found!'));
                    }
                }

                $entityType = 'branderfaqentities';
                $block = Mage::getModel('adminforms/block',array('entity_type'=>$entityType))
                    ->setStoreId(0);

                $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
                    ->setEntityTypeFilter(Mage::getSingleton('eav/config')->getEntityType($entityType)->getEntityTypeId())
                    ->load()
                    ->toOptionArray();
                if (count($sets)==1)
                    $block->setAttributeSetId($sets[0]['value']);

                $block->setStoreId(0);

                $blockData['status'] = 0;
                $blockData['category'] = 0;
                $blockData['created_at'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');
                $block->addData($blockData);

                $block->save();
                $this->_getSession()->addSuccess(Mage::helper('brander_faq')->__('Thank you for submitting your request.'));
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage())
                    ->setBlockData($blockData);
            }
            catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage());
            }
        }

        $this->_redirectReferer();
    }

    private function _getSession() {
        return Mage::getSingleton('core/session');
    }

    public function moreFaqAction()
    {
        $params = $this->getRequest()->getParams();
        if($params){
            $block = $this->getLayout()->createBlock('brander_faq/faq','faq.faq_elements')
                ->setFaqIncrement($params['increment'])
                ->setTemplate('brander/faq/faq_elements.phtml');
            $this->getResponse()
                 ->setHeader('Content-Type', 'text/html')
                 ->setBody($block->toHtml());
            return;
        }
        $this->_forward('no-route');
        return;
    }
}