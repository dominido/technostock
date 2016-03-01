<?php
class Brander_AdminForms_Block_Adminhtml_Block extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    protected $_blockGroup = 'adminforms';
    protected $_gridBlock = null;

    public function __construct($data)
    {
		$this->_gridBlock = $data['grid_block'];
        $this->_controller      = 'adminhtml_block';
        $this->_headerText      = Mage::helper('tax')->__('Blocks');
        $this->_addButtonLabel  = Mage::helper('tax')->__('Add New Block');
        parent::__construct();

		Mage::dispatchEvent('adminforms_block', array('container' => $this, 'request' => $this->getRequest()));
		Mage::dispatchEvent('adminforms_block_'.$this->getRequest()->getParam('block_key'), array('container' => $this, 'request' => $this->getRequest()));
    }
    
    protected function _prepareLayout()
    {
        $this->setChild( 'grid',
            $this->getLayout()->createBlock( $this->_gridBlock,
            $this->_controller . '.grid')->setSaveParametersInSession(true) );
        return Mage_Adminhtml_Block_Widget_Container::_prepareLayout();
    }

    public function getCreateUrl()
    {
        return $this->getUrl('*/*/new',array('block_key'=>$this->getRequest()->getParam('block_key'),'store'=>$this->getRequest()->getParam('store')));
    }

    public function setHeaderText($text)
    {
        $this->_headerText = $text;
    }
}
