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
class Brander_UnitopBlog_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
    /**
     * init routes
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Brander_UnitopBlog_Controller_Router

     */
    public function initControllerRouters($observer)
    {
        $front = $observer->getEvent()->getFront();
        $front->addRouter('brander_unitopblog', $this);
        return $this;
    }

    /**
     * Validate and match entities and modify request
     *
     * @access public
     * @param Zend_Controller_Request_Http $request
     * @return bool

     */
    public function match(Zend_Controller_Request_Http $request)
    {
        if (!Mage::isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect(Mage::getUrl('install'))
                ->sendResponse();
            exit;
        }
        $urlKey = trim($request->getPathInfo(), '/');
        $check = array();
        $check['postscategory'] = new Varien_Object(
            array(
                'prefix'        => Mage::getStoreConfig('brander_unitopblog/postscategory/url_prefix'),
                'suffix'        => Mage::getStoreConfig('brander_unitopblog/postscategory/url_suffix'),
                'list_key'      => Mage::getStoreConfig('brander_unitopblog/postscategory/url_rewrite_list'),
                'list_action'   => 'index',
                'model'         =>'brander_unitopblog/postscategory',
                'controller'    => 'postscategory',
                'action'        => 'view',
                'param'         => 'id',
                'check_path'    => 1
            )
        );
        $check['post'] = new Varien_Object(
            array(
                'prefix'        => Mage::getStoreConfig('brander_unitopblog/post/url_prefix'),
                'suffix'        => Mage::getStoreConfig('brander_unitopblog/post/url_suffix'),
                'list_key'      => Mage::getStoreConfig('brander_unitopblog/post/url_rewrite_list'),
                'list_action'   => 'index',
                'model'         =>'brander_unitopblog/post',
                'controller'    => 'post',
                'action'        => 'view',
                'param'         => 'id',
                'check_path'    => 0
            )
        );
        foreach ($check as $key=>$settings) {
            if ($settings->getListKey()) {
                if ($urlKey == $settings->getListKey()) {
                    $request->setModuleName('blog')
                        ->setControllerName($settings->getController())
                        ->setActionName($settings->getListAction());
                    $request->setAlias(
                        Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
                        $urlKey
                    );
                    return true;
                }
            }
            if ($settings['prefix']) {
                $parts = explode('/', $urlKey);
                if ($parts[0] != $settings['prefix'] || count($parts) != 2) {
                    continue;
                }
                $urlKey = $parts[1];
            }
            if ($settings['suffix']) {
                $urlKey = substr($urlKey, 0, -strlen($settings['suffix']) - 1);
            }
            $model = Mage::getModel($settings->getModel());
            $id = $model->checkUrlKey($urlKey, Mage::app()->getStore()->getId());
            if ($id) {
                if ($settings->getCheckPath() && !$model->load($id)->getStatusPath()) {
                    continue;
                }
                $request->setModuleName('blog')
                    ->setControllerName($settings->getController())
                    ->setActionName($settings->getAction())
                    ->setParam($settings->getParam(), $id);
                $request->setAlias(
                    Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
                    $urlKey
                );
                return true;
            }
        }
        return false;
    }
}
