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
 * @copyright  Copyright (c) 2013 ET Web Solutions (http://etwebsolutions.com)
 * @contacts   support@etwebsolutions.com
 * @license    http://shop.etwebsolutions.com/etws-license-commercial-v1/   ETWS Commercial License (ECL1)
 */
class ET_SocialLogin_AuthController extends Mage_Core_Controller_Front_Action
{

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }


    /**
     * Page for request generation
     *
     * @return void
     */

    public function loginAction()
    {
        $session = $this->_getSession();

        $provider = $this->getRequest()->getParam('provider');
        $responsetype = $this->getRequest()->getParam('responsetype');

        if ($provider) {
            try {
                $session->setData('social_auth_response_type', $responsetype);
                /** @var  $authService ET_SocialLogin_Model_AuthService */
                $authService = Mage::getModel('et_sociallogin/authService');
                $connect = $authService->getConnect($provider, $responsetype = null);

                $refererUrl = $this->_getRefererUrl();
                if (empty($refererUrl)) {
                    $refererUrl = empty($defaultUrl) ? Mage::getBaseUrl() : $defaultUrl;
                }
                $session->setBeforeAuthUrl($refererUrl);

                // exit(var_dump($connect->getRequestUrl()));

                $this->getResponse()->setRedirect($connect->getRequestUrl());
            } catch (Exception $e) {
                Mage::logException($e);
                $refererUrl = $this->_getRefererUrl();
                Mage::getSingleton('core/session')
                    ->addError(Mage::helper('et_sociallogin')->__("Can't connect to the selected social network."));
                $script = '<script type="text/javascript">window.opener.focus();window.opener.location.href = "'
                    . $refererUrl . '"; window.close();</script>';
                $this->getResponse()->setBody($script);
                //return false;
                //$this->_redirect('customer/account');
            }
        }
    }

    /**
     * Page for processing result
     *
     */
    public function callbackAction()
    {
        $session = $this->_getSession();
        try {
            /* @var $authService ET_SocialLogin_Model_AuthService */
            $authService = Mage::getModel('et_sociallogin/authService');
            $result = $authService->callback($this->getRequest()->getParams());
            switch ($result->getStatus()) {
                case ET_SocialLogin_Model_AuthResult::AUTHORIZE_SUCCESS:

                    $type = $result->getSocialAuthResponseType();

                    if ($type == 'popup') {
                        $url = $this->_loginPostRedirect(false);
                        $script = '<script type="text/javascript">window.opener.focus();window.opener.location.href = "'
                            . $url . '"; window.close();</script>';
                        $this->getResponse()->setBody($script);
                    } else {
                        $this->_loginPostRedirect();
                    }
                    break;

                case ET_SocialLogin_Model_AuthResult::LINK_EXISTS_CUSTOMER:
                    $socData = $session->getSocialData(); // returned data from social network
                    $url = Mage::getUrl('social/auth/linkcustomer');
                    // filtered data from table et_social_customer
                    $socialCustomerData = Mage::getModel('et_sociallogin/socialCustomer')->getCollection()
                        ->addFieldToFilter('social_customer_id', array('eq' => $socData['userid']))
                        ->addFieldToFilter('social_provider', array('eq' => $socData['provider']));
                    if (count($socialCustomerData)) {
                        $helper = Mage::helper('et_sociallogin');
                        $session->addError($helper->__('This social account is already linked to other user'));
                        $script = '<script type="text/javascript">window.opener.focus();window.opener.location.href = "'
                            . $url . '"; window.close();</script>';
                        $this->getResponse()->setBody($script);
                        return false;
                    }

                    $this->linkSocialCustomer($session->getCustomer(), $session->getSocialData());
                    $helper = Mage::helper('et_sociallogin');
                    $session->addSuccess($helper->__('Account successfully linked.'));

                    $script = '<script type="text/javascript">window.opener.focus();window.opener.location.href = "'
                        . $url . '"; window.close();</script>';
                    $this->getResponse()->setBody($script);
                    return;
                    break;

                case ET_SocialLogin_Model_AuthResult::REDIRECT_TO_REGISTRATION:
                    $this->_redirect('social/auth/register');
                    break;

                case ET_SocialLogin_Model_AuthResult::AUTHORIZE_FAILED:
                    $this->_failCustomer($result->getMessage());
                    break;

                default:
                    break;
            }
        } catch (ET_SocialLogin_Model_SocialException $e) {
            $this->_failCustomer($e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }


    /**
     * Registration page
     *
     */

    public function registerAction()
    {
        $session = $this->_getSession();

        // Redirect if user is already authenticated
        if ($session->isLoggedIn()) {
            $this->_redirect('customer/account');
            return;
        }

        $userInfo = $this->_getSession()->getSocialData();

        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $block = Mage::getSingleton('core/layout')->getBlock('customer_form_register');

        $block->setDuplicateEmail($session->getDuplicateEmail(0));

        if (isset($userInfo['email'])) {
            $block->setUsername($userInfo['email']);
        }

        if (!$session->getCustomerFormData()) {
            if (isset($userInfo['email'])) {
                $block->getFormData()->setEmail($userInfo['email']);
            }

            if (isset($userInfo['firstname'])) {
                $block->getFormData()->setFirstname($userInfo['firstname']);
            }

            if (isset($userInfo['lastname'])) {
                $block->getFormData()->setLastname($userInfo['lastname']);
            }

            if (isset($userInfo['dob'])) {
                $block->getFormData()->setDob($userInfo['dob']);
            }

            if (isset($userInfo['gender'])) {
                $block->getFormData()->setGender($userInfo['gender']);
            }

            if (isset($userInfo['mini_photo'])) {
                $block->getFormData()->setMniPhoto($userInfo['mini_photo']);
            }
        }

        $this->renderLayout();
    }

    /**
     * Registration save
     */

    public function registerPostAction()
    {
        $helper = Mage::helper('et_sociallogin');
        $session = $this->_getSession();
        if ($session->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        $session->setEscapeMessages(true); // prevent XSS injection in user input
        if ($this->getRequest()->isPost()) {
            $errors = array();

            $session->setDuplicateEmail(0);


            // Sign in
            if ($this->getRequest()->getParam('password', false)) {
                return $this->duplicate();
            }

            if (!$customer = Mage::registry('current_customer')) {
                $customer = Mage::getModel('customer/customer')->setId(null);
            }

            /* @var $customerForm Mage_Customer_Model_Form */
            $customerForm = Mage::getModel('customer/form');
            $customerForm->setFormCode('customer_account_create')
                ->setEntity($customer);

            $customerSocialData = $this->_getSession()->getSocialData();

            $customer->setData($customerSocialData);

            $customerData = $customerForm->extractData($this->getRequest());

            if ($this->getRequest()->getParam('is_subscribed', false)) {
                $customer->setIsSubscribed(1);
            }

            /**
             * Initialize customer group id
             */
            $customer->getGroupId();

            try {
                $customerErrors = $customerForm->validateData($customerData);
                if ($customerErrors !== true) {
                    $errors = array_merge($customerErrors, $errors);
                } else {
                    $customerForm->compactData($customerData);
                    $password = uniqid();
                    $customer->setPassword($password);
                    $customer->setConfirmation($password);
                    $customer->setSkipConfirmationIfEmail($customer->getEmail());
                    $customer->setPasswordConfirmation($password); //variable changed from CE 1.9.1.0 and  EE 1.14.x
                    $customerErrors = $customer->validate();
                    if (is_array($customerErrors)) {
                        $errors = array_merge($customerErrors, $errors);
                    }
                }

                $validationResult = count($errors) == 0;

                if (true === $validationResult) {
                    $customer->save();

                    $this->linkSocialCustomer($customer, $customerSocialData);

                    Mage::dispatchEvent('customer_register_success',
                        array('account_controller' => $this, 'customer' => $customer)
                    );

                    $session->setCustomerAsLoggedIn($customer);
                    $url = $this->_welcomeCustomer($customer);

                    return;

                } else {

                    $session->setCustomerFormData($this->getRequest()->getPost());
                    if (is_array($errors)) {
                        foreach ($errors as $errorMessage) {
                            $session->addError($errorMessage);
                        }
                    } else {
                        $session->addError($helper->__('Invalid customer data'));
                    }
                }
            } catch (Mage_Core_Exception $e) {
                $session->setCustomerFormData($this->getRequest()->getPost());
                if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
                    $url = Mage::getUrl('customer/account/forgotpassword');
                    $message = $helper->__('You are already registered in our store, enter your password'
                    . ' in authorization form or <a href="%s">click here</a> to recovery password.', $url);

                    $session->setDuplicateEmail(1);
                    $session->setEscapeMessages(false);
                } else {
                    $message = $e->getMessage();
                }
                $session->addError($message);
            } catch (Exception $e) {
                $session->setCustomerFormData($this->getRequest()->getPost())
                    ->addException($e, $helper->__('Cannot save the customer.'));
            }
        }

        $this->_redirectError(Mage::getUrl('social/auth/register', array('_secure' => true)));
    }

    /**
     * Define target URL and redirect customer after logging in
     */
    protected function _loginPostRedirect($redirect = true)
    {
        $session = Mage::getSingleton('customer/session');

        if (!$session->getBeforeAuthUrl() || $session->getBeforeAuthUrl() == Mage::getBaseUrl()) {
            // Set default URL to redirect customer to
            $session->setBeforeAuthUrl(Mage::helper('customer')->getAccountUrl());
            // Redirect customer to the last page visited after logging in
            if ($session->isLoggedIn()) {
                if (!Mage::getStoreConfigFlag(
                //Mage_Customer_Helper_Data::XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD
                    'customer/startup/redirect_dashboard'
                )
                ) {
                    $referer = $this->getRequest()->getParam(Mage_Customer_Helper_Data::REFERER_QUERY_PARAM_NAME);
                    if ($referer) {
                        // Rebuild referer URL to handle the case when SID was changed
                        $referer = Mage::getModel('core/url')
                            ->getRebuiltUrl(Mage::helper('core')->urlDecode($referer));
                        if ($this->_isUrlInternal($referer)) {
                            $session->setBeforeAuthUrl($referer);
                        }
                    }
                } else if ($session->getAfterAuthUrl()) {
                    $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
                }
            } else {
                $session->setBeforeAuthUrl(Mage::helper('customer')->getLoginUrl());
            }
        } else if ($session->getBeforeAuthUrl() == Mage::helper('customer')->getLogoutUrl()) {
            $session->setBeforeAuthUrl(Mage::helper('customer')->getDashboardUrl());
        } else {
            if (!$session->getAfterAuthUrl()) {
                $session->setAfterAuthUrl($session->getBeforeAuthUrl());
            }
            if ($session->isLoggedIn()) {
                $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
            }
        }

        if ($redirect) {
            $this->_redirectUrl($session->getBeforeAuthUrl(true));
        } else {
            return $session->getBeforeAuthUrl(true);
        }
    }


    /**
     * Add welcome message and send new account email.
     * Returns success URL
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param bool $isJustConfirmed
     * @param bool $register
     * @return string
     */
    protected function _welcomeCustomer(
        Mage_Customer_Model_Customer $customer,
        $isJustConfirmed = false, $register = true
    ) {
        $helper = Mage::helper('et_sociallogin');
        if ($register) {
            $this->_getSession()->addSuccess(
                $helper->__('Thank you for registering with %s.', Mage::app()->getStore()->getFrontendName())
            );

            $customer->sendNewAccountEmail(
                $isJustConfirmed ? 'confirmed' : 'registered',
                '',
                Mage::app()->getStore()->getId()
            );
        }

        $successUrl = Mage::getUrl('customer/account', array('_secure' => true));
        if ($this->_getSession()->getBeforeAuthUrl()) {
            $successUrl = $this->_getSession()->getBeforeAuthUrl(true);
        }

        $popup = $this->_getSession()->getData('social_auth_response_type');;

        $script = '<script type="text/javascript">window.opener.focus();window.opener.location.href = "'
            . $successUrl . '"; window.close();</script>';
        return $this->getResponse()->setBody($script);
    }

    protected function _failCustomer($message)
    {
        $this->_getSession()->addWarning($message);

        $url = Mage::getUrl('customer/account/login');

        $popup = $this->_getSession()->getData('social_auth_response_type');;

        if ($popup == 'popup') {
            $script = '<script type="text/javascript">window.opener.focus();window.opener.location.href = "'
                . $url . '"; window.close();</script>';
            $this->getResponse()->setBody($script);
        } else {
            $this->_redirect($url);
        }
    }

    protected function linkSocialCustomer($customer, $userinfo)
    {
        $socialCustomer = Mage::getModel('et_sociallogin/socialCustomer');
        $socialCustomer->setSocialCustomerId($userinfo['userid']);
        $socialCustomer->setCustomerId($customer->getId());
        $socialCustomer->setSocialProvider($userinfo['provider']);
        $socialCustomer->setSocialProfileLink($userinfo['link']);
        $socialCustomer->setSocialName($userinfo['username']);
        $socialCustomer->setSocialPhoto((isset($userinfo['mini_photo'])) ? $userinfo['mini_photo'] : '');
        $socialCustomer->setWebsiteId(Mage::app()->getStore()->getWebsiteId());

        $socialCustomer->save();

        return $socialCustomer;
    }


    protected function duplicate()
    {
        $session = $this->_getSession();
        try {
            $isLogin = $session->login(
                $this->getRequest()->getParam('email'),
                $this->getRequest()->getParam('password')
            );

            if ($isLogin) {
                $customerSocialData = $this->_getSession()->getSocialData();
                $customer = $session->getCustomer();
                unset($customerSocialData['email'], $customerSocialData['firstname'], $customerSocialData['lastname']);

                $customer->addData($customerSocialData);
                $customer->save();

                $this->linkSocialCustomer($customer, $customerSocialData);

                $successUrl = Mage::getUrl('customer/account', array('_secure' => true));
                if ($this->_getSession()->getBeforeAuthUrl()) {
                    $successUrl = $this->_getSession()->getBeforeAuthUrl(true);
                }

                $script = '<script type="text/javascript">window.opener.focus();window.opener.location.href = "'
                    . $successUrl . '"; window.close();</script>';
                return $this->getResponse()->setBody($script);
            }
        } catch (Mage_Core_Exception $e) {
            switch ($e->getCode()) {
                case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                    $value = Mage::helper('customer')->getEmailConfirmationUrl($this->getRequest()->getParam('email'));
                    $helper = Mage::helper('et_sociallogin');
                    $message = $helper->__('This account is not confirmed.
                    <a href="%s">Click here</a> to resend confirmation email.', $value);
                    break;
                case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                    $message = $e->getMessage();
                    break;
                default:
                    $message = $e->getMessage();
            }
            $session->setDuplicateEmail(1);
            $session->addError($message);
            $session->setUsername($this->getRequest()->getParam('email'));
        } catch (Exception $e) {
            Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
        }

        $this->_redirect('social/auth/register');
    }


    public function linkCustomerAction()
    {
        if (!$this->_getSession()->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    public function unlinkAction()
    {
        $accountId = Mage::app()->getRequest()->getParam('account');
        $socialAccount = Mage::getModel('et_sociallogin/socialCustomer')->load($accountId);
        if ($socialAccount->getId()) {
            $socialAccount->delete();
        }

        $this->_redirect('*/*/linkcustomer');
    }
}
