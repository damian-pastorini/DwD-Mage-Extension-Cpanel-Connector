<?php

/**
 *
 * DwD-CpanelConnector - Magento Extension
 *
 * @copyright Copyright (c) 2015 DwDesigner Inc. (http://www.dwdeveloper.com/)
 * @author Damian A. Pastorini - damian.pastorini@dwdeveloper.com
 *
 */

class DwD_CpanelConnector_Model_Source_Package extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{

    protected $_packagesList = false;
    protected $options = false;

	public function __construct()
	{
        $api = Mage::getModel('cpanel_connector/api');
        $this->_packagesList = $api->getPackagesList();
	}

    public function toOptionArray()
    {
        $result = array('0' => Mage::helper('cpanel_connector')->__('Select a package...'));
        if($this->_packagesList) {
            foreach ($this->_packagesList as $package) {
                $packageId = str_replace(' ', '', strtolower($package['name']));
                $result[$packageId] = $package['name'];
            }
        }
        return $result;
    }

    public function getAllOptions()
    {
        if (!$this->_options) {
            $emptyOption = Mage::helper('cpanel_connector')->__('Select a package...');
            $options = array($emptyOption);
            if($this->_packagesList) {
                foreach ($this->_packagesList as $package) {
                    $packageId = str_replace(' ', '', strtolower($package['name']));
                    $options[] = array(
                        'value' => $packageId,
                        'label'=>$package['name']
                    );
                }
            }
            $this->_options = $options;
        }
        return $this->_options;
    }
	
}
