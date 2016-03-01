<?php

class Brander_LayeredNavigation_Block_Catalog_Layer_View extends Mage_Catalog_Block_Layer_View
{
    protected $_filterBlocks = null;
    protected $_blockPos     = 'left';

    protected $attributeOptionsData;
    
    public function getFilters()
    {
        if (!is_null($this->_filterBlocks)){
            return $this->_filterBlocks;
        }

        if ($this->_isCurrentUserAgentExcluded()) {
            return array();
        }

        $filters = parent::getFilters();

        $filters = $this->_excludeCurrentLandingFilters($filters);

        // append stock filter
        $f = $this->getChild('stock_filter');
    	if ($f && !$this->_notInBlock(Mage::getStoreConfig('brander_layerednavigation/block/stock_filter_pos'))) {
        	$filters[] = $f;
        }

        /** @var Brander_LayeredNavigation_Block_Catalog_Layer_Filter_Rating $f */
        $f = $this->getChild('rating_filter');
        if ($f && $f->getPosition() > -1 && !$this->_notInBlock(Mage::getStoreConfig('brander_layerednavigation/block/rating_filter_pos'))) {
            $filters[] = $f;
        }

        // remove some filters from the home page
        $exclude = Mage::getStoreConfig('brander_layerednavigation/general/exclude');
        if ('/' == Mage::app()->getRequest()->getRequestString() && $exclude){
            $exclude = explode(',', preg_replace('/[^a-zA-Z0-9_\-,]+/','', $exclude));
            $filters = $this->excludeFilters($filters, $exclude);
        } else {
            $exclude = array();
        }
        
        $this->computeAttributeOptionsData($filters);

        // update filters with new properties
        $allSelected = array();
        foreach ($filters as $f){
            $strategy = $this->_getFilterStrategy($f);

            if (is_object($strategy)) {
                // initiate all filter-specific logic
                $strategy->prepare();
                $f->setIsExcluded($strategy->getIsExcluded());

                // remember selected options for dependent excluding
                if ($strategy instanceof Brander_LayeredNavigation_Helper_Layer_View_Strategy_Attribute) {
                    $selectedValues = $strategy->getSelectedValues();
                    if ($selectedValues){
                        $allSelected = array_merge($allSelected, $selectedValues);
                    }
                }
            }
        }
        
        //exclude dependant, since 1.4.7
        foreach ($filters as $f){
            $parentAttributes = trim(str_replace(' ', '', $f->getDependOnAttribute()));

            if (!$parentAttributes){
                continue;
            }

            if (!empty($parentAttributes)) {
                $attributePresent = false;
                $parentAttributes = explode(',', $parentAttributes);
                foreach ($parentAttributes as $parentAttribute) {
                    if (Mage::app()->getRequest()->getParam($parentAttribute)) {
                        $attributePresent = true;
                        break;
                    }
                }
                if (!$attributePresent) {
                    $exclude[] = $f->getAttributeModel()->getAttributeCode();
                }
            }
        }

        // 1.2.7 exclude some filters from the selected categories
        $filters = $this->excludeFilters($filters, $exclude);

        usort($filters, array($this, 'sortFiltersByOrder'));

        $this->_filterBlocks = $filters;
        return $filters;
    }

    protected function _getFilterStrategy(Mage_Catalog_Block_Layer_Filter_Abstract $filter)
    {
        $strategyCode = null;
        if ($filter instanceof Brander_LayeredNavigation_Block_Catalog_Layer_Filter_Stock) {
            $strategyCode = 'stock';
        }
        else if ($filter instanceof Mage_Catalog_Block_Layer_Filter_Attribute) {
            $strategyCode = 'attribute';
        }
        else if ($filter instanceof Mage_Catalog_Block_Layer_Filter_Category) {
            $strategyCode = 'category';
        }
        else if ($filter instanceof Mage_Catalog_Block_Layer_Filter_Price) {
            $strategyCode = 'price';
        }
        else if ($filter instanceof Mage_Catalog_Block_Layer_Filter_Decimal) {
            $strategyCode = 'decimal';
        }
        else if ($filter instanceof Brander_LayeredNavigation_Block_Catalog_Layer_Filter_Rating) {
            $strategyCode = 'rating';
        }

        /** @var Brander_LayeredNavigation_Helper_Layer_View_Strategy_Abstract|null $strategy */
        if ($strategyCode) {
            $strategy = Mage::helper('brander_layerednavigation/layer_view_strategy_' . $strategyCode);
            $strategy->setLayer($this);
            $strategy->setFilter($filter);
        } else {
            $strategy = null;
        }

        return $strategy;
    }

