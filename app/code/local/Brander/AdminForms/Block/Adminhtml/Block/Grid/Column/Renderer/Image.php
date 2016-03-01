<?php
class Brander_AdminForms_Block_Adminhtml_Block_Grid_Column_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $html = '<img alt=""';
		if ($this->getColumn()->getImageHeight())
			$html .= ' height="'.$this->getColumn()->getImageHeight().'"';
		if ($this->getColumn()->getImageWidth())
			$html .= ' width="'.$this->getColumn()->getImageWidth().'"';
        $html .= ' src="' . $row->getData($this->getColumn()->getIndex()) . '"/>';
        return $html;
    }
}
