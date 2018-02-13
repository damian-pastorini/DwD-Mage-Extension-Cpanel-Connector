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
 * Class DwD_CpanelConnector_Helper_Data
 */
class DwD_CpanelConnector_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * @return false|Mage_Core_Model_Abstract
     */
    public function getAttributesModel()
    {
        return Mage::getModel('catalog/resource_eav_attribute');
    }

    /**
     * @param $data
     * @param bool $returnData
     * @return bool
     */
    public function attributeExists($data, $returnData = false)
    {
        $result = false;
        $attribute = $this->getAttributesModel()->loadByCode($data['type'], $data['code']);
        if(is_object($attribute) && $attribute->getId()) {
            $result = true;
            if($returnData) {
                $result = $attribute;
            }
        }
        return $result;
    }

    /**
     * @param $item
     * @param $label
     * @return bool
     */
    public function getCartItemOptionValueByLabel($item, $label)
    {
        $product = $item->getProduct();
        $result = false;
        if($product) {
            $customOptions = $product->getTypeInstance(true)->getOrderOptions($product);
            $result = $this->getOptionValueByLabel($customOptions['options'], $label);
        }
        return $result;
    }

    /**
     * @param $item
     * @param $label
     * @return bool
     */
    public function getOrderItemOptionValueByLabel($item, $label)
    {
        $opts = $item->getData('product_options');
        $options = unserialize($opts);
        $result = $this->getOptionValueByLabel($options['options'], $label);
        return $result;
    }

    /**
     * @param $options
     * @param $label
     * @return bool
     */
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
