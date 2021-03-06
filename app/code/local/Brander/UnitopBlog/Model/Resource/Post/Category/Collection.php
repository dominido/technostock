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
class Brander_UnitopBlog_Model_Resource_Post_Category_Collection extends Mage_Catalog_Model_Resource_Category_Collection
{
    /**
     * remember if fields have been joined
     *
     * @var bool
     */
    protected $_joinedFields = false;

    /**
     * join the link table
     *
     * @access public
     * @return Brander_UnitopBlog_Model_Resource_Post_Category_Collection

     */
    public function joinFields()
    {
        if (!$this->_joinedFields) {
            $this->getSelect()->join(
                array('related' => $this->getTable('brander_unitopblog/post_category')),
                'related.category_id = e.entity_id',
                array('position')
            );
            $this->_joinedFields = true;
        }
        return $this;
    }

    /**
     * add post filter
     *
     * @access public
     * @param Brander_UnitopBlog_Model_Post | int $post
     * @return Brander_UnitopBlog_Model_Resource_Post_Category_Collection

     */
    public function addPostFilter($post)
    {
        if ($post instanceof Brander_UnitopBlog_Model_Post) {
            $post = $post->getId();
        }
        if (!$this->_joinedFields) {
            $this->joinFields();
        }
        $this->getSelect()->where('related.post_id = ?', $post);
        return $this;
    }
}
