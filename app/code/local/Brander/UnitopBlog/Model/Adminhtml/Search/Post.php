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
class Brander_UnitopBlog_Model_Adminhtml_Search_Post extends Varien_Object
{
    /**
     * Load search results
     *
     * @access public
     * @return Brander_UnitopBlog_Model_Adminhtml_Search_Post

     */
    public function load()
    {
        $arr = array();
        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }
        $collection = Mage::getResourceModel('brander_unitopblog/post_collection')
            ->addAttributeToFilter('title', array('like' => $this->getQuery().'%'))
            ->setCurPage($this->getStart())
            ->setPageSize($this->getLimit())
            ->load();
        foreach ($collection->getItems() as $post) {
            $arr[] = array(
                'id'          => 'post/1/'.$post->getId(),
                'type'        => Mage::helper('brander_unitopblog')->__('Post'),
                'name'        => $post->getTitle(),
                'description' => $post->getTitle(),
                'url' => Mage::helper('adminhtml')->getUrl(
                    '*/unitopblog_post/edit',
                    array('id'=>$post->getId())
                ),
            );
        }
        $this->setResults($arr);
        return $this;
    }
}
