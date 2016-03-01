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
class Brander_UnitopBlog_Block_Post_List extends Mage_Core_Block_Template
{

    protected $_isShowOnHomepage = false;
    static $_currentBlogCategoryName = null;
    static $_currentProductName = null;
    static $_currentCategoryName = null;

    /**
     * initialize
     *
     * @access public

     */
    public function __construct()
    {
        parent::__construct();

        $params = new Varien_Object($this->getRequest()->getParams());
        $posts = Mage::getResourceModel('brander_unitopblog/post_collection')
                         ->setStoreId(Mage::app()->getStore()->getId())
                         ->addAttributeToSelect('*')
                         ->addAttributeToFilter('status', 1);
        if ($this->getIsHomePage()) {
            $homepageConfig = Mage::helper('brander_shop')->getCfg('brander_unitopblog/homepage');
            if (Mage::helper('brander_shop')->getCfg('brander_unitopblog/homepage/enable')) {
                $posts->addAttributeToFilter('show_on_homepage', 1);
                $postsNum = $homepageConfig->getPostsLimit();
                if ($homepageConfig->getAttachNewsletters()) {
                    $postsNum = $postsNum - 1;
                }
                $posts->setPageSize($postsNum)->setCurPage(1);
            } else {
                $posts = Mage::getModel('brander_unitopblog/post');
            }
        } elseif ($params->getCategory() && $params->getBlogCategory()) {
            $category = Mage::getModel('catalog/category')->load($params->getCategory());
            self::$_currentCategoryName = $category->getName();
            $blogCategory = Mage::helper('brander_unitopblog/postscategory')->getActiveBlogCategory($params->getBlogCategory());
            if ($id = $blogCategory->getEntityId()) {
                self::$_currentBlogCategoryName = $blogCategory->getTitle();
                $posts->addAttributeToFilter('postscategory_id', $id)->addCategoryFilter($category);
            }
        } elseif ($params->getProduct() && $params->getBlogCategory()) {
            $product = Mage::getModel('catalog/product')->load($params->getProduct());
            self::$_currentProductName = $product->getName();
            $blogCategory = Mage::helper('brander_unitopblog/postscategory')->getActiveBlogCategory($params->getBlogCategory());
            if ($id = $blogCategory->getEntityId()) {
                self::$_currentBlogCategoryName = $blogCategory->getTitle();
                $posts->addAttributeToFilter('postscategory_id', $id)->addProductFilter($product);
            }
        } elseif ($params->getBlogCategory() && !$params->getTab()) {
            $category = Mage::helper('brander_unitopblog/postscategory')->getActiveBlogCategory($params->getBlogCategory());
            if ($category) {
                $posts->addAttributeToFilter('postscategory_id', $category->getEntityId());
            }
        }

        $posts->setOrder('archived', 'asc');
        $posts->setOrder('post_date', 'desc');

        $this->setPosts($posts);
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Brander_UnitopBlog_Block_Post_List

     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getIsHomePage()) {
            $this->getPosts()->load();
            return $this;
        }
        $pager = $this->getLayout()->createBlock(
            'page/html_pager',
            'brander_unitopblog.post.html.pager')
            ->setCollection($this->getPosts());
        $this->setChild('pager', $pager);
        $this->getPosts()->load();

        return $this;
    }

    /**
     * get the pager html
     *
     * @access public
     * @return string

     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getIsHomePage()
    {
        if($handles = Mage::getSingleton('core/layout')->getUpdate()->getHandles()) {
            if (in_array("PAGE_TYPE_UNIPAGETYPE", $handles)) {
                return true;
            }
        }
        return false;
    }

    public function getTitle()
    {

        if (!$postsTitle = Mage::helper('brander_unitopblog/postscategory')->getActiveCategoryTitle()) {
            $postsTitle = Mage::helper('brander_unitopblog')->__('posts');
        }

        if ($this->getIsHomePage()) {
            return Mage::helper('brander_shop')->getCfg('brander_unitopblog/homepage/posts_block_title');
        } elseif (self::$_currentProductName) {
            return Mage::helper('brander_unitopblog')->__('All %s for %s', $postsTitle, self::$_currentProductName);
        } elseif (self::$_currentCategoryName) {
            return Mage::helper('brander_unitopblog')->__('All %s for category "%s"', $postsTitle, self::$_currentCategoryName);
        } elseif ($title = Mage::helper('brander_unitopblog/postscategory')->getActiveCategoryTitle()) {
            return $title;
        } else {
            return Mage::helper('brander_unitopblog')->__('All Posts');
        }
    }

    public function getShortContent($post)
    {
        $shortContent = $post->getPreviewContent();
        if (!$shortContent) {
            return '';
        }

        $config = Mage::helper('brander_shop')->getCfg('brander_unitopblog/post');
        $shortContentMaxLength = $config->getShortContentLimit();
        $length = strlen($shortContent);
        if ($length > $shortContentMaxLength) {
            $shortContent = mb_substr($shortContent, 0, $shortContentMaxLength, 'UTF-8').' ...';
        }

        return $shortContent;
    }


}
