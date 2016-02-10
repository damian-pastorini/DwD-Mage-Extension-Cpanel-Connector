<?php

/**
 *
 * DwD-CpanelConnector - Magento Extension
 *
 * @copyright Copyright (c) 2015 DwDesigner Inc. (http://www.dwdeveloper.com/)
 * @author Damian A. Pastorini - damian.pastorini@dwdeveloper.com
 *
 */

class DwD_CpanelConnector_Block_Adminhtml_Account_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'cpanel_user';
        $this->_blockGroup = 'cpanel_connector';
        $this->_controller = 'adminhtml_account';
        parent::__construct();
        $this->_updateButton('save', 'label', Mage::helper('cpanel_connector')->__('Save Account'));
        $this->_updateButton('delete', 'label', Mage::helper('cpanel_connector')->__('Delete Account'));
        $message = 'Are you sure you want to do this?. '.
            ' This will also delete the account in the hosting and all the data will be lost. '.
            'WARNING: you can not undo this action!';
        $this->_updateButton('delete', 'onclick', 'deleteConfirm(\''. Mage::helper('adminhtml')->__($message).'\', \''.$this->getDeleteUrl().'\')');
        $this->setFormActionUrl($this->getUrl('adminhtml/cpanel_accounts/save'));
    }

    /**
     * Get edit form container header text.
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('account_data')) {
            return Mage::helper('cpanel_connector')->__("Edit Account '%s'", $this->escapeHtml(Mage::registry('account_data')->getCpanelUser()));
        } else {
            return Mage::helper('cpanel_connector')->__('New Account');
        }
    }

}