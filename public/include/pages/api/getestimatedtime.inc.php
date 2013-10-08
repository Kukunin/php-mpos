<?php

// Make sure we are called from index.php
if (!defined('SECURITY')) die('Hacking attempt');

// Check if the API is activated
$api->isActive();

// Check user token
$user_id = $api->checkAccess($user->checkApiKey($_REQUEST['api_key']), @$_REQUEST['id']);

// Estimated time to find the next block
$iCurrentPoolHashrate = $statistics->getCurrentHashrate() * 1000;
$bitcoin->can_connect() === true ? $dEstimatedTime = $bitcoin->getestimatedtime($iCurrentPoolHashrate) : $dEstimatedTime = 0;

// Output JSON format
echo $api->get_json($dEstimatedTime);

// Supress master template
$supress_master = 1;
?>
