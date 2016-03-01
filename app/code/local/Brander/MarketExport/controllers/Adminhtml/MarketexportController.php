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
class Brander_MarketExport_Adminhtml_MarketexportController extends Mage_Adminhtml_Controller_Action
{

    public function _init()
    {


        $id = $this->getRequest()->getParam('id', null);
        if($id != null){
            $item = Mage::getModel('marketexport/export')->load($id)->getData();

            if(!$item['min_price']){
                unset($item['min_price']);
            }
            if(!$item['max_price']){
                unset($item['max_price']);
            }
            if(!$item['rating']){
                unset($item['rating']);
            }else{
                $item['rating_check'] = 1;
            }

            Mage::register('export_item', $item);
        }
        $this->loadLayout();

        return $this;
    }

    public function indexAction()
    {       
        $this->loadLayout();
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_init();
        //$this->_addContent($this->getLayout()->createBlock('marketexport/adminhtml_edit_formfield'));
        $this->renderLayout();
    }

    public function editAction()
    {
        $this->_forward('new');
    }

    public function addAction()
    {
        $limit = ini_get('memory_limit');
        ini_set('memory_limit', '-1');

        $data = $this->getRequest()->getParams();

        if($data!=null){
            try{
                if(!Zend_Validate::is($data['name'], 'NotEmpty')){
                    throw new Exception;
                }
                if(!Zend_Validate::is($data['path'], 'NotEmpty')){
                    throw new Exception;
                }

/*                $date = $this->_filterDateTime(array(
                    'date'=>  Mage::app()->getLocale()->date()->__toString()),
                     array('date'));*/

                $currentTimestamp = Mage::getModel('core/date')->timestamp(time());
                $datetime = array('date'=>  date('Y-m-d H:i:s', $currentTimestamp));

                $export = Mage::getModel('marketexport/export');

                if($data['entity_id'] || $data['entity_id'] === 0){
                    $export->load($data['entity_id']);
                }else{
                    unset($data['entity_id']);
                    $data['created_at'] = $datetime['date'];
                    $export->setData('created_at', $datetime['date']);
                }

                $data['min_price'] = (intval($data['min_price'])) ? : 0;
                $data['max_price'] = (intval($data['max_price'])) ? : 0;


                if($data['min_price'] || $data['max_price']){
                    $export->initPriceFilter($data['min_price'], $data['max_price']);
                }

                $data['updated_at'] = $datetime['date'];
                $data = Mage::getModel('marketexport/export')->prepareData($data);

                $data['path'] = Mage::helper('marketexport')->generateOptionCode($data['path']);
                $export->setData($data);
                if (isset($data['categories'])) {
                    $export->setCategories($data['categories']);
                } else {
                    $export->setData('categories', null);
                }

                if (isset($data['stores'])) {
                    $export->setStores($data['stores']);
                } else {
                    $export->setData('stores', null);
                }

                if (isset($data['custom_attributes']) && $data['custom_attributes'] == "1") {
                    $export->setCustomAttributesData($data['custom_attributes_data']['map']['product']);
                }
                else ($export->setCustomAttributesData(0));


                /*                if (isset($data['delivery_options'])) {
                                    $export->setDeliveryOptions($data['delivery_options']);
                                } else {
                                    $export->setData('delivery_options', null);
                                }*/
                $export->save();

                Mage::getModel('marketexport/observer')->exportCurrent($export);

                $this->_getSession()->addSuccess(Mage::helper('marketexport')->__('URL: %s', Mage::helper('marketexport')->getExportUrl($data['path'])));
                $this->_redirect('*/*/');
            }catch(Exception $e){
                $this->_getSession()->addError(Mage::helper('marketexport')->__('File was not create'));
                $this->_redirect('*/*/');
            }
        }else{
            $this->_getSession()->addError(Mage::helper('marketexport')->__('File was not create'));
            $this->_redirect('*/*/');
        }
        ini_set('memory_limit', $limit);
    }

    public function updateAction()
    {
        $id = $this->getRequest()->getParam('id', null);

        if($id!=null){
            try{
                $export = Mage::getModel('marketexport/export');
                $item = $export->load($id);

                $currentTimestamp = Mage::getModel('core/date')->timestamp(time());
                $datetime = array('date'=>  date('Y-m-d H:i:s', $currentTimestamp));

                if(($item->getMinPrice()!=null && $item->getMaxPrice()!=null)
                        && ($item->getMinPrice()!=0 && $item->getMaxPrice()!=0)){
                    $export->initPriceFilter($item->getMinPrice(), $item->getMaxPrice());
                }

                $item->setPath(Mage::helper('marketexport')->generateOptionCode($item->getPath()));

                $export->setUpdatedAt($datetime['date'])->save();

                Mage::getModel('marketexport/observer')->exportCurrent($export);

                $this->_getSession()->addSuccess(Mage::helper('marketexport')->__('File URL: %s', Mage::helper('marketexport')->getExportUrl($item->getPath())));
                $this->_redirect('*/*/');
            }catch(Exception $e){
                $this->_getSession()->addError(Mage::helper('marketexport')->__('File was not update'));
                $this->_redirect('*/*/');
            }
        }else{
            $this->_getSession()->addError(Mage::helper('marketexport')->__('File was not update'));
            $this->_redirect('*/*/');
        }
    }

