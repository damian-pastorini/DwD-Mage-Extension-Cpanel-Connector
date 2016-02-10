<?php

/**
 *
 * DwD-CpanelConnector - Magento Extension
 *
 * @copyright Copyright (c) 2015 DwDesigner Inc. (http://www.dwdeveloper.com/)
 * @author Damian A. Pastorini - damian.pastorini@dwdeveloper.com
 *
 */

class DwD_CpanelConnector_Block_Adminhtml_Account extends Mage_Adminhtml_Block_Widget_Grid_Container
{

	public function __construct()
	{
        parent::__construct();
        $this->_blockGroup = 'cpanel_connector';
		$this->_controller = 'adminhtml_account';
		$this->_headerText = Mage::helper('cpanel_connector')->__('cPanel Connector - Accounts Manager');
		$this->_addButtonLabel = Mage::helper('cpanel_connector')->__('New Account');
	}

}
