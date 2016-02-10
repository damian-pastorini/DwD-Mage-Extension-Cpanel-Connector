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

// connector accounts table:

$sql=<<<SQLTEXT

CREATE TABLE IF NOT EXISTS cpanel_accounts (
	cpanel_user VARCHAR(255) NOT NULL,
	magento_user_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (magento_user_id, cpanel_user),
	UNIQUE INDEX cpanel_user (cpanel_user)
)ENGINE=InnoDB;

SQLTEXT;

$installer->run($sql);

// product attribute:

$entityTypeId = $installer->getEntityTypeId('catalog_product');
$attributeData = array('type' => $entityTypeId, 'code' => 'associated_package');
$attribute = Mage::helper('cpanel_connector')->attributeExists($attributeData, true);
if(!$attribute) {
    $installer->addAttribute($entityTypeId, 'associated_package', array(
        'attribute_set'                 => 'Default',
        'group'                         => 'General',
        'user_defined'                  => true,
        'label'                         => 'Associated Package',
        'type'                          => 'varchar',
        'input'                         => 'select',
        'source'                        => 'cpanel_connector/source_package',
        'global'                        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible'                       => true,
        'required'                      => true,
        'searchable'                    => true,
        'filterable'                    => true,
        'visible_on_front'              => true,
        'visible_in_advanced_search'    => true,
        'used_in_product_listing'       => true,
        'unique'                        => false,
        'used_for_sort_by'              => true,
    ));
}

$installer->endSetup();
