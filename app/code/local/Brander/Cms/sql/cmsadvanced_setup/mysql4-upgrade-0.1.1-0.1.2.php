<?php
$installer = $this;

Mage::getModel('cmsadvanced/page')->setData(array(
    'name' => 'Absolute Root',
    'content' => 'This is root page'
))->save();

Mage::getModel('cmsadvanced/page')->setData(array(
    'name' => 'Root',
    'content' => 'This is root page',
    'status' => 1,
    'path' => 1
))->save();


