<?php
class Brander_Preorder_IndexController extends Mage_Core_Controller_Front_Action
{
    const XML_PATH_EMAIL_RECIPIENT = 'contacts/email/recipient_email';
    const XML_PATH_EMAIL_SENDER = 'contacts/email/sender_email_identity';
    const XML_PATH_EMAIL_TEMPLATE = 'contacts/email/email_template';
    const XML_PATH_ENABLED = 'contacts/contacts/enabled';

    public function postAction()
    {
        $post = $this->getRequest()->getPost();
        $response = $this->getResponse()->setHeader('Content-Type', 'application/json', true);
        $result = new Varien_Object();
        if ($post) {
            try {
                $postObject = new Varien_Object();
                $postObject->setData($post);

                $error = false;

                if (!Zend_Validate::is(trim($post['name']), 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['phone']), 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['qty']), 'NotEmpty')) {
                    $error = true;
                }

                if ($error) {
                    throw new Exception('Please make sure to fill in all fields');
                }
            } catch (Exception $e) {
                $result->error = Mage::helper('customer_account')->__($e->getMessage());
            }
            $model = Mage::getModel("preorder/preorder");
            $model->setId(null);
            $model->setData('user_phone', $post['phone']);
            $model->setData('user_name', $post['name']);
            $model->setData('product_id', $post['product_id_preorder']);
            $model->setData('product_qty', $post['qty']);
            $model->setData('user_comment', $post['comment']);
            $model->setData('status', false);
            $model->save();

            Mage::helper('preorder')->sendMail($post);

            $result->success = $this->__('Your order has been accepted for moderation. In the near future you will be contacted our managers.');
            $result->form_visibility = "hide";
        } else {
            $result->error = $this->__("Sorry, something went wrong...");
        }
        return $response->setBody($result->toJSON());
    }
}