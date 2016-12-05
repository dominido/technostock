<?php
/**
 * {{Brander}}_CategoryBanner extension
 */
/**
 * Category Image admin edit form
 *
 * @category    Brander
 * @package     Brander_CategoryBanner
 * @author      Ultimate Module Creator
 */
class Brander_CategoryBanner_Block_Adminhtml_Categorybanner_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * constructor
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'brander_categorybanner';
        $this->_controller = 'adminhtml_categorybanner';
        $this->_updateButton(
            'save',
            'label',
            Mage::helper('brander_categorybanner')->__('Save Category Image')
        );
        $this->_updateButton(
            'delete',
            'label',
            Mage::helper('brander_categorybanner')->__('Delete Category Image')
        );
        $this->_addButton(
            'saveandcontinue',
            array(
                'label'   => Mage::helper('brander_categorybanner')->__('Save And Continue Edit'),
                'onclick' => 'saveAndContinueEdit()',
                'class'   => 'save',
            ),
            -100
        );
        $this->_formScripts[] = "
            function saveAndContinueEdit() {
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    /**
     * get the edit form header
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getHeaderText()
    {
        if (Mage::registry('current_categorybanner') && Mage::registry('current_categorybanner')->getId()) {
            return Mage::helper('brander_categorybanner')->__(
                "Edit Category Image '%s'",
                $this->escapeHtml(Mage::registry('current_categorybanner')->getCategoryImageName())
            );
        } else {
            return Mage::helper('brander_categorybanner')->__('Add Category Image');
        }
    }
}
