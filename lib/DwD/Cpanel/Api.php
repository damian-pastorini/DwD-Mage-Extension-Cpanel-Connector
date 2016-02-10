<?php

/**
 *
 * DwD-CpanelConnector - Magento Extension
 *
 * @copyright Copyright (c) 2015 DwDesigner Inc. (http://www.dwdeveloper.com/)
 * @author Damian A. Pastorini - damian.pastorini@dwdeveloper.com
 *
 */

require_once('xmlapi.php');

class Lib_DwD_Cpanel_Api extends xmlapi
{

    public function xmlapi_query($function, $vars=array())
    {
        $apiCallResult = parent::xmlapi_query($function, $vars);
        $apiCallResultJson = json_encode($apiCallResult);
        $apiCallResultArray = json_decode($apiCallResultJson, true);
        return $apiCallResultArray;
    }

}
