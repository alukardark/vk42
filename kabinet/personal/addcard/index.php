<?

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

global $USER;
$USER_ID = $USER->GetID();

if (empty($USER_ID))
{
    LocalRedirect(PATH_AUTH);
    die;
}


if (1 || $USER->IsAdmin())
{
    include_once 'inc.php';
    include_once 'index_new.php';
}
else
{
    include_once 'index_old.php';
}
?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>