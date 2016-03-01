<?php
$installer = $this;

$installer->installEntities();

$installer->createEntityTables($this->getTable('cmsadvanced/page'));


