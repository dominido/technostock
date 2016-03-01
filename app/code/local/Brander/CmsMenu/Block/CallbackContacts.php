<?php
/**
 * Brander CmsMenu extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        CmsMenu
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_CmsMenu_Block_CallbackContacts extends Mage_Core_Block_Text_List
{

    /**
     * prepare the layout
     *
     * @access protected
     * @return Brander_UnitopBlog_Block_Post_List

     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $alias = $this->getNameInLayout();

        $positionId = Mage::helper('brander_cmsmenu')->getSiteCfg('customercallbacks/settings/header_position');
        $position = Mage::getModel('brander_ourcontacts/config_source_blockpositions')->getPositionAlias($positionId);
        if ($position == $alias) {
            //$class = $this->getLayout()->getUpdate()->getElementClass();
            $callback = $this->getLayout()->getBlock('header_contacts_callback');
            $this->append($callback);
        }

        return $this;
    }
}