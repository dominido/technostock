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
class Brander_UnitopBlog_Block_Adminhtml_Post_Comment_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return UnitopBlog_Post_Block_Adminhtml_Post_Comment_Edit_Tab_Form

     */
    protected function _prepareForm()
    {
        $post = Mage::registry('current_post');
        $comment    = Mage::registry('current_comment');
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('comment_');
        $form->setFieldNameSuffix('comment');
        $this->setForm($form);
        $fieldset = $form->addFieldset('comment_form', array('legend'=>Mage::helper('brander_unitopblog')->__('Comment')));
        $fieldset->addField('post_id','hidden',
            array('name'  => 'post_id', 'after_element_html' => '<a href="'.Mage::helper('adminhtml')->getUrl('adminhtml/unitopblog_post/edit',
                        array('id'=>$post->getId())).'" target="_blank">'.Mage::helper('brander_unitopblog')->__('Post').' : '.$post->getTitle().'</a>')
        );
/*        $fieldset->addField(
            'title',
            'text',
            array(
                'label'    => Mage::helper('brander_unitopblog')->__('Title'),
                'name'     => 'title',
                'required' => true,
                'class'    => 'required-entry',
            )
        );*/

        $configuration = array(
            'label' => Mage::helper('brander_unitopblog')->__('Poster name'),
            'name'  => 'name',
            'required'  => true,
            'class' => 'required-entry',
        );
        if ($comment->getCustomerId()) {
            $configuration['after_element_html'] = '<a href="'.Mage::helper('adminhtml')->getUrl('adminhtml/customer/edit',
                    array('id'=>$comment->getCustomerId())).'" target="_blank">'.Mage::helper('brander_unitopblog')->__('Customer profile').'</a>';
        }
        $fieldset->addField('name', 'text', $configuration);
        $fieldset->addField('customer_id', 'hidden', array('name'  => 'customer_id'));

        if (Mage::helper('brander_unitopblog')->getCfg('brander_unitopblog/post/necessarily_poster_email')) {
            $fieldset->addField(
                'email',
                'text',
                array(
                    'label' => Mage::helper('brander_unitopblog')->__('Poster e-mail'),
                    'name'  => 'email',
                    'required'  => true,
                    'class' => 'required-entry',
                )
            );
        }

        $fieldset->addField(
            'comment',
            'textarea',
            array(
                'label'    => Mage::helper('brander_unitopblog')->__('Comment'),
                'name'     => 'comment',
                'required' => true,
                'class'    => 'required-entry',
            )
        );

        $fieldset->addField(
            'status',
            'select',
            array(
                'label'    => Mage::helper('brander_unitopblog')->__('Status'),
                'name'     => 'status',
                'required' => true,
                'class'    => 'required-entry',
                'values'   => array(
                    array(
                        'value' => Brander_UnitopBlog_Model_Post_Comment::STATUS_PENDING,
                        'label' => Mage::helper('brander_unitopblog')->__('Pending'),
                    ),
                    array(
                        'value' => Brander_UnitopBlog_Model_Post_Comment::STATUS_APPROVED,
                        'label' => Mage::helper('brander_unitopblog')->__('Approved'),
                    ),
                    array(
                        'value' => Brander_UnitopBlog_Model_Post_Comment::STATUS_REJECTED,
                        'label' => Mage::helper('brander_unitopblog')->__('Rejected'),
                    ),
                ),
            )
        );

        if (Mage::app()->isSingleStoreMode()) {
            $fieldset->addField(
                'store_id',
                'hidden',
                array(
                    'name'      => 'stores[]',
                    'value'     => Mage::app()->getStore(true)->getId()
                )
            );
            Mage::registry('current_comment')->setStoreId(Mage::app()->getStore(true)->getId());
        }
        $form->addValues($this->getComment()->getData());
        return parent::_prepareForm();
    }

    /**
     * get the current comment
     *
     * @access public
     * @return Brander_UnitopBlog_Model_Post_Comment
     */
    public function getComment()
    {
        return Mage::registry('current_comment');
    }
}
