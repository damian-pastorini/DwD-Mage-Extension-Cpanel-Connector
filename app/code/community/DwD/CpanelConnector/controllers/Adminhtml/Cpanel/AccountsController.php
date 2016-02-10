<?php

/**
 *
 * DwD-CpanelConnector - Magento Extension
 *
 * @copyright Copyright (c) 2015 DwDesigner Inc. (http://www.dwdeveloper.com/)
 * @author Damian A. Pastorini - damian.pastorini@dwdeveloper.com
 *
 */

class DwD_CpanelConnector_Adminhtml_Cpanel_AccountsController extends Mage_Adminhtml_Controller_Action
{

    protected $api = false;

    public function getApi()
    {
        if(!$this->api) {
            $this->api = Mage::getModel('cpanel_connector/api');
        }
        return $this->api;
    }

	public function indexAction()
	{
        $title = Mage::helper('adminhtml')->__('Accounts Manager');
        $this->_title($title);
        $this->loadLayout();
        $this->_setActiveMenu('cpanel_connector/cpanel_connector_accounts');
        $this->renderLayout();
	}

	public function editAction()
	{
        $this->_title($this->__('Edit Account'));
        $id = $this->getRequest()->getParam('cpanel_user');
        $model = Mage::getModel('cpanel_connector/account')->load($id);
        if ($model->getId()) {
            Mage::register('account_data', $model);
            $this->loadLayout();
            $this->_setActiveMenu('cpanel_connector/cpanel_connector_accounts');
            $editBlock = $this->getLayout()->createBlock('cpanel_connector/adminhtml_account_edit');
            $this->_addContent($editBlock);
            $this->renderLayout();
        } else {
            $errorMessage = Mage::helper('cpanel_connector')->__('Account does not exist.');
            Mage::getSingleton('adminhtml/session')->addError($errorMessage);
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
            $data = $request->getPost();
            $isEdit = (bool) $data['is_edit'];
            $api = $this->getApi();
			$connectorAccount = Mage::getModel('cpanel_connector/account');
            $cpanelAccountName = $this->getRequest()->getParam('cpanel_user');
            $cpanelAccount = false;
            $magentoUser = Mage::getModel('customer/customer');
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
                $errorMessage = Mage::helper('cpanel_connector')->__('The customer do not exist.');
                Mage::getSingleton('adminhtml/session')->addError($errorMessage);
                return $this->_redirect('*/*/');
            }
			try {
                if($cpanelAccount && $cpanelAccount['package']!=$data['package']) {
                    // update account package:
                    $result = $api->getApi()->changepackage($cpanelAccountName, $data['package']);
                }
                if($cpanelAccount && $cpanelAccount['email']!=$data['email']) {
                    // change cpanel account admin email:
                    $api->modifyacct($cpanelAccountName, array('contactemail'=> $data['email']));
                }
                if($cpanelAccount && $cpanelAccount['suspended']!=$data['suspended']) {
                    // suspend account:
                    $result = $api->getApi()->suspendacct($cpanelAccountName, 'Suspended by administrator.');
                }
                if(!$cpanelAccount) {
                    // try to create the cpanel account:
                    $result = $api->saveAccount($cpanelAccountName, $data);
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
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cpanel_connector')->__('Account successfully saved.'));
                    Mage::getSingleton('adminhtml/session')->setFormData(false);
                    if ($this->getRequest()->getParam('back')) {
                        return $this->_redirect('*/*/edit', array('id' => $connectorAccount->getId()));
                    }
                    return $this->_redirect('*/*/');
                }
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setFormData($data);
                return $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
        $errorMessage = Mage::helper('cpanel_connector')->__('There was an error.');
		Mage::getSingleton('adminhtml/session')->addError($errorMessage);
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
                    $errorMessage = Mage::helper('adminhtml')->__('The account %s could not be deleted from the cPanel host.', $cpanelAccountName);
                    Mage::log('CPANEL CONNECTOR: ERROR - '.$errorMessage, null, 'cpanel_connector.log', true);
                    Mage::getSingleton('adminhtml/session')->addError($errorMessage);
                }
                $model = Mage::getModel('cpanel_connector/account');
                $model->load($cpanelAccountName)->delete();
                $successMessage = Mage::helper('adminhtml')->__('The account was successfully deleted from Magento.');
                Mage::getSingleton('adminhtml/session')->addSuccess($successMessage);
                return $this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $params = array('cpanel_user' => $this->getRequest()->getParam('cpanel_user'));
				return $this->_redirect('*/*/edit', $params);
			}
		}
        $errorMessage = Mage::helper('cpanel_connector')->__('There was an error.');
        Mage::getSingleton('adminhtml/session')->addError($errorMessage);
		$this->_redirect('*/*/');
	}

	public function massStatusAction()
	{
        $accountNames = $this->getRequest()->getParam('cpanel_user');
		if(!is_array($accountNames)) {
			Mage::getSingleton('adminhtml/session')->addError($this->__('Please select account(s).'));
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
