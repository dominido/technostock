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
class Brander_UnitopBlog_Block_Adminhtml_Postscategory_Widget_Chooser extends Brander_UnitopBlog_Block_Adminhtml_Postscategory_Tree
{
    protected $_selectedPostscategories = array();

    /**
     * Block construction
     * Defines tree template and init tree params
     *
     * @access public
     * @return void

     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('brander_unitopblog/postscategory/widget/tree.phtml');
    }

    /**
     * Setter
     *
     * @access public
     * @param array $selectedPostscategories
     * @return Brander_UnitopBlog_Block_Adminhtml_Postscategory_Widget_Chooser

     */
    public function setSelectedPostscategories($selectedPostscategories)
    {
        $this->_selectedPostscategories = $selectedPostscategories;
        return $this;
    }

    /**
     * Getter
     *
     * @access public
     * @return array

     */
    public function getSelectedPostscategories()
    {
        return $this->_selectedPostscategories;
    }

    /**
     * Prepare chooser element HTML
     *
     * @access public
     * @param Varien_Data_Form_Element_Abstract $element Form Element
     * @return Varien_Data_Form_Element_Abstract

     */
    public function prepareElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $uniqId = Mage::helper('core')->uniqHash($element->getId());
        $sourceUrl = $this->getUrl(
            '*/unitopblog_postscategory_widget/chooser',
            array('uniq_id' => $uniqId, 'use_massaction' => false)
        );
        $chooser = $this->getLayout()->createBlock('widget/adminhtml_widget_chooser')
            ->setElement($element)
            ->setTranslationHelper($this->getTranslationHelper())
            ->setConfig($this->getConfig())
            ->setFieldsetId($this->getFieldsetId())
            ->setSourceUrl($sourceUrl)
            ->setUniqId($uniqId);
        $value = $element->getValue();
        $postscategoryId = false;
        if ($value) {
            $postscategoryId = $value;
        }
        if ($postscategoryId) {
            $label = Mage::getSingleton('brander_unitopblog/postscategory')->load($postscategoryId)
                ->getTitle();
            $chooser->setLabel($label);
        }
        $element->setData('after_element_html', $chooser->toHtml());
        return $element;
    }

    /**
     * onClick listener js function
     *
     * @access public
     * @return string

     */
    public function getNodeClickListener()
    {
        if ($this->getData('node_click_listener')) {
            return $this->getData('node_click_listener');
        }
        if ($this->getUseMassaction()) {
            $js = '
                function (node, e) {
                    if (node.ui.toggleCheck) {
                        node.ui.toggleCheck(true);
                    }
                }
            ';
        } else {
            $chooserJsObject = $this->getId();
            $js = '
                function (node, e) {
                    '.$chooserJsObject.'.setElementValue(node.attributes.id);
                    '.$chooserJsObject.'.setElementLabel(node.text);
                    '.$chooserJsObject.'.close();
                }
            ';
        }
        return $js;
    }

    /**
     * Get JSON of a tree node or an associative array
     *
     * @access protected
     * @param Varien_Data_Tree_Node|array $node
     * @param int $level
     * @return string

     */
    protected function _getNodeJson($node, $level = 0)
    {
        $item = parent::_getNodeJson($node, $level);
        if (in_array($node->getId(), $this->getSelectedPostscategories())) {
            $item['checked'] = true;
        }
        return $item;
    }

    /**
     * Tree JSON source URL
     *
     * @access public
     * @param mixed $expanded
     * @return string

     */
    public function getLoadTreeUrl($expanded=null)
    {
        return $this->getUrl(
            '*/unitopblog_postscategory_widget/postscategoriesJson',
            array(
                '_current'=>true,
                'uniq_id' => $this->getId(),
                'use_massaction' => $this->getUseMassaction()
            )
        );
    }
}
