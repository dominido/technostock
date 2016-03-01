<?php
class Brander_AdminForms_Block_Adminhtml_Block_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected $_blockGroup = 'adminforms';

    public function __construct()
    {
        $this->_objectId    = 'entity_id';
        $this->_controller  = 'adminhtml_block';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('tax')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('tax')->__('Delete Item'));

		$this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save and Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

		$this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";

		
        $params = array('entity_id'=>$this->getRequest()->getParam('entity_id'),'set'=>$this->getRequest()->getParam('set'),'store'=>$this->getRequest()->getParam('store'),'block_key'=>$this->getRequest()->getParam('block_key'));
        if (Mage::registry('current_block_entity_can_back'))
        {
            $this->removeButton('back');
            $this->removeButton('delete');
            $params['back']='1';
        }

        $this->setFormActionUrl($this->getUrl('*/*/save',$params));

		Mage::dispatchEvent('adminforms_block_edit', array('container' => $this, 'request' => $this->getRequest()));
		Mage::dispatchEvent('adminforms_block_edit_'.Mage::registry('block')->getEntityType(), array('container' => $this, 'block' => Mage::registry('block')));
    }

    public function getHeaderText()
    {
		if (!$this->hasData('header_text'))
		{
			if (Mage::registry('block')->getId())
			{
				if (Mage::registry('block')->getTitle())
					return Mage::helper('adminforms')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('block')->getTitle()));
				else
					return Mage::helper('adminforms')->__("Edit Item");
			}
			else
				return Mage::helper('adminforms')->__('New Item');
		}
		return $this->getData('header_text');
    }

    /**
     * Get form action URL
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        if ($this->hasFormActionUrl()) {
            return $this->getData('form_action_url');
        }
    }


    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
		if (!$this->hasData('back_url'))
		{
			 $this->setData('back_url',$this->getUrl('*/*/', array('store'=>$this->getRequest()->getParam('store'), 'block_key'=>$this->getRequest()->getParam('block_key'))));
		}
		return $this->getData('back_url');
    }

    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array($this->_objectId => $this->getRequest()->getParam($this->_objectId),'store'=>$this->getRequest()->getParam('store'),'block_key'=>$this->getRequest()->getParam('block_key')));
    }

	public function getObjectId()
	{
		return $this->_objectId;
	}
}
