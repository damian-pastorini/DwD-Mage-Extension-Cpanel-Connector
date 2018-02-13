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
 * Class DwD_CpanelConnector_Adminhtml_Cpanel_AccountsController
 */
class DwD_CpanelConnector_Adminhtml_Cpanel_AccountsController extends Mage_Adminhtml_Controller_Action
{

    protected $api = false;

    /**
     * @return bool|false|Mage_Core_Model_Abstract
     */
    public function getApi()
    {
        if(!$this->api) {
            $this->api = Mage::getModel('cpanel_connector/api');
        }
        return $this->api;
    }

    /**
     * @return Mage_Core_Model_Abstract
     */
    public function getAdminSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }

    /**
     * @return false|Mage_Core_Model_Abstract
     */
    public function getAccountModel()
    {
        return Mage::getModel('cpanel_connector/account');
    }

    /**
     * @return false|Mage_Core_Model_Abstract
     */
    public function getCustomerModel()
    {
        return Mage::getModel('customer/customer');
    }

    /**
     * @return bool
     */
    public function isEdit()
    {
        return (bool) $this->getRequest()->getParam('is_edit');
    }

	public function indexAction()
	{
        $title = $this->__('Accounts Manager');
        $this->_title($title);
        $this->loadLayout();
        $this->_setActiveMenu('cpanel_connector/cpanel_connector_accounts');
        $this->renderLayout();
	}

	public function editAction()
	{
        $this->_title($this->__('Edit Account'));
        $id = $this->getRequest()->getParam('cpanel_user');
        $cpanelAccount = $this->getAccountModel()->load($id);
        if ($cpanelAccount->getId()) {
            Mage::register('account_data', $cpanelAccount);
            $this->loadLayout();
            $this->_setActiveMenu('cpanel_connector/cpanel_connector_accounts');
            $editBlock = $this->getLayout()->createBlock('cpanel_connector/adminhtml_account_edit');
            $this->_addContent($editBlock);
            $this->renderLayout();
        } else {
            $errorMessage = $this->__('Account does not exist.');
            $this->getAdminSession()->addError($errorMessage);
            $this->_redirect('*/*/');
        }
	}

	public function newAction()
	{
        $this->_title($this->__('New Account'));
        $this->loadLayout();
        $this->_setActiveMenu('cpanel_connector/cpanel_connector_accounts');
        $editBlock = $this->getLayout()->createBlock('cpanel_connector/adminhtml_account_edit');
        $this->_addContent($editBlock);
        $this->renderLayout();
	}

	public function saveAction()
	{
        $request = $this->getRequest();
        if ($request->isPost()) {
            $isEdit = (bool) $this->getRequest()->getParam('is_edit');
            if($this->isEdit()) {

            } else {

            }
            $api = $this->getApi();
			$connectorAccount = $this->getAccountModel();
            $cpanelAccountName = $this->getRequest()->getParam('cpanel_user');
            $cpanelAccount = false;
            $magentoUser = $this->getCustomerModel();
            if($cpanelAccountName && $isEdit) {
                $cpanelAccount = $api->getAccount($cpanelAccountName);
                $connectorAccount->load($cpanelAccountName);
            } else {
                $magentoUserEmail = $data['magento_user_email'];
                $websiteId = $data['website_id'];
                $store = Mage::app()->getWebsite($websiteId)->getDefaultStore();
                $magentoUser = $magentoUser->setStore($store);
                $magentoUser->loadByEmail($magentoUserEmail);
                if($magentoUser) {
                    $magentoUserId = $magentoUser->getId();
                }
            }
            $magentoUserIdFromConnector = $connectorAccount->getMagentoUserId();
            if(!$magentoUserId && $magentoUserIdFromConnector) {
                $magentoUserId = $magentoUserIdFromConnector;
                $magentoUser = $magentoUser->load($magentoUserId);
            }
            if(!$magentoUser->getId()) {
                $errorMessage = $this->__('The customer do not exist.');
                $this->getAdminSession()->addError($errorMessage);
                return $this->_redirect('*/*/');
            }
			try {
                if($cpanelAccount) {
                    if($cpanelAccount['package']!=$data['package']) {
                        // update account package:
                        $result = $api->getApi()->changepackage($cpanelAccountName, $data['package']);
                    }
                    if($cpanelAccount['email']!=$data['email']) {
                        // change cpanel account admin email:
                        $api->modifyacct($cpanelAccountName, array('contactemail'=> $data['email']));
                    }
                    if($cpanelAccount['suspended']!=$data['suspended']) {
                        // suspend account:
                        $result = $api->getApi()->suspendacct($cpanelAccountName, 'Suspended by administrator.');
                    }
                }
                if(!$cpanelAccount) {
                    // try to create the cpanel account:
                    $result = $api->saveAccount($cpanelAccountName, $data);
                    Mage::log($result, null, 'dap.log', true);
                }
                // if the cpanel account is successfully created we save the relation to the magento user:
                if(isset($result['status']) &&  $result['status'] == 1) {
                    $connectorData = array('magento_user_id' => $magentoUserId, 'cpanel_user' => $cpanelAccountName);
                    $connectorAccount->setData($connectorData);
                    $connectorAccount->save();
                    $emailHelper = Mage::helper('cpanel_connector/email');
                    $templateVariables = array(
                        'account_email' => $magentoUser->getEmail(),
                        'account_domain' => $data['domain'],
                        'account_user' => $cpanelAccountName,
                        'account_password'=> $data['password']
                    );
                    $emailHelper->sendCpanelAccountCreatedEmail($magentoUser->getEmail(), $templateVariables);
                    $this->getAdminSession()->addSuccess($this->__('Account successfully saved.'));
                    $this->getAdminSession()->setFormData(false);
                    if ($this->getRequest()->getParam('back')) {
                        return $this->_redirect('*/*/edit', array('id' => $connectorAccount->getId()));
                    }
                    return $this->_redirect('*/*/');
                }
			} catch (Exception $e) {
				$this->getAdminSession()->addError($e->getMessage());
				$this->getAdminSession()->setFormData($data);
                return $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
        $errorMessage = $this->__('There was an error.');
		$this->getAdminSession()->addError($errorMessage);
        return $this->_redirect('*/*/');
	}

	public function deleteAction()
	{
        $cpanelAccountName = $this->getRequest()->getParam('cpanel_user');
		if($cpanelAccountName) {
			try {
                $api = $this->getApi();
                // delete cpanel account first:
                $apiDelete = $api->deleteAccount($cpanelAccountName);
                if(!isset($apiDelete['status']) || $apiDelete['status'] != 1) {
                    // remove the cpanel account / magento user relation:
                    $errorMessage = $this->__('The account %s could not be deleted from the cPanel host.', $cpanelAccountName);
                    Mage::log('CPANEL CONNECTOR: ERROR - '.$errorMessage, null, 'cpanel_connector.log', true);
                    $this->getAdminSession()->addError($errorMessage);
                }
                $model = $this->getAccountModel();
                $model->load($cpanelAccountName)->delete();
                $successMessage = $this->__('The account was successfully deleted from Magento.');
                $this->getAdminSession()->addSuccess($successMessage);
                return $this->_redirect('*/*/');
			} catch (Exception $e) {
				$this->getAdminSession()->addError($e->getMessage());
                $params = array('cpanel_user' => $this->getRequest()->getParam('cpanel_user'));
				return $this->_redirect('*/*/edit', $params);
			}
		}
        $errorMessage = $this->__('There was an error.');
        $this->getAdminSession()->addError($errorMessage);
		$this->_redirect('*/*/');
	}

	public function massStatusAction()
	{
        $accountNames = $this->getRequest()->getParam('cpanel_user');
		if(!is_array($accountNames)) {
			$this->getAdminSession()->addError($this->__('Please select account(s).'));
		} else {
			try {
                $api = $this->getApi();
                $status = $this->getRequest()->getParam('status');
				foreach ($accountNames as $accountName) {
                    if($status == 1) {
                        $api->getApi()->unsuspendacct($accountName);
                    } else {
                        $api->getApi()->suspendacct($accountName);
                    }
				}
                $message = $this->__('%d account(s) were successfully updated', count($accountNames));
				$this->_getSession()->addSuccess($message);
			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}

}
