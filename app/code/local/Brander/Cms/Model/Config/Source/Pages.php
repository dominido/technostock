<?php
class Brander_Cms_Model_Config_Source_Pages extends Brander_AdminForms_Model_Config_Source_Options
{
    /**
     * @return array
     *      array(
     *          array('value' => value, 'label' => label)
     *      )
     */
    public function toOptionArray()
    {
        $currentPageId = Mage::helper('cmsadvanced')->getCurrentPage()->getId();
        $parameters = array('name','url_key');
        $pages = Mage::getModel('cmsadvanced/page')->getCollection()
					->addAttributeToSelect($parameters)
					->setOrder('entity_id', 'asc')
					->addFieldToFilter('parent_id',array('nin'=> array(0,1)))
					->addFieldToFilter('entity_id',array('nin'=> array($currentPageId)));

        $options = array();
        $options[] = array('value' => 0, 'label' => 'Please Select');
        $options[] = array('value' => 1, 'label' => '-- First Children Page --');
        foreach ($pages as $page) {
            $options[] = array('value' => $page->getId(), 'label' => $page->getName());
        }

        return $options;
    }
}
