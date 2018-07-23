<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
if (!defined("WIZARD_DEFAULT_SITE_ID") && !empty($_REQUEST["wizardSiteID"]))
        define("WIZARD_DEFAULT_SITE_ID", $_REQUEST["wizardSiteID"]);


/**
 * v2.0.01 - trim infoclock values
 */
$arWizardDescription = Array(
    "NAME"        => "База автомобилей и шин v.2",
    "DESCRIPTION" => "Обновление базы данных автомобилей и шин v.2",
    "VERSION"     => "2.1.1",
    "START_TYPE"  => "WINDOW",
    "WIZARD_TYPE" => "INSTALL",
    "IMAGE"       => "images/wizard.png",
    "ICON"        => "images/wizard.png",
    "PARENT"      => "wizard_sol",
    "TEMPLATES"   => Array(
        Array("SCRIPT" => "wizard_sol")
    ),
    "STEPS"       => Array(
        "Info",
        "UploadFile",
        "MigrationConfig",
        "MigrationStart",
        "COMPLETE"
    )
);
