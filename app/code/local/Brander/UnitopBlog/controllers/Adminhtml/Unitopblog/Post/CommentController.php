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
class Brander_UnitopBlog_Adminhtml_Unitopblog_Post_CommentController extends Mage_Adminhtml_Controller_Action
{
    /**
     * init the comment
     *
     * @access protected
     * @return Brander_UnitopBlog_Model_Post_Comment

     */
    protected function _initComment()
    {
        $commentId  = (int) $this->getRequest()->getParam('id');
        $comment    = Mage::getModel('brander_unitopblog/post_comment');
        if ($commentId) {
            $comment->load($commentId);
        }
        Mage::register('current_comment', $comment);
        return $comment;
    }

    /**
     * default action
     *
     * @access public
     * @return void

     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_title(Mage::helper('brander_unitopblog')->__('Blog'))
             ->_title(Mage::helper('brander_unitopblog')->__('Posts'))
             ->_title(Mage::helper('brander_unitopblog')->__('Comments'));
        $this->renderLayout();
    }

    /**
     * grid action
     *
     * @access public
     * @return void

     */
    public function gridAction()
    {
        $this->loadLayout()->renderLayout();
    }

    /**
     * edit comment - action
     *
     * @access public
     * @return void

     */
    public function editAction()
    {
        $commentId    = $this->getRequest()->getParam('id');
        $comment      = $this->_initComment();
        if (!$comment->getId()) {
            $this->_getSession()->addError(
                Mage::helper('brander_unitopblog')->__('This comment no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $comment->setData($data);
        }
        Mage::register('comment_data', $comment);
        $post = Mage::getModel('brander_unitopblog/post')->load($comment->getPostId());
        Mage::register('current_post', $post);
        $this->loadLayout();
        $this->_title(Mage::helper('brander_unitopblog')->__('Blog'))
             ->_title(Mage::helper('brander_unitopblog')->__('Posts'))
             ->_title(Mage::helper('brander_unitopblog')->__('Comments'))
             ->_title($comment->getTitle());
        $this->renderLayout();
    }

    /**
     * save post - action
     *
     * @access public
     * @return void

     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('comment')) {
            try {
                $comment = $this->_initComment();
                $comment->addData($data);
                if (!$comment->getCustomerId()) {
                    $comment->unsCustomerId();
                }
                $comment->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('brander_unitopblog')->__('Comment was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $comment->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
            catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('brander_unitopblog')->__('There was a problem saving the comment.')
                );
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('brander_unitopblog')->__('Unable to find comment to save.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete comment - action
     *
     * @access public
     * @return void

     */
    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0) {
            try {
                $comment = Mage::getModel('brander_unitopblog/post_comment');
                $comment->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('brander_unitopblog')->__('Comment was successfully deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('brander_unitopblog')->__('There was an error deleting the comment.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('brander_unitopblog')->__('Could not find comment to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * mass delete comments - action
     *
     * @access public
     * @return void

     */
    public function massDeleteAction()
    {
        $commentIds = $this->getRequest()->getParam('comment');
        if (!is_array($commentIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('brander_unitopblog')->__('Please select comments to delete.')
            );
        } else {
            try {
                foreach ($commentIds as $commentId) {
                    $comment = Mage::getModel('brander_unitopblog/post_comment');
                    $comment->setId($commentId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('brander_unitopblog')->__(
                        'Total of %d comments were successfully deleted.',
                        count($commentIds)
                    )
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('brander_unitopblog')->__('There was an error deleting comments.')
                );
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
        $commentIds = $this->getRequest()->getParam('comment');
        if (!is_array($commentIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('brander_unitopblog')->__('Please select comments.')
            );
        } else {
            try {
                foreach ($commentIds as $commentId) {
                    $comment = Mage::getSingleton('brander_unitopblog/post_comment')->load($commentId)
                         ->setStatus($this->getRequest()->getParam('status'))
                         ->setIsMassupdate(true)
                         ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d comments were successfully updated.', count($commentIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('brander_unitopblog')->__('There was an error updating comments.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * export as csv - action
     *
     * @access public
     * @return void

     */
    public function exportCsvAction()
    {
        $fileName   = 'post_comments.csv';
        $content    = $this->getLayout()->createBlock(
            'brander_unitopblog/adminhtml_post_comment_grid'
        )
        ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export as MsExcel - action
     *
     * @access public
     * @return void

     */
    public function exportExcelAction()
    {
        $fileName   = 'post_comments.xls';
        $content    = $this->getLayout()->createBlock(
            'brander_unitopblog/adminhtml_post_comment_grid'
        )
        ->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export as xml - action
     *
     * @access public
     * @return void

     */
    public function exportXmlAction()
    {
        $fileName   = 'post_comments.xml';
        $content    = $this->getLayout()->createBlock(
            'brander_unitopblog/adminhtml_post_comment_grid'
        )
        ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * check access
     *
     * @access protected
     * @return bool

     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('brander_shop/brander_unitopblog/post_comments');
    }
}