    protected function computeAttributeOptionsData($filters)
    {
        $ids = array();
        foreach ($filters as $f){
            if ($f->getItemsCount() && ($f instanceof Mage_Catalog_Block_Layer_Filter_Attribute)){
                $items = $f->getItems();
                foreach ($items as $item){
                    $ids[] = $item->getOptionId();
                }
            }
        }

        // images of filter values
        $optionsCollection = Mage::getResourceModel('brander_layerednavigation/value_collection')
            ->addFieldToFilter('option_id', array('in' => $ids))
            ->load();

        $this->attributeOptionsData = array();
        foreach ($optionsCollection as $row){
            $this->attributeOptionsData[$row->getOptionId()] = array(
                'img' => $row->getImgSmall(),
                'img_hover' => $row->getImgSmallHover(),
                'descr' => $row->getDescr()
            );
        }
    }

    public function getAttributeOptionsData()
    {
        if (is_null($this->attributeOptionsData)) {
            throw new Exception('AttributeOptionsData not initialized');
        }

        return $this->attributeOptionsData;
    }

    protected function _excludeCurrentLandingFilters(array $filters)
    {
        return $filters;
    }
    
    public function sortFiltersByOrder($filter1, $filter2) 
    {
        if ($filter1->getPosition() == $filter2->getPosition()) {
            if ($filter1 instanceof Mage_Catalog_Block_Layer_Filter_Category) {
                return -1;
            } else
                if ($filter2 instanceof Mage_Catalog_Block_Layer_Filter_Category) {
                return 1;
            }

            return 0;
        } 
        return $filter1->getPosition() > $filter2->getPosition() ? 1 : -1;
    }
    
    protected function _getFilterableAttributes()
    {
        $attributes = $this->getData('_filterable_attributes');
        if (is_null($attributes)) {
            $settings   = $this->_getDataHelper()->getAttributesSettings();
            $attributes = Mage::helper('brander_layerednavigation/attributes')->getFilterableAttributes();
            foreach ($attributes as $k => $v){
                $pos = 'left';
                if (isset($settings[$v->getId()])){
                    $pos = $settings[$v->getId()]->getBlockPos();
                }
                elseif($v->getAttributeCode() == 'price'){
                    $pos = Mage::getStoreConfig('brander_layerednavigation/block/price_pos');
                }
                if ($this->_notInBlock($pos)){
                    unset($attributes[$k]);
                }
            } 
            
            $this->setData('_filterable_attributes', $attributes);
        }

        return $attributes;
    }    
    
    public function getStateHtml()
    {
        $pos = Mage::getStoreConfig('brander_layerednavigation/block/state_pos');
        if ($this->_notInBlock($pos)){
            return '';
        }
        $this->getChild('layer_state')->setTemplate('brander/layerednavigation/state.phtml');
        return $this->getChildHtml('layer_state');
    } 
    
    public function canShowBlock()
    {
        if ($this->canShowOptions()){
            return true;
        }
        
        $cnt = 0;
        $pos = Mage::getStoreConfig('brander_layerednavigation/block/state_pos');
        if (!$this->_notInBlock($pos)){
            $cnt = count($this->getLayer()->getState()->getFilters());
        }        
        return $cnt;
    }  
      
    public function getBlockId()
    {
        return 'layerednavigation-filters-' . $this->_blockPos;
    }       
    
    protected function excludeFilters($filters, $exclude)
    {
        $new = array();
        foreach ($filters as $f){
            $code = substr($f->getData('type'), 1+strrpos($f->getData('type'), '_'));
            if ($f->getAttributeModel()){
                $code = $f->getAttributeModel()->getAttributeCode();
            }
            
            if (in_array($code, $exclude) || $f->getIsExcluded()){
                 continue;
            } 
             
            $new[] = $f;          
        }
        return $new;
    }
    
    protected function _afterToHtml($html)
    {
        $html = parent::_afterToHtml($html);
        
        if (!$html){
            return '';
        }
        
        $pos = strrpos($html, '</div>');
        if ($pos !== false) {
            //add an overlay before closing tag
            $html = substr($html, 0, strrpos($html, '</div>')) 
                  . '<div style="display:none" class="layerednavigation-overlay"></div>'
                  . '</div>';
        }

        
        // to make js and css work for 1.3 also
        $html = str_replace('class="narrow-by', 'class="block-layered-nav narrow-by', $html);
        // add selector for ajax
        $html = str_replace('block-layered-nav', 'block-layered-nav ' . $this->getBlockId(), $html);

        if (Mage::getStoreConfig('brander_layerednavigation/general/enable_collapsing')) {
            $html = str_replace('block-layered-nav', 'block-layered-nav layerednavigation-collapse-enabled', $html);
        }

        $enableOverflowScroll = Mage::getStoreConfig('brander_layerednavigation/block/enable_overflow_scroll');
        if ($enableOverflowScroll) {
            $html = str_replace('block-layered-nav', 'block-layered-nav layerednavigation-overflow-scroll-enabled', $html);
            if (strpos($html, 'block-layered-nav')) {
                $html = $html
                        . '<style>'
                        . 'div.layerednavigation-overflow-scroll-enabled div.block-content dl dd > ol:first-of-type { max-height: ' . $enableOverflowScroll . 'px; overflow-y: auto; }'
                        . '</style>';
            }
        }

        // we don't want to move this into the template are different in custom themes
        foreach ($this->getFilters() as $f){
            $name = $this->__($f->getName());
            if ($f->getCollapsed() && !$f->getHasSelection()){
                $html = preg_replace('|(<dt[^>]*)(>'. preg_quote($name, '|') .')|iu', '$1 class="layerednavigation-collapsed"$2', $html);
            }
            $comment = $f->getComment();
            if ($comment){
                $img = Mage::getDesign()->getSkinUrl('images/layerednavigation-tooltip.png');
                $img = ' <img class="layerednavigation-tooltip-img" src="'.$img.'" width="9" height="9" alt="'.htmlspecialchars($comment).'" />';

                $pattern = '@(<dt[^>]*>\s*' . preg_quote($name, '@') . ')\s*(</dt>)@ui';
                $replacement = '$1 ' . $img . '$2';
                $html = preg_replace($pattern, $replacement, $html);
            }
            
        }

        return $html;
    }    

