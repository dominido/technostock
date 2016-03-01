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
class Brander_UnitopBlog_Block_Adminhtml_Post_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * constructor
     *
     * @access public

     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('postGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * prepare collection
     *
     * @access protected
     * @return Brander_UnitopBlog_Block_Adminhtml_Post_Grid

     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('brander_unitopblog/post')
            ->getCollection()
            ->addAttributeToSelect('postscategory_id')
            ->addAttributeToSelect('post_date')
            ->addAttributeToSelect('author')
            ->addAttributeToSelect('show_on_homepage')
            ->addAttributeToSelect('new_from')
            ->addAttributeToSelect('new_before')
            ->addAttributeToSelect('archived')
            ->addAttributeToSelect('status')
            ->addAttributeToSelect('url_key');
        
        $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
        $store = $this->_getStore();

        if ($store->getId()) {
            $collection->joinAttribute(
                'brander_unitopblog_post_title',
                'brander_unitopblog_post/title', 
                'entity_id', 
                null, 
                'inner', 
                $store->getId()
            );

            $collection->joinAttribute(
                'status',
                'brander_unitopblog_post/status',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );

        } else {
            $collection->joinAttribute(
                'title',
                'brander_unitopblog_post/title',
                'entity_id',
                null,
                'inner',
                $adminStore
            );
        }



        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare grid collection
     *
     * @access protected
     * @return Brander_UnitopBlog_Block_Adminhtml_Post_Grid

     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            array(
                'header' => Mage::helper('brander_unitopblog')->__('Id'),
                'index'  => 'entity_id',
                'type'   => 'number'
            )
        );
        $this->addColumn(
            'postscategory_id',
            array(
                'header'    => Mage::helper('brander_unitopblog')->__('Post Category'),
                'index'     => 'postscategory_id',
                'type'      => 'options',
                'options'   => Mage::getResourceModel('brander_unitopblog/postscategory_collection')
                    ->addAttributeToSelect('title')->toOptionHash(),
                'renderer'  => 'brander_unitopblog/adminhtml_helper_column_renderer_parent',
                'params'    => array(
                    'id'    => 'getPostscategoryId'
                ),
                'static' => array(
                    'clear' => 1
                ),
                'base_link' => 'adminhtml/unitopblog_postscategory/edit'
            )
        );

        
        if ($this->_getStore()->getId()) {
            $this->addColumn(
                'brander_unitopblog_post_title', 
                array(
                    'header'    => Mage::helper('brander_unitopblog')->__('Title in %s', $this->_getStore()->getName()),
                    'align'     => 'left',
                    'index'     => 'brander_unitopblog_post_title',
                )
            );
        } else {
            $this->addColumn(
                'title',
                array(
                    'header'    => Mage::helper('brander_unitopblog')->__('Title'),
                    'align'     => 'left',
                    'index'     => 'title',
                )
            );
        }

        $this->addColumn(
            'post_date',
            array(
                'header' => Mage::helper('brander_unitopblog')->__('Post Date'),
                'index'  => 'post_date',
                'type'=> 'date',

            )
        );

        // TODO :: FIX AUTHOR
        $this->addColumn(
            'manual_author_name',
            array(
                'header' => Mage::helper('brander_unitopblog')->__('Author'),
                'index'  => 'manual_author_name',
/*                'type'  => 'options',
                'options' => Mage::helper('brander_unitopblog')->convertOptions(
                    Mage::getModel('eav/config')->getAttribute('brander_unitopblog_post', 'author')->getSource()->getAllOptions(false)
                )*/

            )
        );
        $this->addColumn(
            'show_on_homepage',
            array(
                'header' => Mage::helper('brander_unitopblog')->__('Show on Homepage'),
                'index'  => 'show_on_homepage',
                'type'    => 'options',
                    'options'    => array(
                    '1' => Mage::helper('brander_unitopblog')->__('Yes'),
                    '0' => Mage::helper('brander_unitopblog')->__('No'),
                )

            )
        );

