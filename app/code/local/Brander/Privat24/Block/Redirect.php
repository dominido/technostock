<?php
class Brander_Privat24_Block_Redirect extends Mage_Core_Block_Template
{
    /**
     * Set template with message
     */
    protected function _construct()
    {
        $this->setTemplate('brander/privat24/redirect.phtml');
        parent::_construct();
    }

    /**
     * Return redirect form
     *
     * @return Varien_Data_Form
     */
    public function getForm()
    {
        $paymentMethod = Mage::getModel('privat24/redirect');

        $form = new Varien_Data_Form();
        $form->setAction($paymentMethod->getPrivat24PlaceUrl())
            ->setId('privat24_redirect')
            ->setName('privat24_redirect')
            ->setMethod('POST')
            ->setUseContainer(true);

        foreach ($paymentMethod->getRedirectFormFields() as $field => $value) {
            $form->addField($field, 'hidden', array('name' => $field, 'value'=>$value));
        }
        
        return $form;
    }
}