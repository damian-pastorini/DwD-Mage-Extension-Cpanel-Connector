<?php

/**
 *
 * DwD-CpanelConnector - Magento Extension
 *
 * @copyright Copyright (c) 2017 DwDeveloper (http://www.dwdeveloper.com/)
 * @author Damian A. Pastorini - damian.pastorini@dwdeveloper.com
 *
 */

/**
 * Class DwD_CpanelConnector_Block_Adminhtml_Account_Edit_Form_Element_Useremail
 */
class DwD_CpanelConnector_Block_Adminhtml_Account_Edit_Form_Element_Useremail
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function getCustomerModel()
    {
        return Mage::getModel('customer/customer');
    }

    public function render(Varien_Object $element)
    {
        $html = '';
        $magentoUserId = $element->getMagentoUserId();
        if($magentoUserId) {
            $magentoUser = $this->getCustomerModel()->load($magentoUserId);
            if($magentoUser->getId()) {
                $html = $magentoUser->getEmail();
            }
        }
        return $html;
    }

}
