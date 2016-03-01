<?php 
/**
 * Brander_Preorder extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category   	Brander
 * @package		Brander_Preorder
 * @copyright  	Copyright (c) 2015
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Preorder default helper
 *
 * @category	Brander
 * @package		Brander_Preorder
 * @author Ultimate Module Creator
 */
class Brander_Preorder_Helper_Data extends Mage_Core_Helper_Abstract{

    const XML_PATH_EMAIL_TEMPLATE   = 'trans_email/ident_sales/email_template';

    public function sendMail($post)
    {
        try
        {
            $subject =  $this->__('New order for out of stock');
            $sender = Mage::getStoreConfig('trans_email/ident_sales/email',Mage::app()->getStore());
            $senderName = Mage::getStoreConfig('trans_email/ident_sales/email',Mage::app()->getStore());

            if(Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE)){
                $emailTemplate = Mage::getModel('core/email_template')->load(Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE));
            } else {
                $emailTemplate = Mage::getModel('core/email_template')->loadDefault('preorder');
            }
            $emailTemplate->setSenderName($senderName);
            $emailTemplate->setSenderEmail($sender);
            $emailTemplate->setTemplateSubject($subject);
            $emailTemplateVariables = array();
            $emailTemplateVariables['cname'] = $post['name'];
            $emailTemplateVariables['cphone'] = $post['phone'];
            $product = Mage::getModel('catalog/product')->setStoreId(1)->load($post['product_id_preorder']);
            $name = $product->getName();
            $url = Mage::getModel('adminhtml/url')->getUrl('adminhtml/catalog_product/edit/id/'.$post['product_id_preorder']);
            $emailTemplateVariables['cprodname'] = $name;
            $emailTemplateVariables['ccurl'] = $url;
            $emailTemplateVariables['cqty'] = $post['qty'];
            $emailTemplateVariables['ccomment'] = $post['comment'];
            $emailTemplateVariables['subject'] = $subject;
            $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
            $emailTemplate->send($sender,$senderName, $emailTemplateVariables);

        } catch (Exception $e) {
            Mage::log($e->getMessage(),null,'email.log');
        }
    }
}