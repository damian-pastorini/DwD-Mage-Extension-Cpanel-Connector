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
 * Class DwD_CpanelConnector_Helper_Email
 */
class DwD_CpanelConnector_Helper_Email extends Mage_Core_Helper_Abstract
{

    /**
     * @param $sendTo
     * @param $emailTemplateVariables
     * @return bool
     */
    public function sendCpanelAccountCreatedEmail($sendTo, $emailTemplateVariables)
    {
        $result = false;
        if($this->isAutomaticallyCreated()) {
            try {
                // email data:
                $senderName = $this->getSenderMail();
                $subject = $this->getSubject();
                // load the html file 'cpanel_connector_account_created.html'
                // location: app/locale/en_US/template/email/cpanel_connector_account_created.html
                $emailTemplate = $this->getTemplateModel()->loadByCode('cPanel Account Created');
                if(!$emailTemplate->getData()) {
                    $emailTemplate = $this->getTemplateModel()->loadDefault('cpanel_account_created');
                }
                // process template:
                $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                // set email data:
                $emailTemplate->setSenderName($senderName);
                $emailTemplate->setSenderEmail($sendTo);
                $emailTemplate->setTemplateSubject($subject);
                // send email:
                $emailTemplate->send($sendTo, $subject, $emailTemplateVariables);
                $result = true;
            } catch (Exception $e) {
                Mage::log('CPANEL CONNECTOR NOTIFICATION ERROR: '.$e->getMessage());
            }
        }
        return $result;
    }

    /**
     * @return mixed
     */
    public function isAutomaticallyCreated()
    {
        return Mage::getStoreConfig('cpanel_connector/emails/cpanel_account_created_automatically');
    }

    /**
     * @return false|Mage_Core_Model_Abstract
     */
    public function getTemplateModel()
    {
        return Mage::getModel('core/email_template');
    }

    /**
     * @return mixed
     */
    public function getSenderName()
    {
        return Mage::getStoreConfig('cpanel_connector/emails/sender_name');
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return Mage::getStoreConfig('cpanel_connector/emails/subject');
    }

}
