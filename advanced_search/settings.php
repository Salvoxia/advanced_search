<?php

require_once("version.php");
require_once( "common/admin/admin_menu.php" );
$page = new Page( "Settings - Advanced Search" );

// save settings
if ($_POST['submit'])
{
	config::set('adv_search_show_combined', $_POST['always_combined']);
    config::set('adv_search_error_handling', $_POST['error_handling']);
    config::set('adv_search_replace_search', ($_POST['serch_replace'] == "on"?"true":"false"));
}

// get settings
$adv_search_show_combined = config::get('adv_search_show_combined');
if (strlen($adv_search_show_combined) < 2)
	$adv_search_show_combined = "default";
$adv_search_error_handling = config::get('adv_search_error_handling');
if (strlen($adv_search_error_handling) < 2)
	$adv_search_error_handling = "continue";
$adv_search_replace_search = config::get('adv_search_replace_search');
if ($adv_search_replace_search == "true")
	$adv_search_replace_search = true;
else
	$adv_search_replace_search = false;

$html .= "<div class=block-header2>Advanced Search Options</div>";
$html .= "<form id=options name=options method=post action=>";
$html .= "<table class=kb-table width=\"500\" border=\"0\" cellspacing=\"1\">";

$html .= "<tr><td><b>Show combined view (default = board's settings):</b></td><td width=\"150\"><select style=\"width: 100%;\" name=\"always_combined\">";
$html .= "<option value=\"always\"".($adv_search_show_combined == "always"?" selected=\"selected\"":"").">Always</option>";
$html .= "<option value=\"default\"".($adv_search_show_combined == "default"?" selected=\"selected\"":"").">Default</option>";
$html .= "<option value=\"never\"".($adv_search_show_combined == "never"?" selected=\"selected\"":"").">Never</option>";

$html .="</select></td></tr><tr><td><b>On error:</b></td><td><select style=\"width: 100%;\" name=\"error_handling\">";
$html .= "<option value=\"halt\"".($adv_search_error_handling == "halt"?" selected=\"selected\"":"").">Halt</option>";
$html .= "<option value=\"continue\"".($adv_search_error_handling == "continue"?" selected=\"selected\"":"").">Continue</option>";
$html .= "<option value=\"ignore\"".($adv_search_error_handling == "ignore"?" selected=\"selected\"":"").">Ignore</option>";

$html .="</select></td></tr><tr><td><b>Totally replace basic search:</b></td><td><input type=\"checkbox\" name=\"serch_replace\"".($adv_search_replace_search?" checked=\"checked\"":"")."></td></tr></table>";
$html .= "<table class=kb-subtable width=\"99%\"><tr><td><input type=submit name=submit value=\"Save\"></td></tr></table>";
$html .= "</form>";

$html .= "<hr><p class=\"kb-subtable\" align=\"right\"><i>Advanced Search by Sonya Rayner<br>e-mail: kazhkaz@kazhkaz.net<br>".ADV_SRCH_VERSION."</i></p>";   

$page->setContent( $html );
$page->addContext( $menubox->generate() );
$page->generate();
?>