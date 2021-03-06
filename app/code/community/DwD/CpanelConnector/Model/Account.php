<?php

/**
 *
 * DwD-CpanelConnector - Magento Extension
 *
 * @copyright Copyright (c) 2015 DwDesigner Inc. (http://www.dwdeveloper.com/)
 * @author Damian A. Pastorini - damian.pastorini@dwdeveloper.com
 *
 */

class DwD_CpanelConnector_Model_Account extends Mage_Core_Model_Abstract
{
	
	public function _construct()
	{
		$this->_init('cpanel_connector/account');
	}
	
}
