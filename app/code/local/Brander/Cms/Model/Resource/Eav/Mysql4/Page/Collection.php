<?php
class Brander_Cms_Model_Resource_Eav_Mysql4_Page_Collection extends Brander_AdminForms_Model_Entity_Block_Collection
{
    public function __construct($resource=null)
    {
        Mage_Eav_Model_Entity_Collection_Abstract::__construct($resource);
    }

    protected function _construct()
    {
        $this->_init('cmsadvanced/page');
    }

    public function addIdFilter($pageIds)
    {
        if (is_array($pageIds)) {
            if (empty($pageIds)) {
                $condition = '';
            } else {
                $condition = array('in' => $pageIds);
            }
        } elseif (is_numeric($pageIds)) {
            $condition = $pageIds;
        } elseif (is_string($pageIds)) {
            $ids = explode(',', $pageIds);
            if (empty($ids)) {
                $condition = $pageIds;
            } else {
                $condition = array('in' => $ids);
            }
        }
        $this->addFieldToFilter('entity_id', $condition);
        return $this;
    }

    public function addExcludeRootFilter()
    {
        return $this->addFieldToFilter('parent_id', array('gt' => 1));
    }

    public function addActiveFilter()
    {
        return $this->addAttributeToFilter('is_active', 1);
    }

    public function addLevelFilter($level)
    {
        return $this->addFieldToFilter('level', $level);
    }

    public function addParentFilter($parentIds)
    {
        return $this->addFieldToFilter('parent_id', $parentIds);
    }
    
    public function addRedirect()
    {
		return $this->addAttributeToSelect(array(
            'redirect_page',
            'redirect_url'
        ));
	}

    public function addUrl()
    {
        return $this->addAttributeToSelect(array(
            'url_key'
        ))->addRedirect();
    }
}
