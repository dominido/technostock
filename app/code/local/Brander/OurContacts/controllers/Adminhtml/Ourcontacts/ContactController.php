<?php
/**
 * Brander OurContacts extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        OurContacts
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_OurContacts_Adminhtml_Ourcontacts_ContactController extends Mage_Adminhtml_Controller_Action
{
    /**
     * constructor - set the used module name
     *
     * @access protected
     * @return void
     * @see Mage_Core_Controller_Varien_Action::_construct()
     */
    protected function _construct()
    {
        $this->setUsedModuleName('Brander_OurContacts');
    }

    /**
     * init the contact
     *
     * @access protected 
     * @return Brander_OurContacts_Model_Contact
     */
    protected function _initContact()
    {
        $this->_title($this->__('Our Contacts'))
             ->_title($this->__('Manage Contacts'));

        $contactId  = (int) $this->getRequest()->getParam('id');
        $contact    = Mage::getModel('brander_ourcontacts/contact')
            ->setStoreId($this->getRequest()->getParam('store', 0));

        if ($contactId) {
            $contact->load($contactId);
        }
        Mage::register('current_contact', $contact);
        return $contact;
    }

    /**
     * default action for contact controller
     *
     * @access public
     * @return void
     */
    public function indexAction()
    {
        $this->_title($this->__('Our Contacts'))
             ->_title($this->__('Manage Contacts'));
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * new contact action
     *
     * @access public
     * @return void
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * edit contact action
     *
     * @access public
     * @return void

     */
    public function editAction()
    {
        $contactId  = (int) $this->getRequest()->getParam('id');
        $contact    = $this->_initContact();
        if ($contactId && !$contact->getId()) {
            $this->_getSession()->addError(
                Mage::helper('brander_ourcontacts')->__('This contact no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        if ($data = Mage::getSingleton('adminhtml/session')->getContactData(true)) {
            $contact->setData($data);
        }
        $this->_title($contact->getTitle());
        Mage::dispatchEvent(
            'brander_ourcontacts_contact_edit_action',
            array('contact' => $contact)
        );
        $this->loadLayout();
        if ($contact->getId()) {
            if (!Mage::app()->isSingleStoreMode() && ($switchBlock = $this->getLayout()->getBlock('store_switcher'))) {
                $switchBlock->setDefaultStoreName(Mage::helper('brander_ourcontacts')->__('Default Values'))
                    ->setWebsiteIds($contact->getWebsiteIds())
                    ->setSwitchUrl(
                        $this->getUrl(
                            '*/*/*',
                            array(
                                '_current'=>true,
                                'active_tab'=>null,
                                'tab' => null,
                                'store'=>null
                            )
                        )
                    );
            }
        } else {
            $this->getLayout()->getBlock('left')->unsetChild('store_switcher');
        }
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }

    /**
     * save contact action
     *
     * @access public
     * @return void
     */
    public function saveAction()
    {
        $storeId        = $this->getRequest()->getParam('store');
        $redirectBack   = $this->getRequest()->getParam('back', false);
        $contactId   = $this->getRequest()->getParam('id');
        $isEdit         = (int)($this->getRequest()->getParam('id') != null);
        $data = $this->getRequest()->getPost();
        if ($data) {
            $contact     = $this->_initContact();
            $contactData = $this->getRequest()->getPost('contact', array());
            $contact->addData($contactData);
            $contact->setAttributeSetId($contact->getDefaultAttributeSetId());
            if ($useDefaults = $this->getRequest()->getPost('use_default')) {
                foreach ($useDefaults as $attributeCode) {
                    $contact->setData($attributeCode, false);
                }
            }
            try {
                $contact->save();
                $contactId = $contact->getId();
                $this->_getSession()->addSuccess(
                    Mage::helper('brander_ourcontacts')->__('Contact was saved')
                );
            } catch (Mage_Core_Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage())
                    ->setContactData($contactData);
                $redirectBack = true;
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError(
                    Mage::helper('brander_ourcontacts')->__('Error saving contact')
                )
                ->setContactData($contactData);
                $redirectBack = true;
            }
        }
        if ($redirectBack) {
            $this->_redirect(
                '*/*/edit',
                array(
                    'id'    => $contactId,
                    '_current'=>true
                )
            );
        } else {
            $this->_redirect('*/*/', array('store'=>$storeId));
        }
    }

    /**
     * delete contact
     *
     * @access public
     * @return void
     */
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $contact = Mage::getModel('brander_ourcontacts/contact')->load($id);
            try {
                $contact->delete();
                $this->_getSession()->addSuccess(
                    Mage::helper('brander_ourcontacts')->__('The contacts has been deleted.')
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->getResponse()->setRedirect(
            $this->getUrl('*/*/', array('store'=>$this->getRequest()->getParam('store')))
        );
    }

    /**
     * mass delete contacts
     *
     * @access public
     * @return void
     */
    public function massDeleteAction()
    {
        $contactIds = $this->getRequest()->getParam('contact');
        if (!is_array($contactIds)) {
            $this->_getSession()->addError($this->__('Please select contacts.'));
        } else {
            try {
                foreach ($contactIds as $contactId) {
                    $contact = Mage::getSingleton('brander_ourcontacts/contact')->load($contactId);
                    Mage::dispatchEvent(
                        'brander_ourcontacts_controller_contact_delete',
                        array('contact' => $contact)
                    );
                    $contact->delete();
                }
                $this->_getSession()->addSuccess(
                    Mage::helper('brander_ourcontacts')->__('Total of %d record(s) have been deleted.', count($contactIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass status change - action
     *
     * @access public
     * @return void
     */
    public function massStatusAction()
    {
        $contactIds = $this->getRequest()->getParam('contact');
        if (!is_array($contactIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('brander_ourcontacts')->__('Please select contacts.')
            );
        } else {
            try {
                foreach ($contactIds as $contactId) {
                $contact = Mage::getSingleton('brander_ourcontacts/contact')->load($contactId)
                    ->setStatus($this->getRequest()->getParam('status'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d contacts were successfully updated.', count($contactIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('brander_ourcontacts')->__('There was an error updating contacts.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * grid action
     *
     * @access public
     * @return void
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
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
        return Mage::getSingleton('admin/session')->isAllowed('brander_shop/shop_content/brander_ourcontacts');
    }

    /**
     * Export contacts in CSV format
     *
     * @access public
     * @return void
     */
    public function exportCsvAction()
    {
        $fileName   = 'contacts.csv';
        $content    = $this->getLayout()->createBlock('brander_ourcontacts/adminhtml_contact_grid')
            ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export contacts in Excel format
     *
     * @access public
     * @return void
     */
    public function exportExcelAction()
    {
        $fileName   = 'contact.xls';
        $content    = $this->getLayout()->createBlock('brander_ourcontacts/adminhtml_contact_grid')
            ->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export contacts in XML format
     *
     * @access public
     * @return void
     */
    public function exportXmlAction()
    {
        $fileName   = 'contact.xml';
        $content    = $this->getLayout()->createBlock('brander_ourcontacts/adminhtml_contact_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * wysiwyg editor action
     *
     * @access public
     * @return void
     */
    public function wysiwygAction()
    {
        $elementId     = $this->getRequest()->getParam('element_id', md5(microtime()));
        $storeId       = $this->getRequest()->getParam('store_id', 0);
        $storeMediaUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);

        $content = $this->getLayout()->createBlock(
            'brander_ourcontacts/adminhtml_ourcontacts_helper_form_wysiwyg_content',
            '',
            array(
                'editor_element_id' => $elementId,
                'store_id'          => $storeId,
                'store_media_url'   => $storeMediaUrl,
            )
        );
        $this->getResponse()->setBody($content->toHtml());
    }

    /**
     * mass Show in Header change
     *
     * @access public
     * @return void
     */
    public function massShowInHeaderAction()
    {
        $contactIds = (array)$this->getRequest()->getParam('contact');
        $storeId       = (int)$this->getRequest()->getParam('store', 0);
        $flag          = (int)$this->getRequest()->getParam('flag_show_in_header');
        if ($flag == 2) {
            $flag = 0;
        }
        try {
            foreach ($contactIds as $contactId) {
                $contact = Mage::getSingleton('brander_ourcontacts/contact')
                    ->setStoreId($storeId)
                    ->load($contactId);
                $contact->setShowInHeader($flag)->save();
            }
            $this->_getSession()->addSuccess(
                Mage::helper('brander_ourcontacts')->__('Total of %d record(s) have been updated.', count($contactIds))
            );
        } catch (Mage_Core_Model_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addException(
                $e,
                Mage::helper('brander_ourcontacts')->__('An error occurred while updating the contacts.')
            );
        }
        $this->_redirect('*/*/', array('store'=> $storeId));
    }

    /**
     * mass Show In Footer change
     *
     * @access public
     * @return void
     */
    public function massShowInFooterAction()
    {
        $contactIds = (array)$this->getRequest()->getParam('contact');
        $storeId       = (int)$this->getRequest()->getParam('store', 0);
        $flag          = (int)$this->getRequest()->getParam('flag_show_in_footer');
        if ($flag == 2) {
            $flag = 0;
        }
        try {
            foreach ($contactIds as $contactId) {
                $contact = Mage::getSingleton('brander_ourcontacts/contact')
                    ->setStoreId($storeId)
                    ->load($contactId);
                $contact->setShowInFooter($flag)->save();
            }
            $this->_getSession()->addSuccess(
                Mage::helper('brander_ourcontacts')->__('Total of %d record(s) have been updated.', count($contactIds))
            );
        } catch (Mage_Core_Model_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addException(
                $e,
                Mage::helper('brander_ourcontacts')->__('An error occurred while updating the contacts.')
            );
        }
        $this->_redirect('*/*/', array('store'=> $storeId));
    }
}
