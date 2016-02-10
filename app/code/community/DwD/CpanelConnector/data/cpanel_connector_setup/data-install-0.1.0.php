<?php

/**
 *
 * DwD-CpanelConnector - Magento Extension
 *
 * @copyright Copyright (c) 2015 DwDesigner Inc. (http://www.dwdeveloper.com/)
 * @author Damian A. Pastorini - damian.pastorini@dwdeveloper.com
 *
 */

$installer = $this;
$installer->startSetup();

$configPath = 'cpanel_connector/sales/default_admin_email';
$value = Mage::getStoreConfig('trans_email/ident_general/email');
$installer->setConfigData($configPath, $value);

$installer->endSetup();
