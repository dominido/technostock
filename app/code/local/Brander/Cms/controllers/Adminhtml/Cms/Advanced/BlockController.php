<?php
require_once 'Brander/AdminForms/controllers/Adminhtml/BlockController.php';

class Brander_Cms_Adminhtml_Cms_Advanced_BlockController extends Brander_AdminForms_Adminhtml_BlockController
{
    protected function _initBlock()
    {
        $block = parent::_initBlock();
        
        if ($pageId = $this->getPageId()) {
            $block->setPageId($pageId);
        }

        return $block;
    }

    public function indexAction()
    {
        echo '<script type="text/javascript">window.close();</script>';
    }

    public function addActionLayoutHandles()
    {
        parent::addActionLayoutHandles();

        $this->getLayout()->getUpdate()->addHandle('adminforms_is_window');

        return $this;
    }

    public function getPageId()
    {
        return $this->getRequest()->getParam('page_id');
    }

    public function gridAction()
    {
        $this->loadLayout();
        $blockKey = $this->getRequest()->getParam('block_key');

        $blockGrid = Mage::helper('cmsadvanced')->getBlockGrid($blockKey);

        $this->getResponse()->setBody(
            $this->getLayout()->createBlock($blockGrid, $blockKey . '_grid', array('entity_type' => $blockKey, 'block_key' => $blockKey))
                 ->setPageId($this->getPageId())
                 ->toHtml()
        );
    }
}
