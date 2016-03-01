<?php
class Brander_AdminForms_Model_Observer
{
    public function prepareWysiwygPluginConfig(Varien_Event_Observer $observer)
    {
        $config = $observer->getEvent()->getConfig();

		$config->addData(array(
			'files_browser_window_url'      => Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg_images/index'),
			'directives_url'                => Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive'),
			'add_directives'                => true
		));
        return $this;
    }

}
