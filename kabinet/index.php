<?

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;
$USER->IsAuthorized() ? LocalRedirect(PATH_PERSONAL) : LocalRedirect(PATH_AUTH);
exit;
