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

class Brander_AutoImport_Adminhtml_Autoimport_ImportgridController extends Brander_AutoImport_Controller_Adminhtml_FileImport
{

/*    protected function _initImport()
    {
        $bannerId  = (int) $this->getRequest()->getParam('id');
        $banner    = Mage::getModel('selfservicebox_banners/banner');
        if ($bannerId) {
            $banner->load($bannerId);
        }
        Mage::register('current_banner', $banner);
        return $banner;
    }*/

    public function indexAction()
    {
        $this->loadLayout();
        $this->_title(Mage::helper('autoimport')->__('autoimport'))
            ->_title(Mage::helper('autoimport')->__('Import grid'));
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout()->renderLayout();
    }

    public function editAction() {
        $importgridId	= $this->getRequest()->getParam('id');
        $importgrid  	= $this->_initimportgrid();
        if ($importgridId && !$importgrid->getId()) {
            $this->_getSession()->addError(Mage::helper('autoimport')->__('This process no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $importgrid->setData($data);
        }
        $this->loadLayout();
        $this->_title(Mage::helper('autoimport')->__('autoimport'))
            ->_title(Mage::helper('autoimport')->__('Import grid'));
        if ($importgrid->getId()){
            $this->_title(Mage::helper('autoimport')->__('Edit import task'));
        }
        else{
            $this->_title(Mage::helper('autoimport')->__('Add import task'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_redirect('*/*/edit');
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('importgrid')) {
            try {
                $now = Mage::getSingleton('core/date')->gmtDate();
                $filegrid = $this->_initimportgrid();

                $currentTimezone = Mage::getStoreConfig('general/locale/timezone');
                $task_datetime = new Zend_Date();
                $task_datetime->setTimezone($currentTimezone);
                $task_datetime->set($data['planned_at']);
                $task_datetime->setTimezone('GMT');
                $gmt_date = $task_datetime->get( 'YYYY-MM-dd HH:mm' );
                $data['planned_at'] = $gmt_date;

                $filegrid->addData($data);

                if ($filegrid->getEntityId() == null && $data['file_type'] == '2') {
                    $file_csv_nameName = $this->_uploadAndGetName('import_filename', Mage::helper('autoimport')->getFileBaseDir('import'), $data);
                    $file_csv_nameReal = 'importdata.csv';
                    $filegrid->setData('import_filename', $file_csv_nameName);
                    $filegrid->setData('import_file_size', Mage::helper('autoimport')->formatSizeUnits(filesize(Mage::helper('autoimport')->getFileBaseUrl('import').$file_csv_nameReal)));
                    //$filegrid->setData('file_name', basename(Mage::helper('autoimport')->getFileBaseDir().'/'.$file_csv_nameReal));
                    $filegrid->setData('import_file_loadtime', $now);
                }

                $filegrid->setData('import_type', Brander_AutoImport_Model_Source_Importtype::IMPORT_TYPE_MANUAL_MODE);
                $filegrid->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('autoimport')->__('File Grid was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $filegrid->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            }
            catch (Mage_Core_Exception $e){
                Mage::logException($e);
                if (isset($data['import_filename']['value'])){
                    $data['import_filename'] = $data['import_filename']['value'];
                }
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
            catch (Exception $e) {
                Mage::logException($e);
                if (isset($data['file_csv_name']['value'])){
                    $data['file_csv_name'] = $data['file_csv_name']['value'];
                }
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('autoimport')->__('There was a problem saving the file grid.'));
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('autoimport')->__('Unable to find file grid to save.'));
        $this->_redirect('*/*/');

    }

    /*public function newAction() {
        $this->_forward('');
    }*/

    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $importgrid = Mage::getModel('autoimport/importgrid');
                $importgrid->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('autoimport')->__('process was successfully deleted.'));
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('autoimport')->__('There was an error deleteing process.'));
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('autoimport')->__('Could not find process to delete.'));
        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $importgridIds = $this->getRequest()->getParam('importgrid');
        if (!is_array($importgridIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('autoimport')->__('Please select process to delete.'));
        } else {
            try {
                foreach ($importgridIds as $importgridId) {
                    $importgrid = Mage::getModel('autoimport/importgrid');
                    $importgrid->setId($importgridId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('autoimport')->__('Total of %d process were successfully deleted.', count($importgridIds)));
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('autoimport')->__('There was an error deleteing process.'));
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    public function importAction()
    {
        Mage::getModel('autoimport/import')->cronImportStart();
        return true;
    }

    protected function _initimportgrid()
    {
        $importgridId = (int)$this->getRequest()->getParam('id');
        $importgrid = Mage::getModel('autoimport/importgrid');
        if ($importgridId) {
            $importgrid->load($importgridId);
        }
        Mage::register('importgrid_data', $importgrid);
        return $importgrid;
    }

    public function loadfileAction()
    {
        $params = $this->getRequest()->getParams();
        $importgrid = $this->_initimportgrid();
        $files = json_decode($importgrid->getLogFilename());
        $statisticFile = $files->$params['file'];
        $fileName = $params['file'] . '.xls';
        $content = file_get_contents($statisticFile);
        $this->_prepareDownloadResponse($fileName, $content);
        return '';
    }

}