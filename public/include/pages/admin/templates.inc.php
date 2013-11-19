<?php

// Make sure we are called from index.php
if (!defined('SECURITY')) die('Hacking attempt');

// Check user to ensure they are admin
if (!$user->isAuthenticated() || !$user->isAdmin($_SESSION['USERDATA']['id'])) {
  header("HTTP/1.1 404 Page not found");
  die("404 Page not found");
}

$aThemes = $template->getThemes();
$aTemplates = $aFlatTemplatesList = array();
foreach($aThemes as $sTheme) {
  $templates = $template->getTemplateFiles($sTheme);
  $templatesWithTheme = array();
  foreach($templates as $tpl_name) {
    $templatesWithTheme[] = $sTheme."/".$tpl_name;
  }
  $aFlatTemplatesList = array_merge($aFlatTemplatesList, $templatesWithTheme);
  $aTemplates[$sTheme] = array_combine($templatesWithTheme, $templates);
}

//Fetch current slug and template
$sTemplate = @$_REQUEST['template'];
if(!in_array($sTemplate, $aFlatTemplatesList)) {
  $aThemeTemplates = $aTemplates[THEME];
  $sTemplate = array_keys($aThemeTemplates);
  $sTemplate = $sTemplate[0];
}

$sOriginalTemplate = $template->getTemplateContent($sTemplate);

if (@$_REQUEST['do'] == 'save') {
  if ($template->updateEntry(@$_REQUEST['template'], @$_REQUEST['content'], @$_REQUEST['active'])) {
    $_SESSION['POPUP'][] = array('CONTENT' => 'Page updated', 'TYPE' => 'success');
  } else {
    $_SESSION['POPUP'][] = array('CONTENT' => 'Page update failed: ' . $template->getError(), 'TYPE' => 'errormsg');
  }
}

$oDatabaseTemplate = $template->getEntry($sTemplate);

if ( $oDatabaseTemplate === false ) {
  $_SESSION['POPUP'][] = array('CONTENT' => 'Can\'t fetch template from Database. Have you created `templates` table? Run 005_create_templates_table.sql from sql folder', 'TYPE' => 'errormsg');
}

$smarty->assign("TEMPLATES", $aTemplates);
$smarty->assign("CURRENT_TEMPLATE", $sTemplate);
$smarty->assign("ORIGINAL_TEMPLATE", $sOriginalTemplate);
$smarty->assign("DATABASE_TEMPLATE", $oDatabaseTemplate);
$smarty->assign("CONTENT", "default.tpl");
?>
