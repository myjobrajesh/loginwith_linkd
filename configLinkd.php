<?php
//Include LinkedIn client library 
require_once 'src/http.php';
require_once 'src/oauth_client.php';

/*
 * Configuration and setup LinkedIn API
 */
$apiKey = '784eeoj22l7r36';//InsertClientID;
$apiSecret = 'P6mWo80MSuhm69a1';//InsertClientSecret;
$redirectURL = 'http://localhost/projects/loginwith_linkd/';
$scope = 'r_basicprofile r_emailaddress'; //API permissions
?>
