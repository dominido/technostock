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
class ET_SocialLogin_Block_Share extends Mage_Core_Block_Template
{
    protected $_item;


    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('et_sociallogin/share.phtml');
        $storeId = Mage::app()->getStore()->getId();
        $label = Mage::getStoreConfig('social_login/sharing/block_label', $storeId);
        $this->setData('size', 'small');
        $this->setData('label_text', $label);
    }

    /**
     * @return array
     * returns array for share links (url, image link, title, description)
     */
    public function getShareData()
    {
        if (Mage::helper('et_sociallogin')->isSharingEnabled()) {
            // limit for short description needs for vk.com other way it redirects to 400 error.
            $limit = Mage::getStoreConfig('social_login/short_description_length');
            $object = $this->_item;

            if ($object instanceof Mage_Catalog_Model_Product) {
                return array(
                    'url' => $object->getProductUrl(),
                    'title' => htmlspecialchars(str_replace("'", "\'", $object->getName())),
                    'image' => Mage::helper('catalog/image')->init($object, 'image')->resize('600', '600'),
                    'description' => htmlspecialchars(str_replace(array("'", "\r", "\n"), array("\'", " ", " "),
                        mb_substr($object->getData('short_content'), 0, $limit)))
                );
            } elseif ($object instanceof CommerceLab_News_Model_News) {
                return array(
                    'url' => $object->getUrl(),
                    'title' => htmlspecialchars(str_replace("'", "\'", $object->getTitle())),
                    'image' => Mage::helper('et_sociallogin')->resizeImage(str_replace('clnews/', '',
                            $object->getImageFullContent()), '930', null, 'clnews'),
                    'description' => htmlspecialchars(str_replace(array("'", "\r", "\n"), array("\'", " ", " "),
                        mb_substr($object->getData('short_content'), 0, $limit)))
                );
            }
        }
        return false;
    }

    public function getShareCounter($url, $provider)
    {
        $socialNetworks = array(
            'facebook' => 'http://api.facebook.com/restserver.php?method=links.getStats&urls=' . $url . '&format=json',
            'twitter' => 'https://cdn.api.twitter.com/1/urls/count.json?url=' . $url,
            'vk' => 'https://vk.com/share.php?act=count&index=1&url=' . $url . '&callback=?'
        );

        switch ($provider) {
            case 'facebook' :
                $result = json_decode(file_get_contents($socialNetworks['fb']));
                return $result[0]->share_count;
                break;
            case 'twitter' :
                $result = json_decode(file_get_contents($socialNetworks['twitter']));
                return $result->count;
                break;
            case 'vk':
                $result = file_get_contents($socialNetworks['vk']);
                preg_match('/\([0-9]+,(.*)\)/', $result, $match);
                return $match[1];
            default:
                return false;
        }
    }

    public function getLabelText()
    {
        return '';
    }

    /**
     * product is getting from layout by setData method
     * @return $this|Mage_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        $this->_item = $this->getData('item');

        return $this;
    }
}