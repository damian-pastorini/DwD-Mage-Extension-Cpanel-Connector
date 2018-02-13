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
 * Class DwD_CpanelConnector_Model_Source_Package
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

	public function getHelper()
    {
        return Mage::helper('cpanel_connector');
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $result = array('0' => $this->getHelper()->__('Select a package...'));
        if($this->_packagesList) {
            foreach ($this->_packagesList as $package) {
                $packageId = str_replace(' ', '', strtolower($package['name']));
                $result[$packageId] = $package['name'];
            }
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $emptyOption = $this->getHelper()->__('Select a package...');
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
