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
 * Class DwD_CpanelConnector_Block_Adminhtml_Account
 */
class DwD_CpanelConnector_Block_Adminhtml_Account extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    /**
     * DwD_CpanelConnector_Block_Adminhtml_Account constructor.
     */
	public function __construct()
	{
        parent::__construct();
        $this->_blockGroup = 'cpanel_connector';
		$this->_controller = 'adminhtml_account';
		$this->_headerText = $this->__('cPanel Connector - Accounts Manager');
		$this->_addButtonLabel = $this->__('New Account');
	}

}
