<?php
/**
 * Brander_HotCategories extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Brander
 * @package        Brander_HotCategories
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Admin backend source model for files
 *
 * @category    Brander
 * @package     Brander_HotCategories
 * @author      AlexVegas (Brander)
 */
class Brander_HotCategories_Model_Hotcategory_Attribute_Backend_File extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{

    /**
     * Save uploaded file and set its name to hot category object
     *
     * @access public
     * @param Varien_Object $object
     * @return null

     */
    public function afterSave($object)
    {
        $value = $object->getData($this->getAttribute()->getName());
        if (is_array($value) && !empty($value['delete'])) {
            $object->setData($this->getAttribute()->getName(), '');
            $this->getAttribute()->getEntity()
                ->saveAttribute($object, $this->getAttribute()->getName());
            return;
        }

        $path = Mage::helper('brander_hotcategories/hotcategory')->getFileBaseDir();

        try {
            $uploader = new Varien_File_Uploader($this->getAttribute()->getName());
            //set allowed file extensions if you need
            //$uploader->setAllowedExtensions(array('mp4', 'mov', 'f4v', 'flv'));
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $result = $uploader->save($path);
            $object->setData($this->getAttribute()->getName(), $result['file']);
            $this->getAttribute()->getEntity()->saveAttribute($object, $this->getAttribute()->getName());
        } catch (Exception $e) {
            if ($e->getCode() != 666) {
                //throw $e;
            }
            return;
        }
    }
}
