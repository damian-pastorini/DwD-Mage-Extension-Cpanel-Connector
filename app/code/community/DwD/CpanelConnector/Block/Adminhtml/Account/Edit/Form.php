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
 * Class DwD_CpanelConnector_Block_Adminhtml_Account_Edit_Form
 */
class DwD_CpanelConnector_Block_Adminhtml_Account_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected $api = false;

    /**
     * DwD_CpanelConnector_Block_Adminhtml_Account_Edit_Form constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('account_form');
        $this->setTitle($this->__('Account Information'));
    }

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
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = $this->createForm();
        $fieldset = $form->addFieldset('account_form', array('legend' => $this->__('General')));
        $serverData = false;
        $registryData = Mage::registry('account_data');
        $helper = $this->getFieldsHelper();
        $disabled = true;
        if ($registryData) {
            $api = $this->getApi();
            $cpanelUser = $registryData->getCpanelUser();
            $cpanelData = $api->getAccount($cpanelUser);
            $cpanelData['package'] = str_replace(' ', '', strtolower($cpanelData['plan']));
            $customerId = $registryData->getMagentoUserId();
            $customer = $this->loadCustomerById($customerId);
            if($customer->getId()) {
                $cpanelData['magento_user_email'] = $customer->getEmail();
            }
            $cpanelData['is_edit'] = 1;
            $serverData = array_merge($registryData->getData(), $cpanelData);
            $fieldset->addField('is_edit', 'hidden', array('name' => 'is_edit'));
        } else {
            $disabled = false;
        }
        $fieldsConfigDataArray = $helper->getCpanelFieldsConfig($disabled);
        $fieldset->addField('magento_user_email', 'text', array(
            'label' => $this->__('Magento User E-mail'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'magento_user_email',
            'disabled' => $disabled
        ));
        if (!$registryData) {
            if ($this->isSingleStoreMode()) {
                $fieldset->addField('website_id', 'hidden', array('name' => 'website_id'));
                $websiteId = $this->getWebsiteId();
                $serverData = array('website_id' => $websiteId);
            } else {
                $websites = array();
                foreach ($this->getWebsites() as $website) {
                    $websites[$website->getId()] = !is_null($website->getDefaultStore());
                }
                $prefix = $form->getHtmlIdPrefix();
                $afterElementHtml =
                    '<script type="text/javascript">'
                    . "
                var {$prefix}_websites = " . Mage::helper('core')->jsonEncode($websites) .";
                Validation.add(
                    'validate-website-has-store',
                    '" . $this->__('Please select a website which contains store view') . "',
                    function(v, elem){
                        return {$prefix}_websites[elem.value] == true;
                    }
                );
                Element.observe('{$prefix}website_id', 'change', function(){
                    Validation.validate($('{$prefix}website_id'))
                }.bind($('{$prefix}website_id')));
                "
                    . '</script>'
                ;
                $fieldset->addField('website_id', 'select', array(
                    'label' => 'Magento user associated to website',
                    'options'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(false),
                    'class' => 'validate-website-has-store',
                    'after_element_html' => $afterElementHtml
                ));
                $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
                $form->getElement('website_id')->setRenderer($renderer);
            }
            $fieldset->addField('password', 'text', array(
                'label' => $this->__('cPanel Account Password'),
                'class' => 'required-entry',
                'required' => true,
                'name' => 'password',
            ));
        }
        $fieldset->addField('cpanel_user', 'text', array(
            'label' => $this->__('cPanel User'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'cpanel_user',
            'disabled' => $disabled,
        ));
        foreach ($fieldsConfigDataArray as $fieldKey => $fieldConfig) {
            $preparedFieldConfig = array(
                'label' => $fieldConfig['label'],
                'class' => 'required-entry',
                'required' => true,
                'name' => $fieldKey,
            );
            if($fieldConfig['type']=='select') {
                $preparedFieldConfig['values'] = $fieldConfig['values'];
            }
            if($fieldConfig['disabled']) {
                $preparedFieldConfig['disabled'] = true;
            }
            $fieldset->addField($fieldKey, $fieldConfig['type'], $preparedFieldConfig);
        }
        if ($serverData) {
            $form->setValues($serverData);
        }
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * @return Varien_Data_Form
     */
    protected function createForm()
    {
        $formData = array(
            'id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post'
        );
        $form = new Varien_Data_Form($formData);
        return $form;
    }

    /**
     * @return Mage_Core_Helper_Abstract
     */
    public function getFieldsHelper()
    {
        return Mage::helper('cpanel_connector/form_fields');
    }

    /**
     * @return false|Mage_Core_Model_Abstract
     */
    public function getCustomerModel()
    {
        return Mage::getModel('customer/customer');
    }

    /**
     * @param $customerId
     * @return false|Mage_Core_Model_Abstract
     */
    public function loadCustomerById($customerId)
    {
        $customerModel = $this->getCustomerModel();
        $customerModel->load($customerId);
        return $customerModel;
    }

    /**
     * @return bool
     */
    public function isSingleStoreMode()
    {
        return Mage::app()->isSingleStoreMode();
    }

    /**
     * @return int|null|string
     */
    public function getWebsiteId()
    {
        return Mage::app()->getStore(true)->getWebsiteId();
    }

    /**
     * @return array
     */
    public function getWebsites()
    {
        return Mage::app()->getWebsites(true);
    }

}
