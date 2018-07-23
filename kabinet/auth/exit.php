<?

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;
$USER->Logout();

LocalRedirect(PATH_AUTH);
die;
