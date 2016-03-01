<?php

/**
 * NOTICE OF LICENSE
 *
 * You may not give, sell, distribute, sub-license, rent, lease or lend
 * any portion of the Software or Documentation to anyone.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade to newer
 * versions in the future.
 *
 * @category   ET
 * @package    ET_SocialLogin
 * @copyright  Copyright (c) 2015 ET Web Solutions (http://etwebsolutions.com)
 * @contacts   support@etwebsolutions.com
 * @license    http://shop.etwebsolutions.com/etws-license-commercial-v1/   ETWS Commercial License (ECL1)
 */
class ET_SocialLogin_Adminhtml_SocialReportController extends Mage_Adminhtml_Controller_Report_Abstract
{
    public function _initAction()
    {
        parent::_initAction();
        $this->_addBreadcrumb(Mage::helper('reports')->__('Reports'), Mage::helper('reports')->__('Reports'))
            ->_addBreadcrumb(Mage::helper('et_sociallogin')
                ->__('Social Accounts Report'), Mage::helper('et_sociallogin')->__('Social Accounts Report'));
        return $this;
    }

    public function indexAction()
    {

        $this->_title($this->__('Social Accounts Report'));

        //$this->_showLastExecutionTime(ET_ReportsCustomProduct_Model_Flag::REPORT_BUYERS_FLAG_CODE, 'products_buyers');

        $this->_initAction();

        $gridBlock = $this->getLayout()->getBlock('adminhtml_socialReport.grid');

        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));


        //$this->_addContent($this->getLayout()->createBlock('et_reportscustomproduct/adminhtml_buyers'));
        $this->renderLayout();
    }


    /**
     * Check is allowed for report
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }

}