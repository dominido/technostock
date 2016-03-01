<?php
class Brander_AdminForms_Model_Block extends Brander_AdminForms_Model_Abstract
{
    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('adminforms/block');
    }

    public function getImageUrl($value)
    {
        return $value?Mage::getBaseUrl('media').'adminforms/'.$this->getEntityType(). ($value[0]==DS?$value:DS.$value):$value;
    }

    public function getImageFile($value, $rel=false)
    {
        $prefix = $rel ? '' : Mage::getBaseDir('media'). DS . 'adminforms/';
        return $value?$prefix.$this->getEntityType(). ($value[0]==DS?$value:DS.$value):$value;
    }

    public function getImageSize($value)
    {
        $file = $this->getImageFile($value);

        $size = array();
        if ($file) {
            $size = @getimagesize($file);
            if ($size) {
                $size = array(
                    'width' => $size[0],
                    'height' => $size[1]
                );
            }
        }

        return $size;
    }

    /*
     * Returns page attribute image object
     *
     * @return Brander_Adminform_Helper_Image
     */
    public function getImage($attribute)
    {
        $entity = 'adminforms' . DS . $this->getEntityType();
    
        return Mage::helper('adminforms/image')->init($entity, $this->getData($attribute), 'cache');
    }

    public function getImgUrl($attribute = 'image')
    {
        return $this->getImageUrl($this->getData($attribute));
    }

    public function getFileExt($attribute)
    {
        $value = $this->getData($attribute);
        return pathinfo($value, PATHINFO_EXTENSION);
    }

    public function getPreparedUrl($attribute = 'url', $empty = '#')
    {
        $url = $this->getData($attribute);
        if (empty($url)) {
            $url = $empty;
        } elseif (!preg_match('/^https?:/', $url)) {
            $url = trim($url, '/');
            $url = Mage::getUrl($url);
        }

        return $url;
    }

    public function getGridAttributes()
    {
        $entityInfo  = Mage::helper('adminforms')->getEntityInfo($this->getBlockKey());
        $gridOptions = isset($entityInfo['entity_grid_options']) ? unserialize($entityInfo['entity_grid_options']) : array();
    
        $fields = isset($gridOptions['fields']) ? $gridOptions['fields'] : array();

        $attributes = array();
        $eav = Mage::getModel('eav/entity')->setType($this->getEntityType())->loadAllAttributes();
        
        foreach ($fields as $code => $options) {
            $attribute = $eav->getAttribute($code);
            if (!$attribute) {
                continue;
            }

            if (!isset($options['header'])) {
                $options['header'] =  Mage::helper('adminforms')->__($attribute->getFrontendLabel());
            }

            $attribute->setGridOptions($options);
            
            $attributes[$code] = $attribute;
        }

        return $attributes;
    }
}
