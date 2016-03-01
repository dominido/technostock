<?php
/**
 * Brander CmsPages extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        CmsPages
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_CmsPages_Model_Config_Source_Robots extends Brander_AdminForms_Model_Config_Source_Options
{
    /**
     * @return array
     *      array(
     *          array('value' => value, 'label' => label)
     *      )
     */
    public function toOptionArray()
    {
        return array(
            array('value'=>'INDEX,FOLLOW', 'label'=>'INDEX, FOLLOW'),
            array('value'=>'NOINDEX,FOLLOW', 'label'=>'NOINDEX, FOLLOW'),
            array('value'=>'INDEX,NOFOLLOW', 'label'=>'INDEX, NOFOLLOW'),
            array('value'=>'NOINDEX,NOFOLLOW', 'label'=>'NOINDEX, NOFOLLOW'),
        );
    }
}
