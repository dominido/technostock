<?php
class Brander_Seo_Model_System_Config_Source_Stores
{
	public function toOptionArray($addEmpty = true) {
		$options = array();

		foreach (Mage::app()->getStores() as $storeView) {
			$options[] = array(
				'label' => $storeView->getName(),
				'value' => $storeView->getCode()
			);
		}

		return $options;
	}
}