<?php
class Brander_AdminForms_Block_Adminhtml_Helper_Form_File extends Varien_Data_Form_Element_File
{

    public function getElementHtml()
    {
        $this->setClass('input-file');
        $html = parent::getElementHtml();

        if ($this->getValue()) {
            $url = $this->_getUrl();

            if( !preg_match("#^(http://|https://)#", $url) ) {
                $url = Mage::getBaseUrl('media') . 'adminforms/' . $this->getEntityAttribute()->getEntity()->getType() . $url;
            }
            $html .= '<p class="note"><span>Uploaded file:</span> <a href="'.$url.'">'.basename($this->getValue()).'</a></p>';

        }
        $html.= $this->_getDeleteCheckbox();

        return $html;
    }

    protected function _getDeleteCheckbox()
    {
        $html = '';
        if ($this->getValue()) {
            $html .= '<span class="delete-image">';
            $html .= '<input type="checkbox" name="'.parent::getName().'[delete]" value="1" class="checkbox" id="'.$this->getHtmlId().'_delete"'.($this->getDisabled() ? ' disabled="disabled"': '').'/>';
            $html .= '<label for="'.$this->getHtmlId().'_delete"'.($this->getDisabled() ? ' class="disabled"' : '').'> Delete File</label>';
            $html .= $this->_getHiddenInput();
            $html .= '</span>';
        }

        return $html;
    }

    protected function _getHiddenInput()
    {
        return '<input type="hidden" name="'.parent::getName().'[value]" value="'.$this->getValue().'" />';
    }

    protected function _getUrl()
    {
        return $this->getValue();
    }

    public function getName()
    {
        return  $this->getData('name');
    }

}
