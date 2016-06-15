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

class Brander_AutoImport_Block_Adminhtml_Importgrid_Edit_Tab_Report extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('importgrid_');
        $form->setFieldNameSuffix('importgrid');
        $this->setForm($form);

        $fieldset = $form->addFieldset('report_form', array('legend' => Mage::helper('autoimport')->__('Import Report and Items Stat')));

        $formValues = Mage::registry('importgrid_data');
        if (json_decode($formValues->getLogFilename())) {

            $fileName = json_decode($formValues->getLogFilename());
            $path = Mage::helper('autoimport/report')->getReportFolder();

            if ($fileName) {
                $formValues->setFilenameLog($fileName);
                $form->setValues($formValues);
                $filepath = $path.DS.$fileName;

                $fieldset->addField('filename_log', 'text',array(
                        'label'    => Mage::helper('autoimport')->__('Log file'),
                        'name'     => 'filename_log',
                        'disabled' => true,
                        'readonly' => true,
                        'value'    => $fileName,
                        ));

                if (is_file($filepath) && is_readable($filepath)) {
                    $logInfo = file_get_contents($filepath);
                    $fieldset->addField('log', 'textarea', array(
                        'label'    => 'Log',
                        'required' => false,
                        'name'     => 'log',
                        'value'    => $logInfo,
                        'disabled' => true,
                        'readonly' => true,
                        'style'    => "width:1000px",
                    ));
                }
            }

            $statisticFiles = Mage::helper('autoimport/report')->getReportFilenames();
            foreach ($statisticFiles as $key => $statisticFile) {
                $file = Mage::helper('autoimport/report')->getReportFolder().DS.$statisticFile;
                if (is_file($file) && is_readable($file)) {

                    $secretKey = Mage::getSingleton('adminhtml/url')->getSecretKey("autoimport_importgrid/loadfile/");
                    $url = Mage::helper("adminhtml")->getUrl('*/*/loadfile', array('id' => $formValues->getEntityId(), 'file' => $statisticFile, 'key' => $secretKey));
                    //$url = Mage::getUrl('*/*/loadfile');
                    $button = $fieldset->addField($statisticFile, 'button', array(
                        'value'     => Mage::helper('autoimport')->__('Download %s', $file),
                        'onclick'   => 'setLocation(\' '  . $url . '\')',
                        'class'     => 'my_dowload_button',
                        'label'     => $statisticFile . ' '. Mage::helper('autoimport')->__('Items Details'),
                    ));

/*                    $button = $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(
                            array(
                                'label'   => $file,
                                'onclick' => 'return false;',
                                'class'   => 'some-class',
                            )
                        );
                    $button->setName($file);
                    $fieldset->setHeaderBar($button->toHtml());*/

                    $button->setAfterElementHtml('
                        <script type="text/javascript">
                            function loadfile() {
                                jQuery.ajax({
                                    url: \''.$url.'\',
                                    //data: {\'id\':\''.$formValues->getEntityId().'\',\'file\':\''.$statisticFile.'\',\'key\':\''.$key.'\'},
                                    dataType: \'HTML\',
                                    complete : function(){
                                        //alert(this.url)
                                    },
                                    success: function(xml){

                                    }
                                });
                            }
                        </script>
		            ');

                }
            }
        }
        return parent::_prepareForm();
    }

}