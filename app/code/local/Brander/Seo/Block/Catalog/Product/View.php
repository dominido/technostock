<?php
class Brander_Seo_Block_Catalog_Product_View extends Mage_Catalog_Block_Product_View
{
	protected function _prepareLayout()
	{
		$this->getLayout()->createBlock('catalog/breadcrumbs');
		$headBlock = $this->getLayout()->getBlock('head');
		if ($headBlock) {
			$product = $this->getProduct();

            $productName = $product->getName();
            $productAlias = '{product_name}';

			$productMetaTitleSuffix = Mage::getStoreConfig('catalog/seo/product_view_meta_title_suffix');
            if (stristr($productMetaTitleSuffix, $productAlias)) {
                $productMetaTitleSuffix = str_replace($productAlias, $productName, $productMetaTitleSuffix);
            }
			$productMetaKeywordSuffix = Mage::getStoreConfig('catalog/seo/product_view_meta_keywords_suffix');
            if (stristr($productMetaKeywordSuffix, $productAlias)) {
                $productMetaKeywordSuffix = str_replace($productAlias, $productName, $productMetaKeywordSuffix);
            }
			$productMetaDescriptionSuffix = Mage::getStoreConfig('catalog/seo/product_view_meta_descriptions_suffix');
            if (stristr($productMetaDescriptionSuffix, $productAlias)) {
                $productMetaDescriptionSuffix = str_replace($productAlias, $productName, $productMetaDescriptionSuffix);
            }


			$customTitle = $productMetaTitleSuffix;
			$title = $product->getMetaTitle();
			if ($title) {
                $customTitle = $title;
            } else {
				$customTitle = $productMetaTitleSuffix;
			}
			$headBlock->setTitle($customTitle);

			$customKeywords = $product->getName();
			if($productMetaKeywordSuffix) {
				$customKeywords = $productMetaKeywordSuffix;
			}
			$keyword = $product->getMetaKeyword();
			if ($keyword) {
				$customKeywords = $keyword;
			}
			$headBlock->setKeywords($customKeywords);

			$customDescription = $product->getName();
			if($productMetaDescriptionSuffix) {
				$customDescription = $productMetaDescriptionSuffix;
			}

			$description = $product->getMetaDescription();
			if ($description) {
				$customDescription = $description;
			}
			$headBlock->setDescription($customDescription);

			if ($this->helper('catalog/product')->canUseCanonicalTag()) {
				$params = array('_ignore_category'=>true);
				$headBlock->addLinkRel('canonical', $product->getUrlModel()->getUrl($product, $params));
			}
		}

		$block = $this->getLayout()->getBlock('catalog_product_price_template');
		if ($block) {
			foreach ($block->getPriceBlockTypes() as $type => $priceBlock) {
				$this->addPriceBlockType($type, $priceBlock['block'], $priceBlock['template']);
			}
		}

		return $this;
	}

/*	protected function _prepareLayout()
	{
		$this->getLayout()->createBlock('catalog/breadcrumbs');
		$headBlock = $this->getLayout()->getBlock('head');
		if ($headBlock) {
			$product = $this->getProduct();

			$productName = $product->getName();
			$productNameSmCh = mb_strtolower(mb_substr($productName, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr(
					$productName, 1, mb_strlen($productName), 'UTF-8'
				);

			$productAlias = '[название товара]';

			$productMetaTitleSuffix = Mage::getStoreConfig('catalog/seo/product_view_meta_title_suffix');
			$productMetaKeywordSuffix = Mage::getStoreConfig('catalog/seo/product_view_meta_keywords_suffix');
			$productMetaDescriptionSuffix = Mage::getStoreConfig('catalog/seo/product_view_meta_descriptions_suffix');


			if (stristr($productMetaTitleSuffix, $productAlias)) {
				$productMetaTitleSuffix = str_replace($productAlias, $productName, $productMetaTitleSuffix);
			}
			if (stristr($productMetaKeywordSuffix, $productAlias)) {
				$productMetaKeywordSuffix = str_replace($productAlias, $productNameSmCh, $productMetaKeywordSuffix);
			}
			if (stristr($productMetaDescriptionSuffix, $productAlias)) {
				$productMetaDescriptionSuffix = str_replace($productAlias, $productName, $productMetaDescriptionSuffix);
			}

			$customTitle = $productName . ' ' . $productMetaTitleSuffix;
			$title = $product->getMetaTitle();
			if ($title) {
				$customTitle = $customTitle . ' ' . $title;
			}
			$headBlock->setTitle($customTitle);


			if (!$productMetaKeywordSuffix) {
				$customKeywords = $productNameSmCh;
			} else {
				$customKeywords = $productMetaKeywordSuffix;
			}
			$keyword = $product->getMetaKeyword();
			if ($keyword && !$productMetaKeywordSuffix) {
				$customKeywords = $customKeywords . ',' . $keyword;
			}
			$headBlock->setKeywords($customKeywords);


			if (!$productMetaDescriptionSuffix) {
				$customDescription = $productName;
			} else {
				$customDescription = $productMetaDescriptionSuffix;
			}
			$description = $product->getMetaDescription();
			if ($description && !$productMetaDescriptionSuffix) {
				$customDescription = $customDescription . ' ' . $description;
			}
			$headBlock->setDescription($customDescription);

			if ($this->helper('catalog/product')->canUseCanonicalTag()) {
				$params = array('_ignore_category' => true);
				$headBlock->addLinkRel('canonical', $product->getUrlModel()->getUrl($product, $params));
			}

		}
		$block = $this->getLayout()->getBlock('catalog_product_price_template');
		if ($block) {
			foreach ($block->getPriceBlockTypes() as $type => $priceBlock) {
				$this->addPriceBlockType($type, $priceBlock['block'], $priceBlock['template']);
			}
		}


		return $this;
	}*/
}