<?php
/**
 * Brander Account extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        Account
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */

require_once Mage::getModuleDir('controllers', 'Mage_Wishlist') . DS . 'IndexController.php';
class Brander_Account_Wishlist_IndexController extends Mage_Wishlist_IndexController
{

    /**
     * Add the item to wish list
     *
     * @return Mage_Core_Controller_Varien_Action|void
     */
    protected function _addItemToWishList()
    {
        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            return $this->norouteAction();
        }

        $session = Mage::getSingleton('customer/session');

        $productId = (int)$this->getRequest()->getParam('product');
        if (!$productId) {
            $this->_redirect('*/');
            return;
        }

        $product = Mage::getModel('catalog/product')->load($productId);
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            $session->addError($this->__('Cannot specify product.'));
            $this->_redirect('*/');
            return;
        }

        try {
            $requestParams = $this->getRequest()->getParams();
            if ($session->getBeforeWishlistRequest()) {
                $requestParams = $session->getBeforeWishlistRequest();
                $session->unsBeforeWishlistRequest();
            }
            $buyRequest = new Varien_Object($requestParams);

            $result = $wishlist->addNewItem($product, $buyRequest);
            if (is_string($result)) {
                Mage::throwException($result);
            }
            $wishlist->save();

            Mage::dispatchEvent(
                'wishlist_add_product',
                array(
                    'wishlist' => $wishlist,
                    'product' => $product,
                    'item' => $result
                )
            );

            $referer = $session->getBeforeWishlistUrl();
/*            if ($referer) {
                $session->setBeforeWishlistUrl(null);
            } else {
                $referer = $this->_getRefererUrl();
            }*/

            /**
             *  Set referer to avoid referring to the compare popup window
             */
            $session->setAddActionReferer($referer);

            Mage::helper('wishlist')->calculate();

            /*$message = $this->__('%1$s has been added to your wishlist. Click <a href="%2$s">here</a> to continue shopping.',
                $product->getName(), Mage::helper('core')->escapeUrl($referer));*/
            $message = $this->__('%s has been added to your wishlist.', $product->getName());
            Mage::getSingleton('core/session')->addSuccess($message);
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('core/session')->addError($this->__('An error occurred while adding item to wishlist: %s', $e->getMessage()));
        }
        catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($this->__('An error occurred while adding item to wishlist.'));
        }

        $this->_redirectReferer();
    }

    public function clearAction()
    {
        try {

            $wishlistItemCollection = Mage::helper('wishlist')->getWishlist()->getItemCollection();
            if (count($wishlistItemCollection)) {
                $modelWishlistItem = Mage::getModel('wishlist/item');
                foreach ($wishlistItemCollection as $item) {
                    $modelWishlistItem->load($item->getId())->delete();
                }
                Mage::getSingleton('core/session')->addSuccess(
                    $this->__('List is cleared')
                );
            }

        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('core/session')->addError(
                $this->__('An error occurred while deleting items from wishlist: %s', $e->getMessage())
            );
        } catch(Exception $e) {
            Mage::getSingleton('core/session')->addError(
                $this->__('An error occurred while deleting items from wishlist.')
            );
        }
        Mage::helper('wishlist')->calculate();

        $this->_redirectReferer(Mage::helper('wishlist')->getListUrl());
    }
}
