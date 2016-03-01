<?php
class Brander_AdminForms_Model_Block_Attribute_Backend_Image extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{

    /**
     * Save uploaded file and set its name to block
     *
     * @param Varien_Object $object
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

        $path = Mage::getBaseDir('media') . DS . 'adminforms' . DS . 'block' . DS;

        try {
            $uploader = Mage::getModel('core/file_uploader', $this->getAttribute()->getName());
            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
            $uploader->setAllowRenameFiles(true);
            $uploader->save($path);

            $object->setData($this->getAttribute()->getName(), $uploader->getUploadedFileName());
            $this->getAttribute()->getEntity()->saveAttribute($object, $this->getAttribute()->getName());
        } catch (Exception $e) {
            Mage::logException($e);
            /** @TODO ??? */
            return;
        }
    }
}
