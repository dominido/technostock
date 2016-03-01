<?php
class Brander_Cms_Model_Config_Source_Pagetype extends Brander_AdminForms_Model_Config_Source_Options
{
    /**
     * @return array
     *      array(
     *          array('value' => value, 'label' => label)
     *      )
     */
    public function toOptionArray()
    {
        $pageTypes = Mage::getModel('cmsadvanced/config')->getPageTypes();
        $helper = Mage::helper('cmsadvanced');

        $options = array();
        foreach ($pageTypes as $pageType => $params) {
            if (!empty($params['name'])) {
                $label = $params['name'];
            } else {
                $label = $helper->getNameFromCode($pageType);
            }
            if ($pageType=='default')
                array_unshift($options,array('value' => $pageType, 'label' => $label));
            else
                $options[] = array('value' => $pageType, 'label' => $label);
        }

        return $options;
    }
}
