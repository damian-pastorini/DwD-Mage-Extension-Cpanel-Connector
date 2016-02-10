<?php

/**
 *
 * DwD-CpanelConnector - Magento Extension
 *
 * @copyright Copyright (c) 2015 DwDesigner Inc. (http://www.dwdeveloper.com/)
 * @author Damian A. Pastorini - damian.pastorini@dwdeveloper.com
 *
 */

require_once(Mage::getBaseDir().'/lib/DwD/Cpanel/Api.php');

class DwD_CpanelConnector_Model_Api
{

    protected $api = false;
    protected $host = false;
    protected $rootUser = false;
    protected $rootPassword = false;
    protected $defaultPackage = false;
    protected $defaultAdminContact = false;
    public $helper;

	public function __construct()
	{
        // connection data:
        $this->host = $this->getConfig('general/host');
        $this->rootUser = $this->getConfig('general/root_user');
        $this->rootPassword = $this->getConfig('general/root_password');
        $this->defaultPackage = $this->getConfig('sales/default_package');
        $this->defaultAdminContact = $this->getConfig('sales/default_admin_contact');
        if($this->host && $this->rootUser && $this->rootPassword) {
            // api instance:
            $api = new Lib_DwD_Cpanel_Api($this->host, $this->rootUser, $this->rootPassword);
            $this->setApi($api);
        }
        $this->helper = Mage::helper('cpanel_connector');
	}

    public function getConfig($path)
    {
        return Mage::getStoreConfig('cpanel_connector/'.$path);
    }

    /**
     * @param Lib_DwD_Cpanel_Api $api
     */
    public function setApi(Lib_DwD_Cpanel_Api $api)
    {
        $this->api = $api;
    }

    /**
     * @return Lib_DwD_Cpanel_Api
     */
    public function getApi()
    {
        return $this->api;
    }

    public function getPackagesList()
    {
        $result = false;
        $api = $this->getApi();
        if($api) {
            $callResult = $api->listpkgs();
            if(isset($callResult['package'])) {
                $result = $callResult['package'];
            }
        }
        return $result;
    }

    public function getPackageName($packageCode)
    {
        $result = false;
        $packagesResource = Mage::getModel('cpanel_connector/source_package');
        $packagesOptions = $packagesResource->toOptionArray();
        if(is_array($packagesOptions) && isset($packagesOptions[$packageCode])) {
            $result = $packagesOptions[$packageCode];
        }
        return $result;
    }

    public function getAccountAdminEmail($accountData)
    {
        $result = $this->defaultAdminContact;
        if(isset($accountData['email'])) {
            $result = $accountData['email'];
        }
        return $result;
    }

    public function getAccountPackage($accountData)
    {
        $packageCode = $this->defaultAdminContact;
        if(isset($accountData['package'])) {
            $packageCode = $accountData['package'];
        }
        $result = $this->getPackageName($packageCode);
        return $result;
    }

    public function getAccountsList()
    {
        $result = false;
        $api = $this->getApi();
        if($api) {
            $callResult = $api->listaccts();
            if(isset($callResult['status']) && $callResult['status'] == 1) {
                $result = $callResult['acct'];
            }
        }
        return $result;
    }

    public function getAccount($accountName)
    {
        $api = $this->getApi();
        $result = false;
        if($api) {
            $callResult = $api->accountsummary($accountName);
            if(isset($callResult['status']) && $callResult['status'] == 1){
                $result = $callResult['acct'];
            }
        }
        return $result;
    }

    public function saveAccount($accountName, $accountData)
    {
        $api = $this->getApi();
        $result = false;
        if($api) {
            $accountData['username'] = $accountData['cpanel_user'];
            $accountAdminEmail = $this->getAccountAdminEmail($accountData);
            $accountPackage = $this->getAccountPackage($accountData);
            if($accountPackage) {
                $callResult = $api->createacct($accountData);
                if(
                    isset($callResult['result'])
                    && isset($callResult['result']['status'])
                    && $callResult['result']['status'] == 1
                ) {
                    $api->changepackage($accountName, $accountPackage);
                    $api->modifyacct($accountName, array('contactemail'=> $accountAdminEmail));
                    $result = $callResult['result'];
                } else {
                    $result = $callResult['result'];
                }
            }
        }
        return $result;
    }

    public function deleteAccount($accountName)
    {
        $api = $this->getApi();
        $result = false;
        if($api) {
            $callResult = $api->removeacct($accountName);
            if(
                isset($callResult['result'])
                && isset($callResult['result']['status'])
                && $callResult['result']['status'] == 1
            ) {
                $result = $callResult['result'];
            }
        }
        return $result;
    }
	
}
