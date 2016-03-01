<?php
$pages = Mage::getResourceModel('cmsadvanced/page_collection')->addAttributeToSelect('*');

foreach ($pages as $page) {
    $page->save();
}
