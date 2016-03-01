<?php
class Brander_Cms_Model_Page extends Mage_Core_Model_Abstract
{
    /**
     * @var Brander_Cms_Model_Page_Type
     */
    protected $_pageType;

    /**
     * @var Brander_Cms_Model_Page
     */
    protected $_parent = false;

    protected function _construct()
    {
        $this->_init('cmsadvanced/page');
    }

    /**
     * Returns page attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        $result = $this->getResource()
            ->loadAllAttributes($this)
            ->getSortedAttributes();

        return $result;
    }

    /**
     * Get attribute text by its code
     *
     * @param $attributeCode Code of the attribute
     * @return string
     */
    public function getAttributeText($attributeCode)
    {
        return $this->getResource()
            ->getAttribute($attributeCode)
                ->getSource()
                    ->getOptionText($this->getData($attributeCode));
    }

    /**
     * Returns default attribute set id
     *
     * @return int
     */
    public function getDefaultAttributeSetId()
    {
        return $this->getResource()->getEntityType()->getDefaultAttributeSetId();
    }

    /**
     * Returns page path ids
     *
     * @return array
     */
    public function getPathIds()
    {
        $ids = $this->getData('path_ids');
        if (is_null($ids)) {
            $ids = explode('/', $this->getPath());
            $this->setData('path_ids', $ids);
        }
        return $ids;
    }

    /**
     * Returns page type model
     *
     * @return Brander_Cms_Model_Page_Type
     */
    public function getPageType()
    {
        if (is_null($this->_pageType)) {
            $pageTypeName = $this->getData('page_type');
            $config = $this->getConfig();
            if (!$pageTypeName || is_numeric($pageTypeName)) {
                $pageTypeName = $config->getDefaultPageType();
            }

            $config = $this->getConfig()->getPageType($pageTypeName);
            $this->_pageType = Mage::getModel('cmsadvanced/page_type')
                             ->addData($config)
                             ->setCode($pageTypeName)
                             ->setPage($this);
        }

        return $this->_pageType;
    }

    /**
     * Returns page type code
     *
     * @return string
     */
    public function getTypeCode()
    {
        return $this->getPageType()->getCode();
    }

    /**
     * Returns cms config
     *
     * @return Brander_Cms_Model_Config
     */
    public function getConfig()
    {
        return Mage::getSingleton('cmsadvanced/config');
    }

    /**
     * Checks if page has children use chilren count attribute
     *
     * @return bool
     */
    public function hasChildren()
    {
        return $this->_getResource()->getChildrenAmount($this) > 0;
    }

    /**
     * Returns page children
     *
     * @param array|string $select
     * @param bool $onlyActive - if true then is_active = true in filter
     */
    public function getChildren($select = '*', $onlyActive = true, $useOrder = true)
    {
        $collection = $this->getResource()
                    ->getChildren($this)
                    ->addAttributeToSelect($select)
                    ->addUrl()
                    ;

        if ($onlyActive) {
            $collection->addActiveFilter();
        }

        if ($useOrder) {
            $collection->setOrder('position', 'asc');
        }

        return $collection;
    }

    /**
     * Returns page entity type
     *
     * @return string
     */
    public function getEntityType()
    {
        return $this->getConfig()->getDefaultEntityType();
    }

    /**
     * Returns parent ids
     *
     * return array
     */
    public function getParentIds()
    {
        return array_diff($this->getPathIds(), array($this->getId()));
    }

    /**
     * Returns parent id
     *
     * @return int
     */
    public function getParentId()
    {
        $parentIds = $this->getParentIds();
        return intval(array_pop($parentIds));
    }

    /**
     * Returns page parent
     *
     * @param string|array $select
     * @return Brander_Cms_Model_Page
     */
    public function getParent($select = '*')
    {
        if ($this->_parent === false) {
            $this->_parent = $this->getParentCollection(array($this->getParentId()))->getFirstItem();
        }

        return $this->_parent;
    }

    /**
     * Returns page parents
     *
     * @param array|string $select
     * @return Brander_Cms_Model_Resource_Eav_Mysql4_Page_Collection
     */
    public function getParents($select = '*')
    {
        $parentIds = $this->getParentIds();
        array_shift($parentIds);

        $collection = $this->getParentCollection($parentIds, $select = '*');

        return $collection;
    }

