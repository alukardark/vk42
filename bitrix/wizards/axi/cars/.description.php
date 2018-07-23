<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if(!defined("WIZARD_DEFAULT_SITE_ID") && !empty($_REQUEST["wizardSiteID"])) 
	define("WIZARD_DEFAULT_SITE_ID", $_REQUEST["wizardSiteID"]); 

$arWizardDescription = Array(
	"NAME" => "База автомобилей и шин", 
	"DESCRIPTION" => "Обновление базы данных автомобилей и шин", 
	"VERSION" => "1.0.0",
	"START_TYPE" => "WINDOW",
	"WIZARD_TYPE" => "INSTALL",
	"IMAGE" => "images/wizard.png",
	"ICON" => "images/wizard.png",
	"PARENT" => "wizard_sol",
	"TEMPLATES" => Array(
		Array("SCRIPT" => "wizard_sol")
	),
	"STEPS" =>  Array(
		"UploadFile",
		"MigrationConfig",
		"COMPLETE",
		//"FinishStep"
		)
		
);
?>
