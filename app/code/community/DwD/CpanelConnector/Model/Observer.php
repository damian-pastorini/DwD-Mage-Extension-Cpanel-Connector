<?php

/**
 *
 * DwD-CpanelConnector - Magento Extension
 *
 * @copyright Copyright (c) 2015 DwDesigner Inc. (http://www.dwdeveloper.com/)
 * @author Damian A. Pastorini - damian.pastorini@dwdeveloper.com
 *
 */

class DwD_CpanelConnector_Model_Observer
{

    public function checkAddedItem($observer)
    {
        $isEnabled = $this->getConfig('general/enabled');
        if($isEnabled) {
            $cart = $observer->getCart();
            $cartItems = $cart->getItems();
            $helper = Mage::helper('cpanel_connector');
            $optionLabel = $this->getConfig('frontend_config/main_domain_label');
            foreach ($cartItems as $key => $item) {
                $productId = $item->getProduct()->getId();
                $product = Mage::getModel('catalog/product')->load($productId);
                $associatedPackage = $product->getAssociatedPackage();
                $mainDomain = $helper->getCartItemOptionValueByLabel($item, $optionLabel);
                $availableDomain = $this->isAvailableDomain($mainDomain);
                if (!$associatedPackage || !$mainDomain || !$this->isValidDomain($mainDomain) || !$availableDomain) {
                    $cart->removeItem($item->getId());
                    Mage::getSingleton('checkout/session')->getMessages(true);
                    $clickHere = $helper->__('Click here to go back to the product.');
                    $link = ' <a href="' . $product->getProductUrl() . '">' . $clickHere . '</a>';
                    if (!$availableDomain) {
                        $notAvailable = $helper->__('The domain %s is not available.', $mainDomain);
                        $messageLink = $notAvailable . $link;
                    } else {
                        $invalid = $helper->__('Invalid domain or package.');
                        $messageLink = $invalid . $link;
                    }
                    Mage::getSingleton('checkout/session')->addError($messageLink);
                }
            }
        }
    }

    public function checkDomainAvailability($observer)
    {
        $isEnabled = $this->getConfig('general/enabled');
        if($isEnabled) {
            $order = $observer->getOrder();
            $orderItems = $order->getAllItems();
            $helper = Mage::helper('cpanel_connector');
            $optionLabel = $this->getConfig('frontend_config/main_domain_label');
            foreach ($orderItems as $key => $item) {
                $mainDomain = $helper->getCartItemOptionValueByLabel($item, $optionLabel);
                if ($mainDomain && !$this->isAvailableDomain($mainDomain)) {
                    $message = $helper->__('The domain %s is not available.', $mainDomain);
                    Mage::throwException($message);
                }
            }
        }
    }

    public function createCpanelAccount($observer)
    {
        $isEnabled = $this->getConfig('general/enabled');
        $createAccountAutomatically = $this->getConfig('sales/create_account_automatically');
        if($createAccountAutomatically && $isEnabled) {
            $invoice = $observer->getInvoiceItem()->getOrderItem();
            $order = $invoice->getOrder();
            $orderItems = $order->getAllItems();
            $helper = Mage::helper('cpanel_connector');
            $optionLabel = $this->getConfig('frontend_config/main_domain_label');
            $defaultPackage = $this->getConfig('sales/default_config');
            $api = Mage::getModel('cpanel_connector/api');
            $email = $order->getBillingAddress()->getEmail();
            $customerId = $order->getCustomerId();
            $password = $this->generatePassword($email);
            $accountData = array(
                'package' => $defaultPackage,
                'email' => $email,
                'password' => $password
            );
            foreach ($orderItems as $key => $item) {
                $productId = $item->getProduct()->getId();
                $product = Mage::getModel('catalog/product')->load($productId);
                $associatedPackage = $product->getAssociatedPackage();
                $mainDomain = $helper->getOrderItemOptionValueByLabel($item, $optionLabel);
                if($mainDomain) {
                    $accountName = $this->generateAccountName($mainDomain);
                    $model = Mage::getModel('cpanel_connector/account');
                    // check if the account is already created:
                    $model->load($accountName);
                    if(!$model->getMagentoUserId() && $this->isAvailableDomain($mainDomain)) {
                        $accountData['domain'] = $mainDomain;
                        $accountData['package'] = $associatedPackage;
                        $accountData['cpanel_user'] = $accountName;
                        $result = $api->saveAccount($accountName, $accountData);
                        if(isset($result['status']) && $result['status']==1) {
                            $model->setCpanelUser($accountName);
                            $model->setMagentoUserId($customerId);
                            $model->save();
                            $emailHelper = Mage::helper('cpanel_connector/email');
                            $templateVariables = array(
                                'account_email' => $email,
                                'account_domain' => $mainDomain,
                                'account_user' => $accountName,
                                'account_password'=> $password
                            );
                            $emailHelper->sendCpanelAccountCreatedEmail($email, $templateVariables);
                        } else {
                            $message = $helper->__('There was an error creating the cPanel account, please try again o create the account manually.')
                                .' '.$helper->__($result['statusmsg']);
                            Mage::getSingleton('adminhtml/session')->addError($message);
                            Mage::log('CPANEL CONNECTOR: ERROR - '.$message);
                            // Mage::throwException($message);
                        }
                    } else {
                        $message = $helper->__('The domain %s is not available.', $mainDomain);
                        Mage::getSingleton('adminhtml/session')->addError($message);
                        Mage::log('CPANEL CONNECTOR: ERROR - '.$message);
                        // Mage::throwException($message);
                    }
                }
            }
        }
    }

    public function generateAccountName($domain)
    {
        // clean domain:
        $cleanDomain = str_replace(array('.','-', '_'), '', $domain);
        // limit account name:
        $accountName = substr($cleanDomain, 0, 13);
        // added "cp" to totally avoid reserved names accounts:
        return 'cp'.$accountName;
    }

    public function isValidDomain($domain)
    {
        $result = true;
        if(!filter_var('http://'.$domain, FILTER_VALIDATE_URL)) { // using PHP5 filters.
        // if(!preg_match("/^([-a-z0-9]{2,100})\.([a-z\.]{2,8})$/i", $domain)) {
            $result = false;
        }
        return $result;
    }

    public function isAvailableDomain($domain)
    {
        $result = false;
        $isEnabled = $this->getConfig('general/enabled');
        if($isEnabled) {
            $api = Mage::getModel('cpanel_connector/api');
            if ($api->getApi()) {
                $apiCallResult = $api->getApi()->listaccts('domain', $domain);
                if (!isset($apiCallResult['acct'])) {
                    $result = true;
                }
            }
        }
        return $result;
    }

    public function generatePassword($email)
    {
        $password = crypt($email, 'DW');
        // Mage::log('CPANEL CONNECTOR: Temp password: '.$password, null, 'cpanel_connector.log', true);
        return $password;
    }

    public function getConfig($path)
    {
        return Mage::getStoreConfig('cpanel_connector/'.$path);
    }

}
