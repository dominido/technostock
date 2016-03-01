<?php
/**
 * Brander MarketExport extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Brander
 * @package        MarketExport
 * @copyright      Copyright (c) 2014
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         AlexVegas (Alexandr Belyaev) <alexandr.belyaev.only@gmail.com>
 */
class Brander_MarketExport_Block_Adminhtml_Edit_Checkrating
    extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Render the element
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string html
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = '<tr>';
        $html .= '<td class="label">' . $element->getLabelHtml() . '</td>';
        $html .= '<td class="value">' . $element->getElementHtml() . '</td>';
        $html .= '</tr>'."\n";          

        $elementId = $element->getHtmlId();
        $html .= '<script type="text/javascript">
            
                    Event.observe(window, "load", function(){
                        var ratingSwitcher = new CheckSwitcher(
                            $("' . $elementId . '"),
                            $$(".rating-container"),
                            []
                        );
                    });
                  </script>';

        return $html;
    }
}
