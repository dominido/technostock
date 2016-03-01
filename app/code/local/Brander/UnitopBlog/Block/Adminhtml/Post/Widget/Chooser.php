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
class Brander_UnitopBlog_Block_Adminhtml_Post_Widget_Chooser extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Block construction, prepare grid params
     *
     * @access public
     * @param array $arguments Object data
     * @return void

     */
    public function __construct($arguments=array())
    {
        parent::__construct($arguments);
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setDefaultFilter(array('chooser_status' => '1'));
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
            'brander_unitopblog/adminhtml_unitopblog_post_widget/chooser',
            array('uniq_id' => $uniqId)
        );
        $chooser = $this->getLayout()->createBlock('widget/adminhtml_widget_chooser')
            ->setElement($element)
            ->setTranslationHelper($this->getTranslationHelper())
            ->setConfig($this->getConfig())
            ->setFieldsetId($this->getFieldsetId())
            ->setSourceUrl($sourceUrl)
            ->setUniqId($uniqId);
        if ($element->getValue()) {
            $post = Mage::getModel('brander_unitopblog/post')->load($element->getValue());
            if ($post->getId()) {
                $chooser->setLabel($post->getTitle());
            }
        }
        $element->setData('after_element_html', $chooser->toHtml());
        return $element;
    }

    /**
     * Grid Row JS Callback
     *
     * @access public
     * @return string

     */
    public function getRowClickCallback()
    {
        $chooserJsObject = $this->getId();
        $js = '
            function (grid, event) {
                var trElement = Event.findElement(event, "tr");
                var postId = trElement.down("td").innerHTML.replace(/^\s+|\s+$/g,"");
                var postTitle = trElement.down("td").next().innerHTML;
                '.$chooserJsObject.'.setElementValue(postId);
                '.$chooserJsObject.'.setElementLabel(postTitle);
                '.$chooserJsObject.'.close();
            }
        ';
        return $js;
    }

    /**
     * Prepare a static blocks collection
     *
     * @access protected
     * @return Brander_UnitopBlog_Block_Adminhtml_Post_Widget_Chooser

     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('brander_unitopblog/post')->getCollection();
        $collection->addAttributeToSelect('title');
        $collection->addAttributeToSelect('status');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns for the a grid
     *
     * @access protected
     * @return Brander_UnitopBlog_Block_Adminhtml_Post_Widget_Chooser

     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'chooser_id',
            array(
                'header' => Mage::helper('brander_unitopblog')->__('Id'),
                'align'  => 'right',
                'index'  => 'entity_id',
                'type'   => 'number',
                'width'  => 50
            )
        );

        $this->addColumn(
            'chooser_title',
            array(
                'header' => Mage::helper('brander_unitopblog')->__('Title'),
                'align'  => 'left',
                'index'  => 'title',
            )
        );
        $this->addColumn(
            'chooser_status',
            array(
                'header'  => Mage::helper('brander_unitopblog')->__('Status'),
                'index'   => 'status',
                'type'    => 'options',
                'options' => array(
                    0 => Mage::helper('brander_unitopblog')->__('Disabled'),
                    1 => Mage::helper('brander_unitopblog')->__('Enabled')
                ),
            )
        );
        return parent::_prepareColumns();
    }

    /**
     * get url for grid
     *
     * @access public
     * @return string

     */
    public function getGridUrl()
    {
        return $this->getUrl(
            'adminhtml/unitopblog_post_widget/chooser',
            array('_current' => true)
        );
    }
}
