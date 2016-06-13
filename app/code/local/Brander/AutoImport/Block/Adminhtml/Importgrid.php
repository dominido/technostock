<?php

/**
 * Brander AutoImport extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        AutoImport
 * @copyright      Copyright (c) 2014-2016
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */

class Brander_AutoImport_Block_Adminhtml_Importgrid extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct(){

		$this->_controller 		= 'adminhtml_importgrid';
		$this->_blockGroup 		= 'autoimport';
		$this->_headerText 		= Mage::helper('autoimport')->__('Import process report');

        $url = Mage::helper("adminhtml")->getUrl('*/*/import');
/*        $run = $this->_addButton('run_import', array(
            'label'   => Mage::helper('catalog')->__('Run Import'),
            'onclick' => "javascript:runimport('".$url."');return false;",
            'class'   => 'run'
        ));*/


		parent::__construct();
	}

}