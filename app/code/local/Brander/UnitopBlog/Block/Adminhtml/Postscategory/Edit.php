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
class Brander_UnitopBlog_Block_Adminhtml_Postscategory_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * constructor
     *
     * @access public
     * @return void

     */
    public function __construct()
    {
        $this->_objectId    = 'entity_id';
        $this->_blockGroup  = 'brander_unitopblog';
        $this->_controller  = 'adminhtml_postscategory';
        $this->_mode        = 'edit';
        parent::__construct();
        $this->setTemplate('brander_unitopblog/postscategory/edit.phtml');
    }
}

