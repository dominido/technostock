<?php
/**
 * Brander UnitopBlog extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        UnitopBlog
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_UnitopBlog_Helper_Post extends Mage_Core_Helper_Abstract
{

    /**
     * get the url to the posts list page
     *
     * @access public
     * @return string

     */
    public function getPostsUrl()
    {
        if ($listKey = Mage::getStoreConfig('brander_unitopblog/post/url_rewrite_list')) {
            return Mage::getUrl('', array('_direct'=>$listKey));
        }
        return Mage::getUrl('brander_unitopblog/post/index');
    }

    /**
     * check if breadcrumbs can be used
     *
     * @access public
     * @return bool

     */
    public function getUseBreadcrumbs()
    {
        return Mage::getStoreConfigFlag('brander_unitopblog/post/breadcrumbs');
    }

    /**
     * check if the rss for post is enabled
     *
     * @access public
     * @return bool

     */
    public function isRssEnabled()
    {
        return  Mage::getStoreConfigFlag('rss/config/active') &&
            Mage::getStoreConfigFlag('brander_unitopblog/post/rss');
    }

    /**
     * get the link to the post rss list
     *
     * @access public
     * @return string

     */
    public function getRssUrl()
    {
        return Mage::getUrl('brander_unitopblog/post/rss');
    }

    /**
     * get base files dir
     *
     * @access public
     * @return string

     */
    public function getFileBaseDir()
    {
        return Mage::getBaseDir('media').DS.'post'.DS.'file';
    }

    /**
     * get base file url
     *
     * @access public
     * @return string

     */
    public function getFileBaseUrl()
    {
        return Mage::getBaseUrl('media').'post'.'/'.'file';
    }

    /**
     * get post attribute source model
     *
     * @access public
     * @param string $inputType
     * @return mixed (string|null)

     */
     public function getAttributeSourceModelByInputType($inputType)
     {
         $inputTypes = $this->getAttributeInputTypes();
         if (!empty($inputTypes[$inputType]['source_model'])) {
             return $inputTypes[$inputType]['source_model'];
         }
         return null;
     }

    /**
     * get attribute input types
     *
     * @access public
     * @param string $inputType
     * @return array()

     */
    public function getAttributeInputTypes($inputType = null)
    {
        $inputTypes = array(
            'multiselect' => array(
                'backend_model' => 'eav/entity_attribute_backend_array'
            ),
            'boolean'     => array(
                'source_model'  => 'eav/entity_attribute_source_boolean'
            ),
            'file'          => array(
                'backend_model' => 'brander_unitopblog/post_attribute_backend_file'
            ),
            'image'          => array(
                'backend_model' => 'brander_unitopblog/post_attribute_backend_image'
            ),
        );

        if (is_null($inputType)) {
            return $inputTypes;
        } else if (isset($inputTypes[$inputType])) {
            return $inputTypes[$inputType];
        }
        return array();
    }

    /**
     * get post attribute backend model
     *
     * @access public
     * @param string $inputType
     * @return mixed (string|null)

     */
    public function getAttributeBackendModelByInputType($inputType)
    {
        $inputTypes = $this->getAttributeInputTypes();
        if (!empty($inputTypes[$inputType]['backend_model'])) {
            return $inputTypes[$inputType]['backend_model'];
        }
        return null;
    }

    /**
     * filter attribute content
     *
     * @access public
     * @param Brander_UnitopBlog_Model_Post $post
     * @param string $attributeHtml
     * @param string @attributeName
     * @return string

     */
    public function postAttribute($post, $attributeHtml, $attributeName)
    {
        $attribute = Mage::getSingleton('eav/config')->getAttribute(
            Brander_UnitopBlog_Model_Post::ENTITY,
            $attributeName
        );
        if ($attribute && $attribute->getId() && !$attribute->getIsWysiwygEnabled()) {
            if ($attribute->getFrontendInput() == 'textarea') {
                $attributeHtml = nl2br($attributeHtml);
            }
        }
        if ($attribute->getIsWysiwygEnabled()) {
            $attributeHtml = $this->_getTemplateProcessor()->filter($attributeHtml);
        }
        return $attributeHtml;
    }

    /**
     * get the template processor
     *
     * @access protected
     * @return Mage_Catalog_Model_Template_Filter

     */
    protected function _getTemplateProcessor()
    {
        if (null === $this->_templateProcessor) {
            $this->_templateProcessor = Mage::helper('catalog')->getPageTemplateProcessor();
        }
        return $this->_templateProcessor;
    }

    public function getVisibleAttributeList()
    {
        $attribute_list = array(
            'post_date' => $this->__('Post Date'),
            'manual_author_name' => $this->__('Author'),
        );
        return $attribute_list;
    }

    public function getAdditionInformation($item) {
        $attribute_list = $this->getVisibleAttributeList();

        $description = '<ul class="bullet">';

        $i = 0;
        foreach ($attribute_list as $_attribKey => $_attribName) {
            if ($item->getData($_attribKey)) {
                if ($_attribKey == 'post_date') {
                    $item->setData($_attribKey, Mage::helper('core')->formatDate($item->getData($_attribKey), 'medium', false));
                }
                $description .= '<li><b><span>' . $_attribName . ':</span></b> ';
                $description .= '<span>' . $item->getData($_attribKey) . '</span></li>';
            }

        }
        $description .= '</ul>';

        return $description;
    }
}
