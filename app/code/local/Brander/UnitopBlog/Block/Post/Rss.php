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
class Brander_UnitopBlog_Block_Post_Rss extends Mage_Rss_Block_Abstract
{
    /**
     * Cache tag constant for feed reviews
     *
     * @var string
     */
    const CACHE_TAG = 'block_html_unitopblog_post_rss';

    /**
     * constructor
     *
     * @access protected
     * @return void

     */
    protected function _construct()
    {
        $this->setCacheTags(array(self::CACHE_TAG));
        /*
         * setting cache to save the rss for 10 minutes
         */
        $this->setCacheKey('brander_unitopblog_post_rss');
        $this->setCacheLifetime(600);
    }

    /**
     * toHtml method
     *
     * @access protected
     * @return string

     */
    protected function _toHtml()
    {
        $url    = Mage::helper('brander_unitopblog/post')->getPostsUrl();
        $title  = Mage::helper('brander_unitopblog')->__('Posts');
        $rssObj = Mage::getModel('rss/rss');
        $data  = array(
            'title'       => $title,
            'description' => $title,
            'link'        => $url,
            'charset'     => 'UTF-8',
        );
        $rssObj->_addHeader($data);
        $collection = Mage::getModel('brander_unitopblog/post')->getCollection()
            ->addFieldToFilter('status', 1)
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('in_rss', 1)
            ->setOrder('created_at');
        $collection->load();
        foreach ($collection as $item) {
            $description = '<p>';
            $description .= '<div>'.
                Mage::helper('brander_unitopblog')->__('Title').': 
                '.$item->getTitle().
                '</div>';
            $description .= '<div>'.Mage::helper('brander_unitopblog')->__('Post Date').': '.Mage::helper('core')->formatDate($item->getPostDate(), 'full').'</div>';
            $description .= '<div>'.
                Mage::helper('brander_unitopblog')->__("Author").': '
                .$item->getAttributeText('author').
                '</div>';
            $description .= '<div>'.
                Mage::helper('brander_unitopblog')->__('Manual Author Name').': 
                '.$item->getManualAuthorName().
                '</div>';
            if ($item->getPreviewImage()) {
                $description .= '<div>';
                $description .= Mage::helper('brander_unitopblog')->__('Preview Image');
                $description .= '<img src="'.Mage::helper('brander_unitopblog/post_image')->init($item, 'preview_image')->resize(75).'" alt="'.$this->escapeHtml($item->getTitle()).'" />';
                $description .= '</div>';
            }
            $description .= '<div>'.
                Mage::helper('brander_unitopblog')->__('Preview Content').': 
                '.$item->getPreviewContent().
                '</div>';
            $description .= '</p>';
            $data = array(
                'title'       => $item->getTitle(),
                'link'        => $item->getPostUrl(),
                'description' => $description
            );
            $rssObj->_addEntry($data);
        }
        return $rssObj->createRssXml();
    }
}
