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
class Brander_UnitopBlog_Block_Adminhtml_Post_Comment_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * constructor
     *
     * @access public

     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('postCommentGrid');
        $this->setDefaultSort('ct_comment_id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * prepare collection
     *
     * @access protected
     * @return Brander_UnitopBlog_Block_Adminhtml_Post_Comment_Grid

     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('brander_unitopblog/post_comment_post_collection');
        $collection->addStoreData();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare grid collection
     *
     * @access protected
     * @return Brander_UnitopBlog_Block_Adminhtml_Post_Comment_Grid

     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'ct_comment_id',
            array(
                'header'        => Mage::helper('brander_unitopblog')->__('Id'),
                'index'         => 'ct_comment_id',
                'type'          => 'number',
                'filter_index'  => 'ct.comment_id',
            )
        );
        $this->addColumn(
            'title',
            array(
                'header'        => Mage::helper('brander_unitopblog')->__('Title'),
                'index'         => 'title',
                'filter_index'  => 'title',
            )
        );
/*        $this->addColumn(
            'ct_title',
            array(
                'header'        => Mage::helper('brander_unitopblog')->__('Comment Title'),
                'index'         => 'ct_title',
                'filter_index'  => 'ct.title',
            )
        );*/
        $this->addColumn(
            'ct_name',
            array(
                'header'        => Mage::helper('brander_unitopblog')->__('Poster name'),
                'index'         => 'ct_name',
                'filter_index'  => 'ct.name',
            )
        );
        if (Mage::helper('brander_unitopblog')->getCfg('brander_unitopblog/post/necessarily_poster_email')) {
            $this->addColumn(
                'ct_email',
                array(
                    'header'        => Mage::helper('brander_unitopblog')->__('Poster email'),
                    'index'         => 'ct_email',
                    'filter_index'  => 'ct.email',
                )
            );
        }

        $this->addColumn(
            'ct_status',
            array(
                'header'        => Mage::helper('brander_unitopblog')->__('Status'),
                'index'         => 'ct_status',
                'filter_index'  => 'ct.status',
                'type'          => 'options',
                'options'       => array(
                    Brander_UnitopBlog_Model_Post_Comment::STATUS_PENDING  =>
                        $this->__('Pending'),
                    Brander_UnitopBlog_Model_Post_Comment::STATUS_APPROVED =>
                        $this->__('Approved'),
                    Brander_UnitopBlog_Model_Post_Comment::STATUS_REJECTED =>
                        $this->__('Rejected'),
                )
            )
        );
        $this->addColumn(
            'ct_created_at',
            array(
                'header'        => Mage::helper('brander_unitopblog')->__('Created at'),
                'index'         => 'ct_created_at',
                'width'         => '120px',
                'type'          => 'datetime',
                'filter_index'  => 'ct.created_at',
            )
        );
        $this->addColumn(
            'ct_updated_at',
            array(
                'header'        => Mage::helper('brander_unitopblog')->__('Updated at'),
                'index'         => 'ct_updated_at',
                'width'         => '120px',
                'type'          => 'datetime',
                'filter_index'  => 'ct.updated_at',
            )
        );
        if (!Mage::app()->isSingleStoreMode() && !$this->_isExport) {
            $this->addColumn(
                'stores',
                array(
                    'header'     => Mage::helper('brander_unitopblog')->__('Store Views'),
                    'index'      => 'stores',
                    'type'       => 'store',
                    'store_all'  => true,
                    'store_view' => true,
                    'sortable'   => false,
                    'filter_condition_callback' => array($this, '_filterStoreCondition'),
                )
            );
        }
        $this->addColumn(
            'action',
            array(
                'header'  => Mage::helper('brander_unitopblog')->__('Action'),
                'width'   => '100',
                'type'    => 'action',
                'getter'  => 'getCtCommentId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('brander_unitopblog')->__('Edit'),
                        'url'     => array('base'=> '*/*/edit'),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'is_system' => true,
                'sortable'  => false,
            )
        );
        $this->addExportType('*/*/exportCsv', Mage::helper('brander_unitopblog')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('brander_unitopblog')->__('Excel'));
        $this->addExportType('*/*/exportXml', Mage::helper('brander_unitopblog')->__('XML'));
        return parent::_prepareColumns();
    }

    /**
     * prepare mass action
     *
     * @access protected
     * @return Brander_UnitopBlog_Block_Adminhtml_Post_Grid

     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('ct_comment_id');
        $this->setMassactionIdFilter('ct.comment_id');
        $this->setMassactionIdFieldOnlyIndexValue(true);
        $this->getMassactionBlock()->setFormFieldName('comment');
        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label'=> Mage::helper('brander_unitopblog')->__('Delete'),
                'url'  => $this->getUrl('*/*/massDelete'),
                'confirm'  => Mage::helper('brander_unitopblog')->__('Are you sure?')
            )
        );
        $this->getMassactionBlock()->addItem(
            'status',
            array(
                'label' => Mage::helper('brander_unitopblog')->__('Change status'),
                'url'   => $this->getUrl('*/*/massStatus', array('_current'=>true)),
                'additional' => array(
                    'status' => array(
                            'name' => 'status',
                            'type' => 'select',
                            'class' => 'required-entry',
                            'label' => Mage::helper('brander_unitopblog')->__('Status'),
                            'values' => array(Brander_UnitopBlog_Model_Post_Comment::STATUS_PENDING  => Mage::helper('brander_unitopblog')->__('Pending'),
                                Brander_UnitopBlog_Model_Post_Comment::STATUS_APPROVED => Mage::helper('brander_unitopblog')->__('Approved'),
                                Brander_UnitopBlog_Model_Post_Comment::STATUS_REJECTED => Mage::helper('brander_unitopblog')->__('Rejected'),
                            )
                    )
                )
            )
        );
        return $this;
    }

    /**
     * get the row url
     *
     * @access public
     * @param Brander_UnitopBlog_Model_Post_Comment
     * @return string

     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getCtCommentId()));
    }

    /**
     * get the grid url
     *
     * @access public
     * @return string

     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    /**
     * filter store column
     *
     * @access protected
     * @param Brander_UnitopBlog_Model_Resource_Post_Comment_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return Brander_UnitopBlog_Block_Adminhtml_Post_Comment_Grid

     */
    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        $collection->setStoreFilter($value);
        return $this;
    }
}
