<?php
/**
 * Brander CmsHome extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        CmsHome
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_CmsHome_Block_Adminhtml_Specialoffers extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'brander_cmshome';
        $this->_controller = 'adminhtml_specialoffers';
        parent::__construct();
        $this->_headerText = Mage::helper('brander_cmshome')->__('Special Offers Products');

        parent::__construct();
        $this->_removeButton('add');
        $this->setTemplate('brander_cmshome/grid.phtml');
    }
}
