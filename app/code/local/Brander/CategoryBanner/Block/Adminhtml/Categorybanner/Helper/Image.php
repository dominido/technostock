<?php
/**
 * {{Brander}}_CategoryBanner extension
 */
/**
 * Category Image image field renderer helper
 *
 * @category    Brander
 * @package     Brander_CategoryBanner
 * @author      Ultimate Module Creator
 */
class Brander_CategoryBanner_Block_Adminhtml_Categorybanner_Helper_Image extends Varien_Data_Form_Element_Image
{
    /**
     * get the url of the image
     *
     * @access protected
     * @return string
     * @author Ultimate Module Creator
     */
    protected function _getUrl()
    {
        $url = false;
        if ($this->getValue()) {
            $url = Mage::helper('brander_categorybanner/categorybanner_image')->getImageBaseUrl().
                $this->getValue();
        }
        return $url;
    }
}
