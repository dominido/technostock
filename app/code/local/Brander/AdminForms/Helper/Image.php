<?php

class Brander_AdminForms_Helper_Image extends Mage_Core_Helper_Abstract
{
    protected $_model;
    protected $_placeholder = '';
    protected $_file;
    protected $_scheduleResize = false;
    protected $_scheduleAdaptiveResize = false;

    public function init($entity=null, $file, $subDir){
        // Set file in model
        $this->_reset();
        $this->setFile($file);

        if (!$entity){
            $entity = 'adminforms';
        } elseif ($entity instanceof Brander_AdminForms_Model_Block){
            $entity = 'adminforms'.DS.$entity->getEntityType();
        }

        $model = $this->_getModel()
            ->setEntity($entity)
            ->setSubDir($subDir);

        return $this;
    }

    protected function _reset(){
        $this->_model           = null;
        $this->_scheduleResize  = false;
        $this->_scheduleAdaptiveResize = false;
        $this->_placeholder     = '';
        return $this;
    }

    protected function _getModel(){
        if (!$this->_model){
            $this->_model = Mage::getModel('adminforms/entity_image');
        }
        return $this->_model;
    }

    public function setFile($file){
        $this->_file = $file;
        return $this;
    }

    public function getFile(){
        return $this->_file;
    }

    public function resize($width, $height = null)
    {
        $this->_getModel()
            ->setWidth($width)
            ->setHeight($height);
        $this->_scheduleResize = true;
        return $this;
    }

    /**
     * Crop image from center
     * see http://2ammedia.co.uk/web-design/magento-adaptive-resize-resize-to-best-fit
     * 
     * @param int $width
     * @param int|null $height
     * @return Brander_AdminForms_Helper_Image
     */
    public function adaptiveResize($width, $height = null)
    {
        $this->_getModel()->setWidth($width)->setHeight($height)/*->setConstrainOnly(true)*/->setKeepAspectRatio(true)->setKeepFrame(false);
        $this->_scheduleAdaptiveResize = true;
        return $this;
    }

    public function setPlaceholder($placeholder){
        $this->_placeholder = (string) $placeholder;
        return $this;
    }

    public function getPlaceholder(){
        if(!$this->_placeholder){
            $this->_placeholder = sprintf('images/catalog/category/placeholder/%s.jpg', $this->_getModel()->getSubDir());
        }
        return $this->_placeholder;
    }

    public function __toString(){
        try {
            $this->_getModel()
                ->setBaseFile($this->getFile());

            // Return cached image
            if ($this->_getModel()->isCached()) {
                return $this->_getModel()->getUrl();
            }

            // Reuse image
            if ($this->_scheduleResize) {
                $this->_getModel()->resize();
            }

            if ($this->_scheduleAdaptiveResize) {
                $this->_getModel()->adaptiveResize();
            }

            $url = $this->_getModel()->saveFile()->getUrl();
        } catch(Exception $e){
            $url = Mage::getDesign()->getSkinUrl($this->getPlaceholder());
        }
        return $url;
    }

}
