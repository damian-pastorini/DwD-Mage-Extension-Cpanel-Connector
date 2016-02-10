<?php

/**
 *
 * DwD-CpanelConnector - Magento Extension
 *
 * @copyright Copyright (c) 2015 DwDesigner Inc. (http://www.dwdeveloper.com/)
 * @author Damian A. Pastorini - damian.pastorini@dwdeveloper.com
 *
 */

class DwD_CpanelConnector_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function attributeExists($data, $returnData=false)
    {
        $result = false;
        $attribute = Mage::getModel('catalog/resource_eav_attribute')->loadByCode($data['type'], $data['code']);
        if(is_object($attribute) && $attribute->getId()) {
            $result = true;
            if($returnData) {
                $result = $attribute;
            }
        }
        return $result;
    }

    public function getCartItemOptionValueByLabel($item, $label)
    {
        $product = $item->getProduct();
        $customOptions = $product->getTypeInstance(true)->getOrderOptions($product);
        $result = $this->getOptionValueByLabel($customOptions['options'], $label);
        return $result;
    }

    public function getOrderItemOptionValueByLabel($item, $label)
    {
        $opts = $item->getData('product_options');
        $options = unserialize($opts);
        $result = $this->getOptionValueByLabel($options['options'], $label);
        return $result;
    }

    public function getOptionValueByLabel($options, $label)
    {
        $result = false;
        foreach ($options as $option) {
            if($option['label'] == $label) {
                $result = $option['value'];
                break;
            }
        }
        return $result;
    }

}
