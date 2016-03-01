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
class Brander_UnitopBlog_Model_Resource_Postscategory_Collection extends Mage_Catalog_Model_Resource_Collection_Abstract
{
    protected $_joinedFields = array();

    /**
     * constructor
     *
     * @access public
     * @return void

     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('brander_unitopblog/postscategory');
    }

    /**
     * Add Id filter
     *
     * @access public
     * @param array $postscategoryIds
     * @return Brander_UnitopBlog_Model_Resource_Postscategory_Collection

     */
    public function addIdFilter($postscategoryIds)
    {
        if (is_array($postscategoryIds)) {
            if (empty($postscategoryIds)) {
                $condition = '';
            } else {
                $condition = array('in' => $postscategoryIds);
            }
        } elseif (is_numeric($postscategoryIds)) {
            $condition = $postscategoryIds;
        } elseif (is_string($postscategoryIds)) {
            $ids = explode(',', $postscategoryIds);
            if (empty($ids)) {
                $condition = $postscategoryIds;
            } else {
                $condition = array('in' => $ids);
            }
        }
        $this->addFieldToFilter('entity_id', $condition);
        return $this;
    }

    /**
     * Add post category path filter
     *
     * @access public
     * @param string $regexp
     * @return Brander_UnitopBlog_Model_Resource_Postscategory_Collection

     */
    public function addPathFilter($regexp)
    {
        $this->addFieldToFilter('path', array('regexp' => $regexp));
        return $this;
    }

    /**
     * Add post category path filter
     *
     * @access public
     * @param array|string $paths
     * @return Brander_UnitopBlog_Model_Resource_Postscategory_Collection

     */
    public function addPathsFilter($paths)
    {
        if (!is_array($paths)) {
            $paths = array($paths);
        }
        $write  = $this->getResource()->getWriteConnection();
        $cond   = array();
        foreach ($paths as $path) {
            $cond[] = $write->quoteInto('e.path LIKE ?', "$path%");
        }
        if ($cond) {
            $this->getSelect()->where(join(' OR ', $cond));
        }
        return $this;
    }

    /**
     * Add post category level filter
     *
     * @access public
     * @param int|string $level
     * @return Brander_UnitopBlog_Model_Resource_Postscategory_Collection

     */
    public function addLevelFilter($level)
    {
        $this->addFieldToFilter('level', array('lteq' => $level));
        return $this;
    }

    /**
     * Add root post category filter
     *
     * @access public
     * @return Brander_UnitopBlog_Model_Resource_Postscategory_Collection
     */
    public function addRootLevelFilter()
    {
        $this->addFieldToFilter('path', array('neq' => '1'));
        $this->addLevelFilter(1);
        return $this;
    }

    /**
     * Add order field
     *
     * @access public
     * @param string $field
     * @return Brander_UnitopBlog_Model_Resource_Postscategory_Collection
     */
    public function addOrderField($field)
    {
        $this->setOrder($field, self::SORT_ORDER_ASC);
        return $this;
    }

    /**
     * Add active post category filter
     *
     * @access public
     * @return Brander_UnitopBlog_Model_Resource_Postscategory_Collection
     */
    public function addStatusFilter($status = 1)
    {
        $this->addFieldToFilter('status', $status);
        return $this;
    }

    /**
     * get post categories as array
     *
     * @access protected
     * @param string $valueField
     * @param string $labelField
     * @param array $additional
     * @return array

     */
    protected function _toOptionArray($valueField='entity_id', $labelField='title', $additional=array())
    {
        $res = array();
        $additional['value'] = $valueField;
        $additional['label'] = $labelField;

        foreach ($this as $item) {
            if ($item->getId() == Mage::helper('brander_unitopblog/postscategory')->getRootPostscategoryId()) {
                continue;
            }
            foreach ($additional as $code => $field) {
                $data[$code] = $item->getData($field);
            }
            $res[] = $data;
        }
        return $res;
    }

    /**
     * get options hash
     *
     * @access protected
     * @param string $valueField
     * @param string $labelField
     * @return array

     */
    protected function _toOptionHash($valueField='entity_id', $labelField='title')
    {
        $res = array();
        foreach ($this as $item) {
            if ($item->getId() == Mage::helper('brander_unitopblog/postscategory')->getRootPostscategoryId()) {
                continue;
            }
            $res[$item->getData($valueField)] = $item->getData($labelField);
        }
        return $res;
    }

    /**
     * Get SQL for get record count.
     * Extra GROUP BY strip added.
     *
     * @access public
     * @return Varien_Db_Select

     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(Zend_Db_Select::GROUP);
        return $countSelect;
    }
}
