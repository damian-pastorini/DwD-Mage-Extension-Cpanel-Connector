<?php

/**
 *
 * DwD-CpanelConnector - Magento Extension
 *
 * @copyright Copyright (c) 2015 DwDesigner Inc. (http://www.dwdeveloper.com/)
 * @author Damian A. Pastorini - damian.pastorini@dwdeveloper.com
 *
 */

class DwD_CpanelConnector_Block_Adminhtml_Account_Edit_Form_Element_Useremail
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $element)
    {
        $html = '';
        $magentoUserId = $element->getMagentoUserId();
        if($magentoUserId) {
            $magentoUser = Mage::getModel('customer/customer')->load($magentoUserId);
            if($magentoUser->getId()) {
                $html = $magentoUser->getEmail();
            }
        }
        return $html;
    }

}
