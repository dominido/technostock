<?php

class Brander_AdminForms_Model_Entity_Image extends Mage_Catalog_Model_Product_Image
{

    const PATH_MEDIA                = '%2$s%1$s%3$s';
    const PATH_PLACEHOLDER_MEDIA    = '%1$splaceholder%1$s%2$s';
    const PATH_PLACEHOLDER_SKIN     = '%1$simages%1$scatalog%1$s%2$s%1$splaceholder%1$s%3$s.jpg';

    const CONFIG_PLACEHOLDER = 'catalog/placeholder/%s_placeholder';

    protected $_entity = 'adminforms';
    protected $_subDir = 'small_image';

    public function getMediaPath(){
        return sprintf(self::PATH_MEDIA, DS, Mage::getBaseDir('media'), $this->getEntity());
    }

    public function getSkinPath(array $params=array()){
        return Mage::getDesign()->getSkinBaseDir($params);
    }

    public function getConfigPlaceHolder(){
        $file = Mage::getStoreConfig(sprintf(self::CONFIG_PLACEHOLDER, $this->getSubDir()));
        return $file ? sprintf(self::PATH_PLACEHOLDER_MEDIA, DS, $file) : false;
    }

    public function getSkinPlaceholder(){
        return sprintf(self::PATH_PLACEHOLDER_SKIN, DS, $this->getEntity(), $this->getSubDir());
    }

    public function getSubDir(){
        return $this->_subDir;
    }

    public function setSubDir($subDir){
        $this->_subDir = $subDir;
        return $this;
    }

    public function getEntity(){
        return $this->_entity;
    }

    public function setEntity($entity){
        $this->_entity = $entity;
        return $this;
    }

    public function setBaseFile($file){
        $file       = $this->_prepareFile($file);
        $baseFile   = $this->getMediaPath().$file;

        // File doesn't exist or is too large so try and get the placeholder image
        if(!file_exists($baseFile) || !$this->_checkMemory($baseFile)) {

            $phConfig   = $this->getConfigPlaceholder();
            $phSkin     = $this->getSkinPlaceholder();

            // Set config placeholder
            if($this->_validateFile($this->getMediaPath(), $phConfig)) {

                $file   = $phConfig;
                $path   = $this->getMediaPath();
            }
            // Set current theme skin placeholder
            elseif($this->_validateFile($this->getSkinPath(), $phSkin)) {

                $file   = $phSkin;
                $path   = $this->getSkinPath();
            }
            // Set default theme skin placeholder
            elseif($this->_validateFile($this->getSkinPath(array('_theme' => 'default')), $phSkin)) {

                $file   = $phSkin;
                $path   = $this->getSkinPath(array('_theme' => 'default'));
            }
            // Could not load any placeholder images
            else {
                throw new Exception('Could not load image file');
            }

            // Set the base file
            $baseFile = $path.DS.$file;
        }

        // Set file paths
        $this->_baseFile    = $baseFile;
        $this->_newFile     = $this->_getSavePath().$file;

        return $this;
    }

    protected function _validateFile($path, $file){
        return ($file && file_exists($path.$file));
    }

    protected function _prepareFile($file){
        if(!$file || 'no_selection' == $file) {
            $file = 'placeholder.jpg';
        }
        return DS.ltrim($file, DS);
    }

    protected function _getSavePath(){
        // Default params
        $path = array($this->getMediaPath(), 'cache', Mage::app()->getStore()->getId(), $path[] = $this->getSubDir());

        // Width/height
        if(!empty($this->_width) || !empty($this->_height)) {
            $path[] = $this->_width.'x'.$this->_height;
        }

        $hashParams = array(
            ($this->_keepAspectRatio    ? '' : 'non').'proportional',
            ($this->_keepFrame          ? '' : 'no').'frame',
            ($this->_keepTransparency   ? '' : 'no').'transparency',
            ($this->_constrainOnly      ? 'do' : 'not').'constrainonly',
            $this->_rgbToString($this->_backgroundColor),
            'angle'.$this->_angle
        );

        // Attribute hash
        $path[] = md5(implode('_', $hashParams));

        return implode('/', $path);
    }

    protected function _rgbToString($rgbArray){
        $result = array();
        foreach ($rgbArray as $value){
            if(null === $value) {
                $result[] = 'null';
                continue;
            }
            $result[] = sprintf('%02s', dechex($value));
        }
        return implode($result);
    }

    public function clearCache(){
        $directory  = $this->getMediaPath().DS.'cache'.DS;
        $io         = new Varien_Io_File();
        $io->rmdir($directory, true);
        return $this;
    }

    public function getImageProcessor()
    {
        if( !$this->_processor ) {
            $this->_processor = new Brander_AdminForms_Model_Varien_Image($this->getBaseFile());
        }
        $this->_processor->keepAspectRatio($this->_keepAspectRatio);
        $this->_processor->keepFrame($this->_keepFrame);
        $this->_processor->keepTransparency($this->_keepTransparency);
        $this->_processor->constrainOnly($this->_constrainOnly);
        $this->_processor->backgroundColor($this->_backgroundColor);
        $this->_processor->quality($this->_quality);
        return $this->_processor;
    }

    public function adaptiveResize()
    {
        if (is_null($this->getWidth()) && is_null($this->getHeight())) {
            return $this;
        }
        
        $this->getImageProcessor()->adaptiveResize($this->_width, $this->_height);
        return $this;
    }
}
