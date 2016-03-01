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
Mage::getModel('brander_unitopblog/postscategory')
    ->load(1)
    ->setParentId(0)
    ->setPath(1)
    ->setLevel(0)
    ->setPosition(0)
    ->setChildrenCount(0)
    ->setTitle('ROOT')
    ->setInitialSetupFlag(true)
    ->setAttributeSetId(Mage::getModel('brander_unitopblog/postscategory')->getDefaultAttributeSetId())
    ->save();
