<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

if (empty($_POST['action']))
{
    json_result(false, "invalid action");
}

$sAction = $_POST['action'];

json_result(false, "unknown error");
