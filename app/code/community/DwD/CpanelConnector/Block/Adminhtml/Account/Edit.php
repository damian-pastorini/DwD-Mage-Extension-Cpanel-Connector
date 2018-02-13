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
 * Class DwD_CpanelConnector_Block_Adminhtml_Account_Edit
 */
class DwD_CpanelConnector_Block_Adminhtml_Account_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    protected $registry = false;

    /**
     * @return bool|mixed
     */
    public function getRegistry()
    {
        if(!$this->registry) {
            $this->registry = Mage::registry('account_data');
        }
        return $this->registry;
    }

    /**
     * DwD_CpanelConnector_Block_Adminhtml_Account_Edit constructor.
     */
    public function __construct()
    {
        $this->_objectId = 'cpanel_user';
        $this->_blockGroup = 'cpanel_connector';
        $this->_controller = 'adminhtml_account';
        parent::__construct();
        $this->_updateButton('save', 'label', $this->__('Save Account'));
        $this->_updateButton('delete', 'label', $this->__('Delete Account'));
        $message = 'Are you sure you want to do this?. '.
            ' This will also delete the account in the hosting and all the data will be lost. '.
            'WARNING: you can not undo this action!';
        $action = 'deleteConfirm(\''. $this->__($message).'\', \''.$this->getDeleteUrl().'\')';
        $this->_updateButton('delete', 'onclick', $action);
        $this->setFormActionUrl($this->getUrl('adminhtml/cpanel_accounts/save'));
    }

    /**
     * Get edit form container header text.
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->getRegistry()) {
            $accountData = $this->getRegistry()->getCpanelUser();
            $message = $this->__("Edit Account '%s'", $this->escapeHtml($accountData));
        } else {
            $message = $this->__('New Account');
        }
        return $this->__($message);
    }

}