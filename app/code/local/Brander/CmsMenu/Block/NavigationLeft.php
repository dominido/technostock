<?php
/**
 * Brander CmsMenu extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        CmsMenu
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_CmsMenu_Block_NavigationLeft extends Mage_Catalog_Block_Navigation
{
    const DDTYPE_NONE = 0;
    const DDTYPE_MEGA = 1;
    const DDTYPE_CLASSIC = 2;
    const DDTYPE_SIMPLE = 3;
    protected $p0b;
    protected $p0c = false;
    protected $p0d;
    protected $p0e = false;
    protected $p0f = null;
    protected $p10 = null;

    protected function _construct()
    {
        parent::_construct();
        $this->p0b = array(self::DDTYPE_MEGA => "mega", self::DDTYPE_CLASSIC => "classic",
                           self::DDTYPE_SIMPLE => "simple");
        $this->p0c = false;
        $this->p0d = "#@#";
        $this->p0e = false;
        $this->p0f = null;
        if (Mage::registry('current_category')) {
            $this->p10 = Mage::registry('current_category')->getId();
        }
    }

    public function getCacheKeyInfo()
    {
        $x11 = array('CATALOG_NAVIGATIONLEFT', Mage::app()->getStore()->getId(), Mage::getDesign()->getPackageName(),
                     Mage::getDesign()->getTheme('template'),
                     Mage::getSingleton('customer/session')->getCustomerGroupId(), 'template' => $this->getTemplate(),
                     'name'                                                                   => $this->getNameInLayout(
                     ), $this->getCurrenCategoryKey(), Mage::helper('ultramegamenu')->getIsOnHome(),
                     (int)Mage::app()->getStore()->isCurrentlySecure(),);
        $x12 = $x11;
        $x11 = array_values($x11);
        $x11 = implode('|', $x11);
        $x11 = md5($x11);
        $x12['category_path'] = $this->getCurrenCategoryKey();
        $x12['short_cache_id'] = $x11;
        return $x12;
    }

    protected function x0b(
        $x13, $x14 = 0, $x15 = false, $x16 = false, $x17 = false, $x18 = '', $x19 = '', $x1a = false, $x1b = null
    ) {
        if (!$x13->getIsActive()) {
            return '';
        }
        $x1c = '';
        if (Mage::helper('catalog/category_flat')->isEnabled()) {
            $x1d = (array)$x13->getChildrenNodes();
            $x1e = count($x1d);
        } else {
            $x1d = $x13->getChildrenCategories();
            $x1e = $x1d->count();
        }
        $x1f = ($x1d && $x1e);
        $x20 = array();
        // здесь второй уровень вложености
        foreach ($x1d as $x21) {
            if ($x21->getIsActive()) {
                $x20[] = $x21;
            }
        }
        $x22 = count($x20);
        $x23 = ($x22 > 0);
        $x24 = Mage::helper('ultramegamenu');
        /*$x25 = Mage::getModel('catalog/category')->load($x13->getId());*/
        $x25 = $x13; /**/
        $x26 = intval($x25->getData('umm_dd_type'));
        if ($x26 === self::DDTYPE_NONE) {
            if ($x1b["ddType"] === self::DDTYPE_MEGA) {
            } else {
                $x26 = self::DDTYPE_CLASSIC;
            }
        } elseif ($x26 === self::DDTYPE_MEGA) {
            if ($x1b["ddType"] === self::DDTYPE_MEGA) {
                $x26 = self::DDTYPE_NONE;
            }
        } elseif ($x26 === self::DDTYPE_SIMPLE) {
            if ($x1b["ddType"] === self::DDTYPE_MEGA) {
                $x26 = self::DDTYPE_NONE;
            } elseif ($x14 === 0) {
                $x26 = self::DDTYPE_CLASSIC;
            }
        }
        $x27 = array("ddType" => $x26,);
        $x28 = false;
        $x29 = array();
        $x2a = '';
        $x2b = '';
        $x2c = false;
        $x2d = "level" . $x14;
        if (false === $this->p0c && ($x26 === self::DDTYPE_MEGA || $x1b["ddType"] === self::DDTYPE_MEGA)) {
            $x28 = true;
        }
        if ($x28) {
            $x2e = $this->x10($x25, "umm_dd_blocks");
            if ($x2e) {
                $x29 = explode($this->p0d, $x2e);
            }
        }
        if (false === $this->p0c && $x26 === self::DDTYPE_MEGA) {
                $x2f = $x25->getData("umm_dd_proportions");
            if ($x2f) {
                $x30 = explode(";", $x2f);
                $x31 = $x30[0];
                $x32 = $x30[1];
                $x33 = $x30[2];
            } else {
                $x31 = $x32 = $x33 = 4;
            }
            $x34 = "grid12-" . $x31;
            $x35 = "grid12-" . $x32;
            $x36 = "grid12-" . $x33;
            if (empty($x29[1]) && empty($x29[2])) {
                $x34 = '';
                $x35 = "grid12-12";
                $x36 = '';
            } elseif (empty($x29[1])) {
                $x34 = '';
                $x35 = "grid12-" . ($x31 + $x32);
            } elseif (empty($x29[2])) {
                $x35 = "grid12-" . ($x32 + $x33);
                $x36 = '';
            } elseif (!$x23) {
                $x34 = "grid12-" . ($x31 + $x32);
                $x35 = '';
                $x36 = "grid12-" . $x33;
            }
            if (!empty($x29[0])) {
                $x2c = true;
                $x2a .= '<div class="nav-block nav-block--top std grid-full">' . $x29[0] . '</div>';
            }
            if (!empty($x29[1])) {
                $x2c = true;
                $x2a .= '<div class="nav-block nav-block--left std ' . $x34 . '">' . $x29[1] . '</div>';
            }
            if ($x23) {
                $x2a .= '<div class="nav-block--center ' . $x35 . '">';
                $x2b .= '</div>';
            }
            if (!empty($x29[2])) {
                $x2c = true;
                $x2b .= '<div class="nav-block nav-block--right std ' . $x36 . '">' . $x29[2] . '</div>';
            }
            if (!empty($x29[3])) {
                $x2c = true;
                $x2b .= '<div class="nav-block nav-block--bottom std grid-full">' . $x29[3] . '</div>';
            }
        }
        $x37 = ($x23 || $x2c) ? true : false;
        $x38 = array("nav-item");
        $x39 = array();
        $x3a = array();
        $x3b = array("nav-submenu");
        $x3c = '';
        $x3d = '';
        $x3e = '';
        $x38[] = $x2d;
        $x38[] = "nav-" . $this->_getItemPosition($x14);
        if ($this->isCategoryActive($x13)) {
            $x38[] = "active";
            if ($x13->getId() === $this->p10) {
                $x38[] = "current";
            }
        }
        if ($x17 && $x18) {
            $x38[] = $x18;
            $x39[] = $x18;
        }
        if ($x16) {
            $x38[] = "first";
        }
        if ($x15) {
            $x38[] = "last";
        }
        if (false === $this->p0c) {
            if ($x26 === self::DDTYPE_CLASSIC) {
                if ($x37) {
                    $x38[] = "nav-item--parent";
                    $x3b[] = "nav-panel--dropdown";
                }
                $x38[] = $this->p0b[self::DDTYPE_CLASSIC];
                $x3b[] = "nav-panel";
            } elseif ($x26 === self::DDTYPE_MEGA) {
                if ($x37) {
                    $x38[] = "nav-item--parent";
                    $x3a[] = "nav-panel--dropdown";
                }
                $x38[] = $this->p0b[self::DDTYPE_MEGA];
                $x3a[] = "nav-panel";
                if ($x19) {
                    $x3a[] = $x19;
                }
                $x3b[] = "nav-submenu--mega";
                $x3f = intval($x25->getData("umm_dd_columns"));
                if ($x3f === 0) {
                    $x3f = 4;
                }
                $x3b[] = "dd-itemgrid dd-itemgrid-" . $x3f . "col";
            } elseif ($x26 === self::DDTYPE_SIMPLE) {
                $x38[] = $this->p0b[self::DDTYPE_SIMPLE];
                $x3b[] = "nav-panel";
            } elseif ($x26 === self::DDTYPE_NONE) {
                $x3b[] = "nav-panel";
            }
            if ($x40 = $x25->getData("umm_dd_width")) {
                $x41 = '';
                $x42 = '';
                if (strpos($x40, "px") || strpos($x40, "%")) {
                    $x41 = ' style="width:' . $x40 . ';"';
                } else {
                    $x42 = intval($x40);
                    if (0 < $x42 && $x42 <= 12) {
                        $x42 = "no-gutter grid12-" . $x42;
                    } else {
                        $x42 = '';
                    }
                }
                if ($x26 === self::DDTYPE_CLASSIC) {
                    $x3d = $x41;
                } elseif ($x26 === self::DDTYPE_MEGA) {
                    $x3c = $x41;
                    if ($x42) {
                        $x3a[] = $x42;
                    }
                }
            } else {
                if ($x26 === self::DDTYPE_MEGA) {
                    $x3a[] = "full-width";
                }
            }
            if ($x2c) {
                if (false === $x23) {
                    $x38[] = "nav-item--only-blocks";
                }
            } else {
                if ($x23) {
                    $x38[] = "nav-item--only-subcategories";
                }
            }
        }
        if ($x37) {

            // TODO: check super category


            $x38[] = "parent";
            if ((false === $this->p0c) && (filter_var($x2d, FILTER_SANITIZE_NUMBER_INT) <= 1 )) {
                $x3e = '<span class="caret">&nbsp;</span>';
            }
        }
        $x43 = '';
        if ($this->p0e && $this->p0c) {
            $x43 = '<span class="number">(' . $this->x0f($x25) . ')</span>';
        }
        $x44 = $this->x11($x25, $x14);
        if ($x45 = $x25->getData("umm_cat_target")) {
            if ($x45 === "#") {
                $x46 = "#";
                $x39[] = "no-click";
            } elseif ($x45 = trim($x45)) {
                if (strpos($x45, "http") === 0) {
                    $x46 = $x45;
                } else {
                    $x46 = Mage::getBaseUrl() . $x45;
                }
            } else {
                $x46 = $this->getCategoryUrl($x13);
            }
        } else {
            $x46 = $this->getCategoryUrl($x13);
        }
        $x1c .= "<li" . ($x38 ? ' class="' . implode(" ", $x38) . '"' : '') . ">";
        if (false === $this->p0c && $x1b["ddType"] === self::DDTYPE_MEGA) {
            if (!empty($x29[0])) {
                $x1c .= '<div class="nav-block nav-block--top std">' . $x29[0] . '</div>';
            }
        }
        $x1c .= '<a href="' . $x46 . '"' . ($x39 ? ' class="' . implode(" ", $x39) . '"' : '') . '>';
        $x1c .= '<span>' . $this->escapeHtml($x13->getName()) . $x43 . $x44 . '</span>' . $x3e;
        $x1c .= '</a>';
        $x47 = '';
        $x48 = 0;
        foreach ($x20 as $x21) {
            $x47 .= $this->x0b($x21, ($x14 + 1), ($x48 == $x22 - 1), ($x48 == 0), false, $x18, $x19, $x1a, $x27);
            $x48++;

        }
        if ((!empty($x47) || $x2c) && ( filter_var($x2d, FILTER_SANITIZE_NUMBER_INT) <= 1 )) {
            $x1c .= '<span class="opener"></span>';
            if (!empty($x3a)) {
                $x1c .= '<div class="' . implode(' ', $x3a) . '"' . $x3c . '><div class="nav-panel-inner">';
            }
            $x1c .= $x2a;
            if (!empty($x47)) {
                $x1c .= '<div class="dropdown_new"><ul class="' . $x2d . ' ' . implode(' ', $x3b) . '"' . $x3d . '>';
                $x1c .= $x47;
                $x1c .= '</ul></div>';
            }
            $x1c .= $x2b;
            if (!empty($x3a)) {
                $x1c .= "</div></div>";
            }
        }
        if (false === $this->p0c && $x1b["ddType"] === self::DDTYPE_MEGA) {
            if (!empty($x29[3])) {
                $x1c .= '<div class="nav-block nav-block--bottom std">' . $x29[3] . '</div>';
            }
        }
        $x1c .= "</li>";
        return $x1c;
    }

    public function renderCategoriesMenuHtml($x49 = false, $x14 = 0, $x18 = '', $x19 = '')
    {
        $x4a = array();
        //TODO::
        foreach ($this->getStoreCategories() as $x21) {
            if ($x21->getIsActive()) {
                $x4a[] = $x21;
            }
        }
        $x4b = count($x4a);
        $x4c = ($x4b > 0);
        if (!$x4c) {
            return '';
        }
        $x1b = array("ddType" => self::DDTYPE_NONE);
        $x1c = '';
        $x48 = 0;
        foreach ($x4a as $x13) {
            $x1c .= $this->x0b($x13, $x14, ($x48 == $x4b - 1), ($x48 == 0), true, $x18, $x19, true, $x1b);
            $x48++;
        }
        return $x1c;
    }

    public function renderMe($x49, $x4d = 0, $x4e = 0)
    {
        $x4f = '';
        $x50 = '';
        if ($x4d === 'parent_no_siblings') {
            if ($x51 = Mage::registry('current_category')) {
                $x4f = $x51->getId();
                $x50 = $x51->getLevel();
            }
        }
        $this->p0c = true;
        $this->p0e = Mage::helper('ultramegamenu')->getCfg('sidemenu/num_of_products');
        $x14 = 0;
        $x18 = '';
        $x19 = '';
        $x52 = $this->x0e($x4d);
        $x53 = $this->x0c($x52, $x4e);
        $x4a = array();
        foreach ($x53 as $x21) {
            if ($x21->getIsActive()) {
                if ($x4d === 'parent_no_siblings') {
                    if ($x50 !== '' && $x21->getLevel() == $x50 && $x21->getId() != $x4f) {
                        continue;
                    }
                }
                $x4a[] = $x21;
            }
        }
        $x4b = count($x4a);
        $x4c = ($x4b > 0);
        if (!$x4c) {
            return '';
        }
        $x1b = array("ddType" => self::DDTYPE_NONE);
        $x1c = '';
        $x48 = 0;
        foreach ($x4a as $x13) {
            $x1c .= $this->x0b($x13, $x14, ($x48 == $x4b - 1), ($x48 == 0), true, $x18, $x19, true, $x1b);
            $x48++;
        }
        return $x1c;
    }

    protected function x0c($x52 = 0, $x4e = 0, $x54 = false, $x55 = false, $x56 = true)
    {
        $x13 = Mage::getModel('catalog/category');
        if ($x52 === null || !$x13->checkId($x52)) {
            return array();
        }
        if (Mage::helper('catalog/category_flat')->isEnabled()) {
            $x53 = $this->x0d($x52, $x4e, $x54, $x55, $x56);
        } else {
            $x53 = $x13->getCategories($x52, $x4e, $x54, $x55, $x56);
        }
        return $x53;
    }

    protected function x0d($x52 = 0, $x4e = 0, $x54 = false, $x55 = false, $x56 = true)
    {
        $x57 = Mage::getResourceModel('catalog/category_flat');
        return $x57->getCategories($x52, $x4e, $x54, $x55, $x56);
    }

    protected function x0e($x4d)
    {
        $x52 = null;
        if ($x4d === 'current') {
            $x51 = Mage::registry('current_category');
            if ($x51) {
                $x52 = $x51->getId();
            }
        } elseif ($x4d === 'parent') {
            $x51 = Mage::registry('current_category');
            if ($x51) {
                $x52 = $x51->getParentId();
            }
        } elseif ($x4d === 'parent_no_siblings') {
            $x51 = Mage::registry('current_category');
            if ($x51) {
                $x52 = $x51->getParentId();
            }
        } elseif ($x4d === 'root' || !$x4d) {
            $x52 = Mage::app()->getStore()->getRootCategoryId();
        } elseif (is_numeric($x4d)) {
            $x52 = intval($x4d);
        }
        $x58 = Mage::helper('ultramegamenu')->getCfg('sidemenu/fallback');
        if ($x52 === null && $x58) {
            $x52 = Mage::app()->getStore()->getRootCategoryId();
        }
        return $x52;
    }

    protected function x0f($x13)
    {
        return Mage::getModel('catalog/layer')->setCurrentCategory($x13->getID())->getProductCollection()->getSize();
    }

    public function renderBlockTitle()
    {
        $x24 = Mage::helper('ultramegamenu');
        $x51 = Mage::registry('current_category');
        if (!$x51) {
            $x58 = $x24->getCfg('sidemenu/fallback');
            if ($x58) {
                $x59 = $x24->getCfg('sidemenu/block_name_fallback');
                if ($x59) {
                    return $x59;
                }
            }
        }
        $x5a = $this->getBlockName();
        if ($x5a === null) {
            $x5a = $x24->getCfg('sidemenu/block_name');
        }
        $x5b = '';
        if ($x51) {
            $x5b = $x51->getName();
        }
        $x5a = str_replace('[current_category]', $x5b, $x5a);
        return $x5a;
    }

    protected function x10($x25, $x5c)
    {
        if (!$this->p0f) {
            $this->p0f = Mage::helper('cms')->getBlockTemplateProcessor();
        }
        return $this->p0f->filter(trim($x25->getData($x5c)));
    }

    protected function x11($x25, $x14)
    {
        $x5d = $x25->getData('umm_cat_label');
        if ($x5d) {
            $x5e = trim(Mage::helper('ultramegamenu')->getCfg('category_labels/' . $x5d));
            if ($x5e) {
                if ($x14 == 0) {
                    return '<span class="cat-label cat-label-' . $x5d . ' pin-bottom">' . $x5e . '</span>';
                } else {
                    return '<span class="cat-label cat-label-' . $x5d . '">' . $x5e . '</span>';
                }
            }
        }
        return '';
    }

    public function getStoreCategories()
    {
        $helper = Mage::helper('brander_cmshome');
        return $helper->getStoreCategories();
    }
}