    /**
     * Returns page specify parents
     *
     * @param array $parentIds
     * @param array|string $select
     * @return Brander_Cms_Model_Resource_Eav_Mysql4_Page_Collection
     */
    public function getParentCollection($parentIds, $select = '*')
    {
        $collection = $this->getCollection()
                    ->addAttributeToSelect($select)
                    ->addUrl()
                    ;

        if ($parentIds !== null) {
            $collection->addFieldToFilter('entity_id', array('in' => $parentIds));
        }

        return $collection;
    }

    /**
     * Move page
     *
     * @param   int $parentId new parent category id
     * @param   int $afterPageId category id after which we have put current category
     * @return  Brander_Cms_Model_Page
     */
    public function move($parentId, $afterPageId)
    {
        /**
         * Setting affected category ids for third party engine index refresh
         */
        $this->setMovedPageId($this->getId());

        /**
         * Validate new parent category id. (category model is used for backward
         * compatibility in event params)
         */
        $parent = Mage::getModel('cmsadvanced/page')
            ->setStoreId($this->getStoreId())
            ->load($parentId);

        if (!$parent->getId()) {
            Mage::throwException(
                Mage::helper('catalog')->__('Page move operation is not possible: the new parent page was not found.')
            );
        }

        $eventParams = array(
            $this->_eventObject => $this,
            'parent'        => $parent,
            'category_id'   => $this->getId(),
            'prev_parent_id'=> $this->getParentId(),
            'parent_id'     => $parentId
        );
        $moveComplete = false;

        $this->_getResource()->beginTransaction();
        try {
            $this->getResource()->changeParent($this, $parent, $afterPageId);
            $this->_getResource()->commit();

            $this->setAffectedPageIds(array($this->getId(), $this->getParentId(), $parentId));

            $moveComplete = true;
        } catch (Exception $e) {
            $this->_getResource()->rollBack();
            throw $e;
        }

        return $this;
    }

    /**
     * Returns page url
     *
     * @return string
     */
    public function getUrl()
    {
        if (!$url = $this->getData('_url')) {
            if ($this->isHomePage()) {
                $url = $this->_getUrlModel()->getUrl('');
            } elseif (!$url = $this->getRedirectUrl()) {
                $url = $this->getRawUrl();
            }

            $this->setData('_url', $url);
        }

        return $url;
    }

    /**
     * Returns page url use url_key
     *
     * @return string
     */
    public function getRawUrl()
    {
		return $this->_getUrlModel()->getDirectUrl($this->getUrlKey() . '/');
	}

    /**
     * Returns page url model
     *
     * @return Mage_Core_Model_Url
     */
    protected function _getUrlModel()
    {
        $model = Mage::getModel('core/url');
        if (($storeId = $this->getStoreId()) > 0) {
            $model->setStore($this->getStoreId());
        }

        return $model;
    }

    /**
     * Returns page redirect url
     *
     * @return null|string
     */
    public function getRedirectUrl()
    {
		$url = null;
        if ($redirectUrl = $this->getData('redirect_url')) {
            $url = Mage::helper('cmsadvanced')->getUrl($redirectUrl);
        } elseif ((Brander_Cms_Model_Config::REDIRECT_PAGE_TYPE === $this->getTypeCode()) && $redirectPage = $this->getRedirectPage()) {
			$url = $redirectPage->getUrl();
		}

		return $url;
	}

    /**
     * Returns redirect page
     *
     * @return null|Brander_Cms_Model_Page
     */
	public function getRedirectPage()
	{
		$redirectPageId = (int) $this->getData('redirect_page');

		$page = null;

		if (1 === $redirectPageId) {
			$children = $this->getChildren(array('url_key'))->setPage(1,1);
			if (count($children)) {
				$page = $children->getFirstItem();
			}
		} elseif ($redirectPageId > 1) {
			$pages = Mage::getResourceModel('cmsadvanced/page_collection')
				  ->addIdFilter($redirectPageId)
				  ->addActiveFilter()
				  ->addUrl()
				  ->setPage(1,1)
				  ;

			if (count($pages)) {
				$page = $pages->getFirstItem();
			}
		}

		return $page;
	}

