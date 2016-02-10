<?php

/**
 *
 * DwD-CpanelConnector - Magento Extension
 *
 * @copyright Copyright (c) 2015 DwDesigner Inc. (http://www.dwdeveloper.com/)
 * @author Damian A. Pastorini - damian.pastorini@dwdeveloper.com
 *
 */

class DwD_CpanelConnector_Helper_Form_Fields extends Mage_Core_Helper_Abstract
{

    public function getCpanelFieldsConfig($userEdition = false)
    {
        $yesNo = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();
        $plans = Mage::getModel('cpanel_connector/source_package')->toOptionArray();
        // TODO: in future versions we will allow to change everything from here.
        $result = array(
            'domain' => array('type'=>'text', 'label'=>'Main Domain'), // 'mydomain.com',
            'email' => array('type'=>'text', 'label'=>'CPANEL Administrator E-mail'), // 'my-email@test.com',
            // NOTE: plan was replaced by package in magento to match the selected value in the options / select box.
            'package' => array('type'=>'select', 'label'=>'Package', 'values'=>$plans), // 'plan' => 'package_name',
            'suspended' => array('type'=>'select', 'label'=>'Suspended', 'values'=>$yesNo), // '0',
            'backup' => array('type'=>'select', 'label'=>'Backup', 'values'=>$yesNo, 'disabled'=>true), // '0',
            'disklimit' => array('type'=>'text', 'label'=>'Disk Limit', 'disabled'=>true), // '100M',
            'diskused' => array('type'=>'text', 'label'=>'Disk Used', 'disabled'=>true), // '20M',
            'ip' => array('type'=>'text', 'label'=>'IP', 'disabled'=>true), // '127.0.0.1',
            'is_locked' => array('type'=>'select', 'label'=>'Is Locked', 'values'=>$yesNo, 'disabled'=>true), // '0',
            'legacy_backup' => array('type'=>'select', 'label'=>'Legacy Backup', 'values'=>$yesNo, 'disabled'=>true), // '0',
            // 'max_defer_fail_percentage' => '*unknown*',
            'max_email_per_hour' => array('type'=>'text', 'label'=>'Max Emails Per Hour', 'disabled'=>true), // '*unknown*',
            // 'maxaddons' => '*unknown*',
            'maxftp' => array('type'=>'text', 'label'=>'Max FTP', 'disabled'=>true), // 'unlimited',
            'maxlst' => array('type'=>'text', 'label'=>'Max LST', 'disabled'=>true), // 'unlimited',
            'maxparked' => array('type'=>'text', 'label'=>'Max Parked Domains', 'disabled'=>true), // '*unknown*',
            'maxpop' => array('type'=>'text', 'label'=>'Max POP', 'disabled'=>true), // 'unlimited',
            'maxsql' => array('type'=>'text', 'label'=>'Max SQL', 'disabled'=>true), // 'unlimited',
            'maxsub' => array('type'=>'text', 'label'=>'Max Sub-Domains', 'disabled'=>true), // 'unlimited',
            // 'min_defer_fail_to_trigger_protection' => '5',
            // 'owner' => 'owner-account-name',
            // 'partition' => 'home',
            // 'shell' => 'bin-noshell-path',
            'startdate' => array('type'=>'text', 'label'=>'Start Date', 'disabled'=>true), // '11 Jan 22 17:17',
            // 'suspendreason' => 'not suspended',
            // 'suspendtime' => array (),
            // 'theme' => 'x3',
            // 'unix_startdate' => '1295738253',
            // 'user' => 'cpanel-account-user'
        );
        if($userEdition) {
            $result['domain']['disabled'] = true;
            $result['email']['disabled'] = true;
        }
        return $result;
    }

}
