<?php
/**
 * Brander_ShopReview extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category   	Brander
 * @package		Brander_ShopReview
 * @copyright  	Copyright (c) 2016
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * ShopReview default helper
 *
 * @category    Brander
 * @package     Brander_ShopReview
 * @author      Ultimate Module Creator
 */
class Brander_ShopReview_Helper_Data
    extends Mage_Core_Helper_Abstract {
    /**
     * convert array to options
     * @access public
     * @param $options
     * @return array
     * @author Ultimate Module Creator
     */
    public function convertOptions($options){
        $converted = array();
        foreach ($options as $option){
            if (isset($option['value']) && !is_array($option['value']) && isset($option['label']) && !is_array($option['label'])){
                $converted[$option['value']] = $option['label'];
            }
        }
        return $converted;
    }

    public function sendMail($post)
    {
        try
        {
            $subject =  $this->__('New Shop Review');
            $sendersArray = explode(',',Mage::getStoreConfig('brander_shopreview/shopreview/email',Mage::app()->getStore()));
            $senderName = Mage::getStoreConfig('brander_shopreview/shopreview/name',Mage::app()->getStore());

            if(Mage::getStoreConfig('brander_shopreview/shopreview/email_template')){
                $emailTemplate = Mage::getModel('core/email_template')->load(Mage::getStoreConfig('brander_shopreview/shopreview/email_template'));
            } else {
                $emailTemplate = Mage::getModel('core/email_template')->loadDefault('brander_shopreview');
            }
            $emailTemplate->setSenderName($senderName);
//            $emailTemplate->setSenderEmail($sender);
            $emailTemplate->setTemplateSubject($subject);
            $emailTemplateVariables = array();
            $emailTemplateVariables['cname'] = $post['user_name'];
            $emailTemplateVariables['csubject'] = $post['subject_review'];
            $emailTemplateVariables['cemail'] = $post['user_email'];
            $emailTemplateVariables['cphone'] = $post['user_phone'];
            $emailTemplateVariables['creview'] = $post['user_review'];
            $emailTemplateVariables['cdate'] = now();
            $emailTemplateVariables['subject'] = $subject;
            $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
            for($i=0; $i<count($sendersArray);$i++){
                $emailTemplate->setSenderEmail($sendersArray[$i]);
                $emailTemplate->send($sendersArray[$i],$senderName, $emailTemplateVariables);
            }

        } catch (Exception $e) {
            Mage::log($e->getMessage(),null,'email.log');
        }
    }
}