    /**
     * Returns page url key
     *
     * @return string
     */
    public function getUrlKey()
    {
        if (!$urlKey = $this->getData('url_path')) {
            if ($parentKey = $this->getResource()->getParentUrlKey($this)) {
                $parentKey .= '/';
            }

            $urlKey = $parentKey . $this->getData('url_key');
        }

        return $urlKey;
    }

    /**
     * Returns page adminforms block model
     *
     * @return Brander_Adminforms_Model_Block
     */
    public function getBlockModel()
    {
        return Mage::getModel('adminforms/block')->setEntityType($this->getEntityType());
    }

    /**
     * Returns page attribute image object
     *
     * @return Brander_Adminform_Helper_Image
     */
    public function getImage($attribute)
    {
        $entity = 'adminforms' . DS . $this->getEntityType();

        return Mage::helper('adminforms/image')->init($entity, $this->getData($attribute), 'cache');
    }

    /**
     * Returns page image attribute url
     *
     * @param string $attributeCode
     * @return string
     */
    public function getImageUrl($attributeCode)
    {
        return $this->getBlockModel()->getImageUrl($this->getData($attributeCode));
    }

    /**
     * Returns page image attribute filename
     *
     * @param string $attributeCode
     * @return string
     */
    public function getImageFile($attributeCode)
    {
        return $this->getBlockModel()->getImageFile($this->getData($attributeCode));
    }

    /**
     * Returns page image attribute sizes
     *
     * @param string $attributeCode
     * @return array
     *      array(
     *          'width' => int,
     *          'height' => int
     *      )
     */
    public function getImageSize($attributeCode)
    {
        return $this->getBlockModel()->getImageSize($this->getData($attributeCode));
    }

    /**
     * Prepares url key
     *
     * @param string $urlKey
     * @return string
     */
    public function formatUrlKey($urlKey)
    {
        $urlKey = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('catalog/product_url')->format($urlKey));
        $urlKey = strtolower($urlKey);
        $urlKey = trim($urlKey, '-');

/*        $urlKey = Mage::helper('core')->removeAccents($urlKey);
        $urlKey = preg_replace('#[^0-9a-z]+#i', '-', $urlKey);
        $urlKey = strtolower($urlKey);
        $urlKey = trim($urlKey, '-');*/
        return $urlKey;
    }

    /**
     * Returns page template
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->getPageType()->getTemplate();
    }

    /**
     * Returns page block
     *
     * @return Brander_Cms_Block_Page
     */
    public function getBlock()
    {
        return $this->getPageType()->getBlock();
    }

    /**
     * Prepare attribute value with template processor
     *
     * @param string $attributeCode
     * @return string
     */
    public function getPreparedContent($attributeCode)
    {
        return Mage::helper('cms')->getPageTemplateProcessor()->filter($this->getData($attributeCode));
    }

    /**
     * Check is homepage
     *
     * @return bool
     */
    public function isHomePage()
    {
        return $this->getConfig()->getHomePageId() === $this->getId();
    }

    /**
     * Returns collection of adminforms blocks
     *
     * @param string $attributeCode
     * @param array|string $select - field to select
     * @param bool $isStatus - use status field in filter
     * @return Brander_AdminForms_Model_Entity_Block_Collection
     */
    public function getBlockCollection($attributeCode, $select = '*', $isStatus = true)
    {
        $collection = $this->getData($attributeCode);

        if (!($collection instanceof Brander_AdminForms_Model_Entity_Block_Collection)) {
            //compatibility fix
            $collection = Mage::helper('adminforms')
                        ->getCollection($attributeCode, '*', false)
                        ->addAttributeToFilter('page_id', $this->getId())
                        ;
            $this->setData($attributeCode, $collection);
        }

        $collection->removeAttributeToSelect();
        $collection->addAttributeToSelect($select);

        if ($isStatus) {
            $collection->addAttributeToFilter('status', 1);
        }

        return $collection;
    }
}
