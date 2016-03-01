<?php
/**
 * Brander MarketExport extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        MarketExport
 * @copyright      Copyright (c) 2014
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_MarketExport_GetController extends Mage_Core_Controller_Front_Action
{

    public function fileAction()
    {
        // Prepared vars
        $exportId = $this->getRequest()->getParam('id');

        // Logic
        $exports = Mage::getResourceModel('marketexport/export_collection')
            ->addFieldToFilter('path', $exportId)
            ->addFieldToFilter('is_active', 1);

        try {
            if (!count($exports)) {
                throw new Exception('Export not found');
            }

            /** @var $export Brander_MarketExport_Model_Export */
            $export = $exports->getFirstItem();
            $fileId = $export->getData('entity_id');
            $exportDir = Mage::helper('marketexport')->getFullExportDir();
            $exportFile = $exportDir . '/' . $fileId . '.xml';

            if (!file_exists($exportFile)){
                throw new Exception('Export file not found "' . $exportFile . '"');
            }
            $content = file_get_contents($exportFile);
            $this->prepareFileResponse('text/xml; charset=windows-1251', $content);
        }
        catch(Exception $e){
            $this->getResponse()->setHttpResponseCode(404);
        }
    }


    protected function prepareFileResponse($contentType, $content)
    {
        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Content-type', $contentType, true)
            ->setHeader('Last-Modified', date('r'));

        if (!is_null($content)) {
            $this->getResponse()->setBody($content);
        }
        return $this;
    }

}