    protected function _prepareLayout()
    {
        $pos = Mage::getStoreConfig('brander_layerednavigation/block/categories_pos');
        if ($this->_notInBlock($pos)){
            $this->_categoryBlockName = 'brander_layerednavigation/catalog_layer_filter_empty';
        }        
        if (Mage::getStoreConfig('brander_layerednavigation/general/stock_filter_pos') >= 0) {
        	$stockBlock = $this->getLayout()->createBlock('brander_layerednavigation/catalog_layer_filter_stock')
            	->setLayer($this->getLayer())
            	->init();

            $this->setChild('stock_filter', $stockBlock);
        }

        if (Mage::getStoreConfig('brander_layerednavigation/general/rating_filter_pos') >= 0) {
            $ratingBlock = $this->getLayout()->createBlock('brander_layerednavigation/catalog_layer_filter_rating')
                                ->setLayer($this->getLayer())
                                ->init();
            $this->setChild('rating_filter', $ratingBlock);
        }

        if (Mage::registry('brander_layerednavigation_layout_prepared')){
            return parent::_prepareLayout();
        }
        else {
            Mage::register('brander_layerednavigation_layout_prepared', true);
        }
        
        if (!Mage::getStoreConfigFlag('customer/startup/redirect_dashboard')) { 
            $url = Mage::helper('brander_layerednavigation/url')->getFullUrl($_GET);
            Mage::getSingleton('customer/session')
                ->setBeforeAuthUrl($url);           
        }
        
        $head = $this->getLayout()->getBlock('head');
        if ($head){
            $head->addJs('brander/layerednavigation/layerednavigation.js');

            if (Mage::getStoreConfig('brander_layerednavigation/block/slider_use_ui')) {
                $head->addJs('brander/layerednavigation/jquery.ui.touch-punch.min.js');
                $head->addJs('brander/layerednavigation/layerednavigation-jquery.js');
            }

            if (Mage::getStoreConfigFlag('brander_layerednavigation/block/ajax')){
                $request = Mage::app()->getRequest();
                
                $isProductPage = $request->getControllerName() == "product" &&
                    $request->getActionName() == "view";
                
                if (!$isProductPage)
                $head->addJs('brander/layerednavigation/layerednavigation-ajax.js');
            }
        }
        
        return parent::_prepareLayout();
    } 
    
    protected function _notInBlock($pos)
    {
        if (!in_array($pos, array('left', 'right', 'top','both'))){
            $pos = 'left';
        }
        return (!in_array($pos, array($this->_blockPos, Brander_LayeredNavigation_Model_Source_Position::BOTH)));
    }
      
    protected function _isCurrentUserAgentExcluded()
    {
        /** @var Mage_Core_Helper_Http $helper */
        $helper = Mage::helper('core/http');
        $currentAgent = $helper->getHttpUserAgent();

        $excludeAgents = explode(',', Mage::getStoreConfig('brander_layerednavigation/seo/exclude_user_agent'));
        foreach ($excludeAgents as $agent) {
            if (stripos($currentAgent, trim($agent)) !== false) {
                return true;
            }
        }

        return false;
    }

	public function getClearUrl()
    {
        /** @var Brander_LayeredNavigation_Helper_Url $helper */
        $helper = Mage::helper('brander_layerednavigation/url');
        $query = array();
        if ($helper->isOnBrandPage()) {
            $brandAttr = trim(Mage::getStoreConfig('brander_layerednavigation/brands/attr'));
            $brandId = $this->getRequest()->getParam($brandAttr);
            if ($brandId) {
                $query[$brandAttr] = (int) $brandId;
            }
        }
		return $helper->getFullUrl($query, true);
	}

    protected function _getDataHelper()
    {
        /** @var Brander_LayeredNavigation_Helper_Data $helper */
        $helper = Mage::helper('brander_layerednavigation');
        return $helper;
    }

}