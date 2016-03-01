<?php
class Brander_Core_Model_Observer
{
	public function prepareLayoutBefore(Varien_Event_Observer $observer)
	{
		/* @var $block Mage_Page_Block_Html_Head */
		$block = $observer->getEvent()->getBlock();

		if ("head" == $block->getNameInLayout()) {
			foreach (Mage::helper('brander_core')->getFilesJs() as $file) {
				$block->addJs(Mage::helper('brander_core')->getJQueryPath($file));
			}

			foreach (Mage::helper('brander_core')->getFilesCss() as $file) {
				$block->addCss(Mage::helper('brander_core')->getCssPath($file));
			}
		}

		return $this;
	}
}