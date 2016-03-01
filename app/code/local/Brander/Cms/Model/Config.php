<?php
class Brander_Cms_Model_Config
{
    CONST DEFAULT_PAGE_TYPE = 'default';
    CONST DEFAULT_ENTITY_TYPE = 'cms_page';

    CONST REDIRECT_PAGE_TYPE = 'redirect';

    /*
     * @var array
     */
    protected $_pageTypes;

    /*
     * @var array
     */
    protected $_extendParams = array('block', 'template', 'cached');

    protected $_data;

    public function __construct()
    {
        $this->getPageTypes();
    }

    /*
     * Returns page types
     *
     * return array
     */
    public function getPageTypes()
    {
        if (is_null($this->_pageTypes)) {
            $this->_pageTypes = $this->getRawPageTypes();

            $this->_preparePageTypes();
        }

        return $this->_pageTypes;
    }

    /*
     * Returns page type from config
     *
     * @return array
     */
    public function getRawPageTypes()
    {
        $config = $this->getData();

        if (isset($config['pagetypes'])) {
            $pageTypes = $config['pagetypes'];
        } else {
            $pageTypes = array();
        }

        return $pageTypes;
    }

    public function getRootNode()
    {
        return Mage::getConfig()->getNode('cmsadvanced');
    }

    public function getData()
    {
        if (null === $this->_data) {
            $this->_data = $this->getRootNode()->asArray();
        }

        return $this->_data;
    }

    /*
     * Returns default page type
     *
     * @return string
     */
    public function getDefaultPageType()
    {
        return self::DEFAULT_PAGE_TYPE;
    }

    /*
     * Returns default entity type
     *
     * @return string
     */
    public function getDefaultEntityType()
    {
        return self::DEFAULT_ENTITY_TYPE;
    }

    /*
     * Returns page type
     *
     * @param string $name
     * @return Brander_Cms_Model_Page_Type
     */
    public function getPageType($name)
    {
        return isset($this->_pageTypes[$name]) ? $this->_pageTypes[$name] : null;
    }

    /*
     * Returns page type parent names
     *
     * @param string $name
     * @return array
     */
    public function getPageTypeParents($name)
    {
        $pageType = $this->getPageType($name);

        $parents = array();
        
        if (!isset($pageType['extends'])) {
            $parents[] = $this->getDefaultPageType();
        } else {
            $parents[] = $pageType['extends'];
            $parents = array_merge($parents, $this->getPageTypeParents($pageType['extends']));
        }

        return $parents;
    }

    /*
     * Returns custom element types
     *
     * @return array
     */
    public function getElementTypes()
    {
        return (array) $this->getDataNode('elementtypes');
    }

    /*
     * Returns custom setup templates for adminforms setup builder
     *
     * @return array
     */
    public function getSetupTemplates()
    {
        return (array) $this->getDataNode('setuptemplates');
    }

    /*
     * Returns config data by node
     *
     * @param string $name
     * @return array
     */
    public function getDataNode($name)
    {
        $config = $this->getData();
        return isset($config[$name]) ? $config[$name] : array();
    }

    public function getHomePageId()
    {
        return Mage::getStoreConfig('cms_advanced/default/home_page');
    }

    protected function _preparePageTypes()
    {
        foreach ($this->_pageTypes as $pageType => &$params) {
            if (!is_array($params)) {
                $params = array();
            }
            $params = $this->_prepareExtends($pageType, $params);
        }

        return $this;
    }

    protected function _prepareExtends($pageType, $params)
    {
        $defaultPageType = $this->getDefaultPageType();
        $helper = Mage::helper('cmsadvanced');

        $extends = '';
        
        if ($pageType == $defaultPageType) {
        } elseif (isset($params['extends'])) {
            $extends = $params['extends'];
        } else {
            $extends = $defaultPageType;
        }

        $extendParams = array();
        
        if (!$extends) {
            
        } elseif ($extends == $defaultPageType) {
            $buf = $this->getPageType($extends);

            if (isset($buf['groups'])) {
                $extendParams['groups'] = $buf['groups'];
            }
            
            foreach ($this->_extendParams as $key) {
                if (!isset($params[$key]) && isset($buf[$key])) {
                    $extendParams[$key] = $buf[$key];
                }
            }
        } else {
            $extendParams = $this->_prepareExtends($extends, $this->getPageType($extends));
        }

        if ($extends) {
            $params = $helper->mergeArray($extendParams, $params);
        }

        return $params;
    }
}
