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
class Brander_UnitopBlog_Block_Adminhtml_Postscategory_Edit_Form extends Brander_UnitopBlog_Block_Adminhtml_Postscategory_Abstract
{
    /**
     * Additional buttons on post category page
     * @var array
     */
    protected $_additionalButtons = array();
    /**
     * constructor
     *
     * set template
     * @access public

     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('brander_unitopblog/postscategory/edit/form.phtml');
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Brander_UnitopBlog_Block_Adminhtml_Postscategory_Edit_Form

     */
    protected function _prepareLayout()
    {
        $postscategory = $this->getPostscategory();
        $postscategoryId = (int)$postscategory->getId();
        $this->setChild(
            'tabs',
            $this->getLayout()->createBlock('brander_unitopblog/adminhtml_postscategory_edit_tabs', 'tabs')
        );
        $this->setChild(
            'save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(
                    array(
                        'label'   => Mage::helper('brander_unitopblog')->__('Save Post Category'),
                        'onclick' => "postscategorySubmit('" . $this->getSaveUrl() . "', true)",
                        'class'   => 'save'
                    )
                )
        );
        // Delete button
        if (!in_array($postscategoryId, $this->getRootIds())) {
            $this->setChild(
                'delete_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(
                        array(
                            'label'   => Mage::helper('brander_unitopblog')->__('Delete Post Category'),
                            'onclick' => "postscategoryDelete('" . $this->getUrl(
                                '*/*/delete',
                                array('_current' => true)
                            )
                            . "', true, {$postscategoryId})",
                            'class'   => 'delete'
                        )
                    )
            );
        }

        // Reset button
        $resetPath = $postscategory ? '*/*/edit' : '*/*/add';
        $this->setChild(
            'reset_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(
                    array(
                        'label' => Mage::helper('brander_unitopblog')->__('Reset'),
                        'onclick'   => "postscategoryReset('".$this->getUrl(
                            $resetPath,
                            array('_current'=>true)
                        )
                        ."',true)"
                    )
                )
        );
        return parent::_prepareLayout();
    }

    /**
     * get html for delete button
     *
     * @access public
     * @return string

     */
    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    /**
     * get html for save button
     *
     * @access public
     * @return string

     */
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    /**
     * get html for reset button
     *
     * @access public
     * @return string

     */
    public function getResetButtonHtml()
    {
        return $this->getChildHtml('reset_button');
    }

    /**
     * Retrieve additional buttons html
     *
     * @access public
     * @return string

     */
    public function getAdditionalButtonsHtml()
    {
        $html = '';
        foreach ($this->_additionalButtons as $childName) {
            $html .= $this->getChildHtml($childName);
        }
        return $html;
    }

    /**
     * Add additional button
     *
     * @param string $alias
     * @param array $config
     * @return Brander_UnitopBlog_Block_Adminhtml_Postscategory_Edit_Form

     */
    public function addAdditionalButton($alias, $config)
    {
        if (isset($config['name'])) {
            $config['element_name'] = $config['name'];
        }
        $this->setChild(
            $alias . '_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')->addData($config)
        );
        $this->_additionalButtons[$alias] = $alias . '_button';
        return $this;
    }

    /**
     * Remove additional button
     *
     * @access public
     * @param string $alias
     * @return Brander_UnitopBlog_Block_Adminhtml_Postscategory_Edit_Form

     */
    public function removeAdditionalButton($alias)
    {
        if (isset($this->_additionalButtons[$alias])) {
            $this->unsetChild($this->_additionalButtons[$alias]);
            unset($this->_additionalButtons[$alias]);
        }
        return $this;
    }

    /**
     * get html for tabs
     *
     * @access public
     * @return string

     */
    public function getTabsHtml()
    {
        return $this->getChildHtml('tabs');
    }

    /**
     * get the form header
     *
     * @access public
     * @return string

     */
    public function getHeader()
    {
        if ($this->getPostscategoryId()) {
            return $this->getPostscategoryTitle();
        } else {
            return Mage::helper('brander_unitopblog')->__('New Root Post Category');
        }
    }

    /**
     * get the delete url
     *
     * @access public
     * @param array $args
     * @return string

     */
    public function getDeleteUrl(array $args = array())
    {
        $params = array('_current'=>true);
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/delete', $params);
    }

    /**
     * Return URL for refresh input element 'path' in form
     *
     * @access public
     * @param array $args
     * @return string

     */
    public function getRefreshPathUrl(array $args = array())
    {
        $params = array('_current'=>true);
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/refreshPath', $params);
    }

    /**
     * check if request is ajax
     *
     * @access public
     * @return bool

     */
    public function isAjax()
    {
        return Mage::app()->getRequest()->isXmlHttpRequest() || Mage::app()->getRequest()->getParam('isAjax');
    }
}
