<?php
class Brander_AdminForms_Block_Block extends Mage_Core_Block_Template
{
    protected function _beforeToHtml()
    {
        $blockKey = $this->getBlockKey();
		if ($blockKey)
		{
			$data = Mage::getResourceModel('adminforms/block')->getEntityInfo($blockKey);

			if(is_array($data))
			{
				$entityType = $data['entity_type'];
				$entityId = $data['entity_id'];
			}
			else
				return;

			$block = Mage::getModel('adminforms/block',array('entity_type'=>$entityType))
				->setStoreId(Mage::app()->getStore()->getId());

			if ($entityId)
				$block->load($entityId);

			$this->setBlock($block);
		}

        return parent::_beforeToHtml();
    }
}