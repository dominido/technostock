<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * EAV Entity Setup Model
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Brander_CommerceML_Model_Import_Product_Attribute_Setup extends Mage_Eav_Model_Entity_Setup
{

    public function addAttribute($entityTypeId, $code, array $attr, $groups)
    {
        $entityTypeId = $this->getEntityTypeId($entityTypeId);
        $data = array_merge(
            array(
                'entity_type_id' => $entityTypeId,
                'attribute_code' => $code
            ),
            $this->_prepareValues($attr)
        );

        $this->_validateAttributeData($data);

        $sortOrder = isset($attr['sort_order']) ? $attr['sort_order'] : null;
        $attributeId = $this->getAttribute($entityTypeId, $code, 'attribute_id');
        if ($attributeId) {
            $this->updateAttribute($entityTypeId, $attributeId, $data, null, $sortOrder);
        } else {
            $this->_insertAttribute($data);
        }

        // alexvegas fix - add attribute to special attributeset
        if (!empty($attr['group']) || empty($attr['user_defined'])) {
            $select = $this->_conn->select()
                ->from($this->getTable('eav/attribute_set'))
                ->where('entity_type_id = :entity_type_id');
            $sets = $this->_conn->fetchAll($select, array('entity_type_id' => $entityTypeId));
            foreach ($sets as $set) {
                if (!empty($attr['group']) && in_array($set['attribute_set_name'], $groups)) {
                    $this->addAttributeGroup($entityTypeId, $set['attribute_set_id'],
                        $set['attribute_set_name']);
                    $this->addAttributeToSet($entityTypeId, $set['attribute_set_id'],
                        $set['attribute_set_name'], $code, $sortOrder);
                }
            }
        }

        if (isset($attr['option']) && is_array($attr['option'])) {
            $option = $attr['option'];
            $option['attribute_id'] = $this->getAttributeId($entityTypeId, $code);
            $this->addAttributeOption($option);
        }

        return $this;
    }

    /**
     * Add attribute to an entity type
     *
     * If attribute is system will add to all existing attribute sets
     *
     * @param string|integer $entityTypeId
     * @param string $code
     * @param array $attr
     * @return Mage_Eav_Model_Entity_Setup
     */
    public function updateAttributeGroups($entityTypeId, $code, array $groups)
    {
        $entityTypeId = $this->getEntityTypeId($entityTypeId);
        $data = array_merge(
            array(
                'entity_type_id' => $entityTypeId,
                'attribute_code' => $code
            )
        );

        $this->_validateAttributeData($data);

        $sortOrder = isset($attr['sort_order']) ? $attr['sort_order'] : null;
        $attributeId = $this->getAttribute($entityTypeId, $code, 'attribute_id');
        
        
        // alexvegas fix - add attribute to special attributeset
        if (!empty($groups)) {
            $select = $this->_conn->select()
                ->from($this->getTable('eav/attribute_set'))
                ->where('entity_type_id = :entity_type_id');
            $sets = $this->_conn->fetchAll($select, array('entity_type_id' => $entityTypeId));
            foreach ($sets as $set) {
                if (!empty($groups) && in_array($set['attribute_set_name'], $groups)) {
                    $this->addAttributeGroup($entityTypeId, $set['attribute_set_id'],
                        $set['attribute_set_name']);
                    $this->addAttributeToSet($entityTypeId, $set['attribute_set_id'],
                        $set['attribute_set_name'], $code, $sortOrder);
                }
            }
        }
    }
}