<?php
class Brander_AdminForms_Adminhtml_BlockController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Array of actions which can be processed without secret key validation
     *
     * @var array
     */
    protected $_publicActions = array('edit');

    protected function _construct()
    {
        // Define module dependent translate
        $this->setUsedModuleName('Brander_AdminForms');
    }

    /**
     * Initialize block from request parameters
     *
     * @return Brander_AdminForms_Model_Block
     */
    protected function _initBlock()
    {
        $blockKey = $this->getRequest()->getParam('block_key');
        $data = Mage::getResourceModel('adminforms/block')->getEntityInfo($blockKey);

        $blockEntityId = null;
        if(is_array($data))
        {
            $entityType = $data['entity_type'];
            $blockEntityId = $entityId = $data['entity_id'];
            Mage::register('current_block_entity_can_back', $blockEntityId?true:false);
        }
        else
            return;

        if (!is_null($this->getRequest()->getParam('entity_id',null)))
            $blockEntityId = $entityId = $this->getRequest()->getParam('entity_id');
        else
            $this->getRequest()->setParam('entity_id',$entityId);

        $block = Mage::getModel('adminforms/block',array('entity_type'=>$entityType))
            ->setStoreId($this->getRequest()->getParam('store', 0));

        if ($entityId) {
            $block->load($entityId);
        }

        if (!$entityId) {
            if ($setId = (int) $this->getRequest()->getParam('set')) {
                $block->setAttributeSetId($setId);
            }
            else
            {
                $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
                    ->setEntityTypeFilter(Mage::getSingleton('eav/config')->getEntityType($entityType)->getEntityTypeId())
                    ->load()
                    ->toOptionArray();
                if (count($sets)==1)
                    $block->setAttributeSetId($sets[0]['value']);
            }
        }

        $block->setStoreId($this->getRequest()->getParam('store', 0));

        Mage::register('block', $block);
        Mage::register('current_block', $block);
        Mage::register('current_block_entity', $blockEntityId?true:false);

        return $block;
    }

    /**
     * Block list page
     */
    public function indexAction()
    {
        $this->loadLayout();

        $blockKey = $this->getRequest()->getParam('block_key');
        $data = Mage::getResourceModel('adminforms/block')->getEntityInfo($blockKey);

        if(is_array($data) && @$data['entity_grid_block'])
            $entityTypeGridBlock = $data['entity_grid_block'];
        else
            return;
        $this->_addContent(
            $this->getLayout()->createBlock('adminforms/adminhtml_block', 'block', array('grid_block'=>$entityTypeGridBlock))
        );
        //$this->getLayout()->getBlock('block')->append($this->getLayout()->createBlock($entityType, 'block_grid'));
        $this->renderLayout();
    }


    /**
     * Block grid for AJAX request
     */
    public function gridAction()
    {
        $blockKey = $this->getRequest()->getParam('block_key');
        $data = Mage::getResourceModel('adminforms/block')->getEntityInfo($blockKey);

        if(is_array($data) && @$data['entity_grid_block'])
            $entityTypeGridBlock = $data['entity_grid_block'];
        else
            return;

        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock($entityTypeGridBlock)->toHtml()
        );
    }

    /**
     * Create new block page
     */
    public function newAction()
    {
        $block = $this->_initBlock();

        $this->_title($this->__('New Block'));

        Mage::dispatchEvent('adminforms_block_new_action', array('block' => $block));
		Mage::dispatchEvent('adminforms_block_new_action_'.$block->getEntityType(), array('action'=>$this,'block' => $block));
        
        $this->loadLayout(array('default','editor'));
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        if ($js = $this->getLayout()->getBlock('js'))
            $js->append($this->getLayout()->createBlock('core/template', '',array('template'=>'catalog/wysiwyg/js.phtml')));
        $this->_addContent(
            $this->getLayout()->createBlock('adminforms/adminhtml_block_edit', 'block_edit')
        );

        $this->_addLeft(
            $this->getLayout()->createBlock('adminforms/adminhtml_block_edit_tabs', 'block_tabs')
        );

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->getLayout()->getBlock('head')->addJs('adminforms/adminhtml.js');
        
        $this->renderLayout();
    }

    /**
     * Block edit form
     */
    public function editAction()
    {
        $block = $this->_initBlock();

        if ($block && !$block->getEntityId() || !$block) {
            $this->_getSession()->addError(Mage::helper('adminforms')->__('This block no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }

        $this->_title($block->getTitle());

        Mage::dispatchEvent('adminforms_block_edit_action', array('block' => $block));
		Mage::dispatchEvent('adminforms_block_edit_action_'.$block->getEntityType(), array('action'=>$this,'block' => $block));
        $this->loadLayout(array('default','editor'));
        if (!Mage::app()->isSingleStoreMode() && ($switchBlock = $this->getLayout()->getBlock('store_switcher'))) {
            $switchBlock->setDefaultStoreName($this->__('Default Values'))
                ->setSwitchUrl($this->getUrl('*/*/*', array('_current'=>true, 'active_tab'=>null, 'tab' => null, 'store'=>null)));
        }
        if ($js = $this->getLayout()->getBlock('js'))
            $js->append($this->getLayout()->createBlock('core/template', '',array('template'=>'catalog/wysiwyg/js.phtml')));
        $this->_addContent(
            $this->getLayout()->createBlock('adminforms/adminhtml_block_edit', 'block_edit')
        );
        $this->_addLeft(
            $this->getLayout()->createBlock('adminhtml/store_switcher', 'store_switcher')
        );
        $this->_addLeft(
            $this->getLayout()->createBlock('adminforms/adminhtml_block_edit_tabs', 'block_tabs')
        );

//      $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }

    /**
     * Initialize block before saving
     */
    protected function _initBlockSave()
    {
        $block    = $this->_initBlock();
        $blockData = $this->getRequest()->getPost('block');

        $block->addData($blockData);

        /**
         * Check "Use Default Value" checkboxes values
         */
        if ($useDefaults = $this->getRequest()->getPost('use_default')) {
            foreach ($useDefaults as $attributeCode) {
                $block->setData($attributeCode, false);
            }
        }

        Mage::dispatchEvent('adminforms_block_prepare_save', array('block' => $block, 'request' => $this->getRequest()));

        return $block;
    }

    /**
     * Save block action
     */
    public function saveAction()
    {
        $storeId        = $this->getRequest()->getParam('store');
        $redirectBack   = $this->getRequest()->getParam('back', false);
        $blockId        = $this->getRequest()->getParam('entity_id');

        $data = $this->getRequest()->getPost();
		$block = null;
        if ($data) {
            $block = $this->_initBlockSave();

            try {
                $block->save();
                $blockId = $block->getId();

                $this->_getSession()->addSuccess($this->__('The item has been saved.'));
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage())
                    ->setBlockData($data);
                $redirectBack = true;
            }
            catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage());
                $redirectBack = true;
            }
        }

        if ($redirectBack)
            $this->_redirect('*/*/edit', array(
                'entity_id' => $blockId,
                '_current' => true
            ));
        else
		{
			$this->_getSession()->setIsUrlNotice($this->getFlag('', self::FLAG_IS_URLS_CHECKED));
			$this->_redirectUrl($this->getUrl('*/*/', array('store'=>$storeId, 'block_key'=>$this->getRequest()->getParam('block_key'))));
			Mage::dispatchEvent('adminforms_block_save_success_action', array('block' => $block));
			Mage::dispatchEvent('adminforms_block_save_success_action_'.$block->getEntityType(), array('action'=>$this,'block' => $block));
		}
    }

   /**
     * @deprecated since 1.4.0.0-alpha2
     */
    protected function _decodeInput($encoded)
    {
        parse_str($encoded, $data);
        foreach($data as $key=>$value) {
            parse_str(base64_decode($value), $data[$key]);
        }
        return $data;
    }

    /**
     * Delete block action
     */
    public function deleteAction()
    {
        if ($entityId = $this->getRequest()->getParam('entity_id')) {
            $block = $this->_initBlock();
            try {
                $block->delete();
                $this->_getSession()->addSuccess($this->__('The block has been deleted.'));
            }
            catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
		$this->_getSession()->setIsUrlNotice($this->getFlag('', self::FLAG_IS_URLS_CHECKED));
        $this->getResponse()->setRedirect($this->getUrl('*/*/', array('store'=>$this->getRequest()->getParam('store'), 'block_key'=>$this->getRequest()->getParam('block_key'))));
		Mage::dispatchEvent('adminforms_block_delete_action', array('block' => $block));
		Mage::dispatchEvent('adminforms_block_delete_action_'.$block->getEntityType(), array('action'=>$this,'block' => $block));
    }

    public function wysiwygAction()
    {
        $elementId = $this->getRequest()->getParam('element_id', md5(microtime()));
        $content = $this->getLayout()->createBlock('adminhtml/catalog_helper_form_wysiwyg_content', '', array(
            'editor_element_id' => $elementId
        ));
        $this->getResponse()->setBody($content->toHtml());
    }


    /**
     * Check for is allowed
     *
     * @return boolean
     */
    /*protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('adminforms/blocks');
    }*/
}