/*        $this->addColumn(
            'archived',
            array(
                'header' => Mage::helper('brander_unitopblog')->__('Archived'),
                'index'  => 'archived',
                'type'    => 'options',
                    'options'    => array(
                    '1' => Mage::helper('brander_unitopblog')->__('Yes'),
                    '0' => Mage::helper('brander_unitopblog')->__('No'),
                )

            )
        );*/

        $this->addColumn(
            'created_at',
            array(
                'header' => Mage::helper('brander_unitopblog')->__('Created at'),
                'index'  => 'created_at',
                'width'  => '120px',
                'type'   => 'datetime',
            )
        );

        $this->addColumn(
            'status',
            array(
                'header'  => Mage::helper('brander_unitopblog')->__('Status'),
                'index'   => 'status',
                'type'    => 'options',
                'options' => array(
                    '1' => Mage::helper('brander_unitopblog')->__('Enabled'),
                    '0' => Mage::helper('brander_unitopblog')->__('Disabled'),
                )
            )
        );
        $this->addColumn(
            'action',
            array(
                'header'  =>  Mage::helper('brander_unitopblog')->__('Action'),
                'width'   => '100',
                'type'    => 'action',
                'getter'  => 'getId',
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
     * get the selected store
     *
     * @access protected
     * @return Mage_Core_Model_Store

     */
    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    /**
     * prepare mass action
     *
     * @access protected
     * @return Brander_UnitopBlog_Block_Adminhtml_Post_Grid

     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('post');
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
                'label'      => Mage::helper('brander_unitopblog')->__('Change status'),
                'url'        => $this->getUrl('*/*/massStatus', array('_current'=>true)),
                'additional' => array(
                    'status' => array(
                        'name'   => 'status',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('brander_unitopblog')->__('Status'),
                        'values' => array(
                            '1' => Mage::helper('brander_unitopblog')->__('Enabled'),
                            '0' => Mage::helper('brander_unitopblog')->__('Disabled'),
                        )
                    )
                )
            )
        );

        //TODO :: fix author
        $this->getMassactionBlock()->addItem(
            'author',
            array(
                'label'      => Mage::helper('brander_unitopblog')->__('Change Author'),
                'url'        => $this->getUrl('*/*/massAuthor', array('_current'=>true)),
                'additional' => array(
                    'flag_author' => array(
                        'name'   => 'flag_author',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('brander_unitopblog')->__('Author'),
                        'values' => Mage::getModel('eav/config')->getAttribute('brander_unitopblog_post', 'author')
                            ->getSource()->getAllOptions(true),

                    )
                )
            )
        );
        $this->getMassactionBlock()->addItem(
            'show_on_homepage',
            array(
                'label'      => Mage::helper('brander_unitopblog')->__('Change Show on Homepage'),
                'url'        => $this->getUrl('*/*/massShowOnHomepage', array('_current'=>true)),
                'additional' => array(
                    'flag_show_on_homepage' => array(
                        'name'   => 'flag_show_on_homepage',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('brander_unitopblog')->__('Show on Homepage'),
                        'values' => array(
                                '1' => Mage::helper('brander_unitopblog')->__('Yes'),
                                '0' => Mage::helper('brander_unitopblog')->__('No'),
                            )

                    )
                )
            )
        );
        $this->getMassactionBlock()->addItem(
            'archived',
            array(
                'label'      => Mage::helper('brander_unitopblog')->__('Change Archived'),
                'url'        => $this->getUrl('*/*/massArchived', array('_current'=>true)),
                'additional' => array(
                    'flag_archived' => array(
                        'name'   => 'flag_archived',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('brander_unitopblog')->__('Archived'),
                        'values' => array(
                                '1' => Mage::helper('brander_unitopblog')->__('Yes'),
                                '0' => Mage::helper('brander_unitopblog')->__('No'),
                            )

                    )
                )
            )
        );
        $values = Mage::getResourceModel('brander_unitopblog/postscategory_collection')->toOptionHash();
        $values = array_reverse($values, true);
        $values[''] = '';
        $values = array_reverse($values, true);
        $this->getMassactionBlock()->addItem(
            'postscategory_id',
            array(
                'label'      => Mage::helper('brander_unitopblog')->__('Change Post Category'),
                'url'        => $this->getUrl('*/*/massPostscategoryId', array('_current'=>true)),
                'additional' => array(
                    'flag_postscategory_id' => array(
                        'name'   => 'flag_postscategory_id',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('brander_unitopblog')->__('Post Category'),
                        'values' => $values
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
     * @param Brander_UnitopBlog_Model_Post
     * @return string

     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
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
}
