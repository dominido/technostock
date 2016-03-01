<?php
class Brander_AdminForms_Block_Adminhtml_Helper_Form_Wysiwyg extends Varien_Data_Form_Element_Textarea
{
    /**
     * Retrieve additional html and put it at the end of element html
     *
     * @return string
     */
    public function getAfterElementHtml()
    {
        $html = parent::getAfterElementHtml();
        if ($this->getIsWysiwygEnabled()) {
            $html .= Mage::getSingleton('core/layout')
                ->createBlock('adminhtml/widget_button', '', array(
                    'label'   => Mage::helper('catalog')->__('WYSIWYG Editor'),
                    'type'    => 'button',
                    'onclick' => 'catalogWysiwygEditor.open(\''.Mage::helper('adminhtml')->getUrl('*/*/wysiwyg').'\', \''.$this->getHtmlId().'\')',
					'style'   => 'display:block;'
                ))->toHtml();
        }
        return $html;
    }

    /**
     * Check whether wysiwyg enabled or not
     *
     * @return boolean
     */
    public function getIsWysiwygEnabled()
    {
        return (bool)(Mage::getSingleton('cms/wysiwyg_config')->isEnabled() && $this->getEntityAttribute()->getIsWysiwygEnabled());
    }
}

