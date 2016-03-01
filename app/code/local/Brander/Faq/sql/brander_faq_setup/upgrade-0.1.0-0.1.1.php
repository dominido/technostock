<?php
$installer = $this;
$installer->startSetup();
$catalogEntity = Mage_Catalog_Model_Product::ENTITY;
$attrCode = 'faq_questions';
if (!$installer->getAttributeId($catalogEntity, $attrCode)) {
    $this->addAttribute($catalogEntity,
            $attrCode,
            array (
                'group' => 'Faq',
                'label' => 'Faq questions',
                'type' => 'varchar',
                'input' => 'multiselect',
                'backend' => 'eav/entity_attribute_backend_array',
                'source' => 'brander_faq/system_config_source_faqquestions',
                'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
            )
    );
}

$installer->endSetup();