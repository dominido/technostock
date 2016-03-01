<?php
/*class Brander_Seo_Block_Catalog_Breadcrumbs extends Mage_Catalog_Block_Breadcrumbs
{
	protected function _prepareLayout()
	{
		if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
			$breadcrumbsBlock->addCrumb('home', array(
				'label'=>Mage::helper('catalog')->__('Home'),
				'title'=>Mage::helper('catalog')->__('Go to Home Page'),
				'link'=>Mage::getBaseUrl()
			));

			$title = array();
			$path  = Mage::helper('catalog')->getBreadcrumbPath();

			foreach ($path as $name => $breadcrumb) {
				$breadcrumbsBlock->addCrumb($name, $breadcrumb);
				$title[] = $breadcrumb['label'];
			}

			if (($headBlock = $this->getLayout()->getBlock('head')) && $product = $this->getProduct()) {
				$customTitle = $product->getName() . ' ' . Mage::getStoreConfig('catalog/seo/product_view_meta_title_suffix');

				$title = $product->getMetaTitle();
				if ($title) {
					$customTitle = $customTitle . ' ' . $title;
				}

				$headBlock->setTitle($customTitle);
			}
		}
	}

	public function getProduct()
	{
		return Mage::registry('product');
	}
}*/