    public function activeAction()
    {
        $id = $this->getRequest()->getParam('id', null);

        if($id!=null){
            try{
                $export = Mage::getModel('marketexport/export');
                $export->load($id)->setIsActive(1)->save();

                $this->_getSession()->addSuccess(Mage::helper('marketexport')->__('Файл экспорта активирован'));
                $this->_redirect('*/*/');
            }catch(Exception $e){
                $this->_getSession()->addError(Mage::helper('marketexport')->__('Error! Activity status remains the same export file'));
                $this->_redirect('*/*/');
            }
        }else{
            $this->_getSession()->addError(Mage::helper('marketexport')->__('Error! Activity status remains the same export file'));
            $this->_redirect('*/*/');
        }
    }

    public function disactiveAction()
    {
        $id = $this->getRequest()->getParam('id', null);

        if($id!=null){
            try{
                $export = Mage::getModel('marketexport/export');
                $export->load($id)->setIsActive(0)->save();

                $this->_getSession()->addSuccess(Mage::helper('marketexport')->__('Файл экспорта деактивирован'));
                $this->_redirect('*/*/');
            }catch(Exception $e){
                $this->_getSession()->addError(Mage::helper('marketexport')->__('Error! Activity status remains the same export file'));
                $this->_redirect('*/*/');
            }
        }else{
            $this->_getSession()->addError(Mage::helper('marketexport')->__('Error! Activity status remains the same export file'));
            $this->_redirect('*/*/');
        }
    }

    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id', null);

        if($id!=null){
            try{
                $export = Mage::getModel('marketexport/export');
                $export->load($id);
                $exportFile = Mage::helper('marketexport')->getFullExportDir() .'/'. $export->getId() . ".xml";
                if (is_file($exportFile)){
                    unlink($exportFile);
                }
                $export->delete();

                $this->_getSession()->addSuccess(Mage::helper('marketexport')->__('File was delete'));
                $this->_redirect('*/*/');
            }catch(Exception $e){
                $this->_getSession()->addError(Mage::helper('marketexport')->__('File was not delete'));
                $this->_redirect('*/*/');
            }
        }else{
            $this->_getSession()->addError(Mage::helper('marketexport')->__('File was not delete'));
            $this->_redirect('*/*/');
        }
    }

    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('export_ids', null);

        if($ids!= null){
            try{
                foreach ($ids as $id){
                    $export = Mage::getModel('marketexport/export');
                    $export->load($id);
                    $exportFile = Mage::helper('marketexport')->getFullExportDir(). '/' . $export->getId() . ".xml";
                    if (is_file($exportFile)){
                        unlink($exportFile);
                    }
                    $export->delete();
                }

                $this->_getSession()->addSuccess(Mage::helper('marketexport')->__('Files was delete'));
                $this->_redirect('*/*/');
                return;
            }catch(Exception $e){
                $this->_getSession()->addError(Mage::helper('marketexport')->__('Files was not delete'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_getSession()->addError(Mage::helper('marketexport')->__('Files was not delete'));
        $this->_redirect('*/*/');
    }

    public function massActiveAction()
    {
        $ids = $this->getRequest()->getParam('export_ids', null);

        if($ids!= null){
            try{
                foreach ($ids as $id){
                    $export = Mage::getModel('marketexport/export');
                    $export->load($id)->setIsActive(1)->save();
                }

                $this->_getSession()->addSuccess(Mage::helper('marketexport')->__('Export files is active'));
                $this->_redirect('*/*/');
                return;
            }catch(Exception $e){
                $this->_getSession()->addError(Mage::helper('marketexport')->__('Error! Activity status remains the same export files'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_getSession()->addError(Mage::helper('marketexport')->__('Error! Activity status remains the same export files'));
        $this->_redirect('*/*/');
    }

    public function massDisactiveAction()
    {
        $ids = $this->getRequest()->getParam('export_ids', null);

        if($ids!= null){
            try{
                foreach ($ids as $id){
                    $export = Mage::getModel('marketexport/export');
                    $export->load($id)->setIsActive(0)->save();
                }

                $this->_getSession()->addSuccess(Mage::helper('marketexport')->__('Export files is not active'));
                $this->_redirect('*/*/');
                return;
            }catch(Exception $e){
                $this->_getSession()->addError(Mage::helper('marketexport')->__('Error! Activity status remains the same export files'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_getSession()->addError(Mage::helper('marketexport')->__('Error! Activity status remains the same export files'));
        $this->_redirect('*/*/');
    }
    
    public function resetAction()
    {
        Mage::getSingleton('core/resource')
            ->getConnection('write')
            ->truncate('visits');

        $productIds = Mage::getModel('catalog/product')
            ->getCollection()
            ->getAllIds();

        //**ME6 - STORE ID
        Mage::getSingleton('catalog/product_action')
                ->updateAttributes(
            $productIds,
            array('additional_top_weight' => 0),
            Mage_Core_Model_App::ADMIN_STORE_ID
        );
        
        Mage::getSingleton('adminhtml/session')
            ->addSuccess($this->__('Products Rating has been successfully reseted.'));
        $this->_redirectReferer();
    }

    /**
     * restrict access
     *
     * @access protected
     * @return bool
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('brander_shop/marketexport');
    }
}
