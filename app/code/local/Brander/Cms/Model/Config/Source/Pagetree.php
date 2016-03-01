<?php
class Brander_Cms_Model_Config_Source_Pagetree extends Brander_AdminForms_Model_Config_Source_Options
{
    /**
     * @return array
     *      array(
     *          array('value' => value, 'label' => label)
     *      )
     */
    public function toOptionArray()
    {
        $pages = Mage::getModel('cmsadvanced/page')->getCollection()
					->addAttributeToSelect(array('name'))
                    ->setStoreId(Mage::app()->getStore()->getId())
                    //->addActiveFilter()
					;

        $tree = Mage::getResourceModel('cmsadvanced/page_tree')->load(null, 0);
        $tree->addCollectionData($pages, true, array(), true);

        $root = $tree->getNodeById(1);

        $options = array(array(
            'value' => '',
            'label' => '--Please Select--'
        ));
        
        $options = array_merge($options, $this->_getOptionByNode($root, '', true));

        return $options;
    }

    protected function _getOptionByNode($node, $prefix = '', $excludeCurrent = false)
    {
        $options = array();
        
        if (!$excludeCurrent) {
            $options[] = array(
                'value' => $node->getId(),
                'label' => $prefix . $node->getName()
            );
        }

        if ($node->hasChildren()) {
            if (!$excludeCurrent) {
                $prefix .= '- ';
            }
            
            foreach ($node->getChildren() as $child) {
                $options = array_merge($options, $this->_getOptionByNode($child, $prefix));
            }
        }

        return $options;
    }
}
