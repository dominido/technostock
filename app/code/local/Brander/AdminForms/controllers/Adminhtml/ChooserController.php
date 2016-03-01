<?php
class Brander_AdminForms_Adminhtml_ChooserController extends Mage_Adminhtml_Controller_Action
{

    protected function _construct()
    {
        // Define module dependent translate
        $this->setUsedModuleName('Brander_AdminForms');
    }

    /**
     * Block chooser Action (Ajax request)
     *
     */
    public function indexAction()
    {
        $selected = $this->getRequest()->getParam('selected', '');
        $blockKey = $this->getRequest()->getParam('block_key', '');
        $chooser = $this->getLayout()
            ->createBlock('adminforms/adminhtml_widget_chooser')
            ->setName(Mage::helper('core')->uniqHash('adminhtml_grid_'))
            ->setUseMassaction(true)
            ->setBlockKey($blockKey)
            ->setSelectedBlocks(explode(',', $selected));
        /* @var $serializer Mage_Adminhtml_Block_Widget_Grid_Serializer */
        $serializer = $this->getLayout()->createBlock('adminhtml/widget_grid_serializer');
        $serializer->initSerializerBlock($chooser, 'getSelectedBlocks', 'selected_blocks', 'selected_blocks');
        $this->getResponse()->setBody($chooser->toHtml().$serializer->toHtml());
    }

    /**
     * Chooser Source action
     */
    public function chooserAction()
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');
        $massAction = $this->getRequest()->getParam('use_massaction', false);
        $blockKey = $this->getRequest()->getParam('block_key', null);

        $blocksGrid = $this->getLayout()->createBlock('adminforms/adminhtml_widget_chooser', '', array(
            'id'                => $uniqId,
            'use_massaction'    => $massAction,
            'block_key'         => $blockKey
        ));

        $html = $blocksGrid->toHtml();
        $this->getResponse()->setBody($html);
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
