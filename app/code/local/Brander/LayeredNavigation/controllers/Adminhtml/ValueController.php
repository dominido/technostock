<?php

class Brander_LayeredNavigation_Adminhtml_ValueController extends Mage_Adminhtml_Controller_Action
{
    // edit filters (uses tabs)
    public function editAction() 
    {
        $id     = (int) $this->getRequest()->getParam('id');
        /** @var Brander_LayeredNavigation_Model_Value $model */
        $model  = Mage::getModel('brander_layerednavigation/value')->load($id);

        if ($id && !$model->getId()) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('brander_layerednavigation')->__('Option does not exist'));
            $this->_redirect('*/adminhtml_filter/index');
            return;
        }
        
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        // todo: save images
        
        Mage::register('brander_layerednavigation_value', $model);

        $this->loadLayout();
        
        $this->_setActiveMenu('catalog/layerednavigation');
        $this->_addContent($this->getLayout()->createBlock('brander_layerednavigation/adminhtml_value_edit'));

        $this->_title($model->getCurrentTitle() . $this->__(' Settings'));

        $this->renderLayout();
    }

    public function saveAction() 
    {
        $id     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('brander_layerednavigation/value')
                   ->load($id);
        $filterId = $model->getFilterId();
                   
        $data = $this->getRequest()->getPost();
        if (isset($data['multistore'])){
            foreach ($data['multistore'] as $key=>$value){
                $data[$key] = serialize($value);
            }
        }
        if (!$data) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('brander_layerednavigation')->__('Unable to find an option to save'));
            $this->_redirect('*/adminhtml_filter/');
        }
        
        //upload images
        $path = Mage::getBaseDir('media') . DS . 'brander/layerednavigation' . DS;
        $imagesTypes = array('big', 'small', 'medium', 'small_hover');
        foreach ($imagesTypes as $type){
            $field = 'img_' . $type;
            
            $isRemove = isset($data['remove_' . $field]);
            $hasNew   = !empty($_FILES[$field]['name']);
            
            try {
                // remove the old file
                if ($isRemove || $hasNew){
                    $oldName = $model->getData($field);
                    if ($oldName){
                         @unlink($path . $oldName);
                         $data[$field] = '';
                    }
                }
    
                // upload a new if any
                if (!$isRemove && $hasNew){
                    $newName = $type . $id;
                    $newName .= '.' . strtolower(substr(strrchr($_FILES[$field]['name'], '.'), 1)); 
               
                    $uploader = new Varien_File_Uploader($field);
                    $uploader->setFilesDispersion(false);
                    $uploader->setAllowRenameFiles(false);
                       $uploader->setAllowedExtensions(array('png','gif', 'jpg', 'jpeg'));
                    $uploader->save($path, $newName);    
                     
                    $data[$field] = $newName;            
                }   
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());    
            }
        }
        
        try {
            $model->setData($data)->setId($id);
            
            $model->save();
            Mage::getSingleton('adminhtml/session')->setFormData(false);
            
            $msg = Mage::helper('brander_layerednavigation')->__('Option properties have been successfully saved');
            Mage::getSingleton('adminhtml/session')->addSuccess($msg);

            if ($this->getRequest()->getParam('continue')){
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
            }
            else {
                $this->_redirect('*/adminhtml_filter/edit', array('id'=>$filterId, 'tab'=>'values'));
            }

        } 
        catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            Mage::getSingleton('adminhtml/session')->setFormData($data);
            $this->_redirect('*/*/edit', array('id' => $id));
        }

        $this->invalidateCache();
    }

    protected function invalidateCache()
    {
        /** @var Brander_LayeredNavigation_Helper_Data $helper */
        $helper = Mage::helper('brander_layerednavigation');
        $helper->invalidateCache();
    }

}