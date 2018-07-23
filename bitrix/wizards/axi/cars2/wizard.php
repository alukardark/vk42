<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?
set_time_limit(0);
ini_set('max_execution_time', 0);
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/install/wizard_sol/wizard.php");


include_once 'steps/Info.php';
include_once 'steps/UploadFile.php';

include_once 'steps/MigrationConfig.php';
include_once 'steps/MigrationStart.php';

include_once 'steps/COMPLETE.php';
include_once 'steps/FinishStep.php';