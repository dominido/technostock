<?php
$installer = $this;

//update url_path for all exists cms pages
/*$cmsPages = Mage::getResourceModel('cmsadvanced/page_collection')->addAttributeToSelect(array('url_key'))->setStoreId(0);

foreach ($cmsPages as $cmsPage) {
    $cmsPage->setUrlPath($cmsPage->getUrlKey());
    $cmsPage->getResource()->saveAttribute($cmsPage, 'url_path');
}*/


