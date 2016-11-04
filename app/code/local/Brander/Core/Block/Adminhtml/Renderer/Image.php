<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 04.08.16
 * Time: 14:46
 */
class Brander_Core_Block_Adminhtml_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
    public function render(Varien_Object $row)
    {

        // get image path


        $label = $this->__('Have image');


        // if first load without filters

        $productId = $row->getData('entity_id');
        $product = Mage::getModel('catalog/product')->load($productId);
        $imagePath = $product->getImage();
        
        
        if ((is_null($imagePath)) || ( $imagePath == 'no_selection')) {

            $label = $this->__('No image');
        }

        $out = "<span>". $label ."</span>";
        return $out;
    }
}