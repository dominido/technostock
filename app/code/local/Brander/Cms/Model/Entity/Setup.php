<?php
class Brander_Cms_Model_Entity_Setup extends Brander_AdminForms_Model_Entity_Setup
{
    public function getDefaultEntities()
    {
        return array(
            'cms_page' => array(
                'entity_model'     => 'cmsadvanced/page',
                'attribute_model'  => 'cmsadvanced/entity_attribute',
                'table' => 'cmsadvanced/page',
                'additional_attribute_table' => 'adminforms/eav_attribute',
                'attributes'   => array()
            )
        );
    }

    protected function _prepareBuilder($builder)
    {
        $this->_appendSetupTemplates($builder);
        return $this;
    }
    
    public function processBuilder($builder)
    {
        parent::processBuilder($builder);

        //prepare page types
        if (!Mage::registry('is_cmsadvanced_config_updated')) {
            $entity = $this->_prepareDefaultEntity();
            $builder = $this->getBuilder();
            
            $this->_appendSetupTemplates($builder);
            
            $builder->setArray($entity);
            parent::processBuilder($builder);
            
            Mage::register('is_cmsadvanced_config_updated', true);
        }
    }

    protected function _appendSetupTemplates($builder)
    {
        $config = Mage::getModel('cmsadvanced/config');
        $setupTemplates = $config->getSetupTemplates();

        $builder->addTemplates($setupTemplates);
        return $this;
    }

    protected function _prepareDefaultEntity()
    {
        $config = Mage::getModel('cmsadvanced/config');
        $pageTypes = $config->getRawPageTypes();

        $defaultEntityType = $config->getDefaultEntityType();

        $helper = Mage::helper('cmsadvanced');

        $groups = array();

        foreach ($pageTypes as $pageType) {
            if (isset($pageType['groups'])) {
                $groups = $helper->mergeArray($groups, $pageType['groups']);
            }
        }

        $entity = array(
            $defaultEntityType => array(
                'groups' => $groups
            )
        );

        return $entity;
    }
} 
