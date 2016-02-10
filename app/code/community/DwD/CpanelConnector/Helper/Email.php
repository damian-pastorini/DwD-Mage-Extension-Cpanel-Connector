<?php

/**
 *
 * DwD-CpanelConnector - Magento Extension
 *
 * @copyright Copyright (c) 2015 DwDesigner Inc. (http://www.dwdeveloper.com/)
 * @author Damian A. Pastorini - damian.pastorini@dwdeveloper.com
 *
 */

class DwD_CpanelConnector_Helper_Email extends Mage_Core_Helper_Abstract
{

    public function sendCpanelAccountCreatedEmail($sendTo, $emailTemplateVariables)
    {
        $result = false;
        if(Mage::getStoreConfig('cpanel_connector/emails/cpanel_account_created_automatically')) {
            try {
                // load the html file 'cpanel_connector_account_created.html'
                // location: app/locale/en_US/template/email/cpanel_connector_account_created.html
                $emailTemplate = Mage::getModel('core/email_template')->loadByCode('cPanel Account Created');
                if(!$emailTemplate->getData()) {
                    $emailTemplate = Mage::getModel('core/email_template')->loadDefault('cpanel_account_created');
                }
                // process template:
                $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                // set email data:
                $senderName = Mage::getStoreConfig('cpanel_connector/emails/sender_name');
                $emailTemplate->setSenderName($senderName);
                $emailTemplate->setSenderEmail($sendTo);
                $subject = Mage::getStoreConfig('cpanel_connector/emails/subject');
                $emailTemplate->setTemplateSubject($subject);
                // send email
                $emailTemplate->send($sendTo, $subject, $emailTemplateVariables);
                $result = true;
            } catch (Exception $e) {
                Mage::log('CPANEL CONNECTOR NOTIFICATION ERROR: '.$e->getMessage());
            }
        }
        return $result;
    }

}
