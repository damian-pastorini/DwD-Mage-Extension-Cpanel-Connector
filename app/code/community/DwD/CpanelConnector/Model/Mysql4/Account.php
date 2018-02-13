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
 * Class DwD_CpanelConnector_Model_Mysql4_Account
 */
class DwD_CpanelConnector_Model_Mysql4_Account extends Mage_Core_Model_Mysql4_Abstract
{

	public function _construct()
	{
		$this->_init('cpanel_connector/account', 'cpanel_user');
        $this->_isPkAutoIncrement = false;
	}

}
