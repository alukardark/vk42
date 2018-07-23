<?
//require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

global $USER, $APPLICATION;

$context = Bitrix\Main\Application::getInstance()->getContext();
$request = $context->getRequest();

$code  = $request['code'];
$state = $request['state'];

$netsCodes = \COAuthExt::getNetsCodes();

if ($USER->IsAuthorized()):
    $redirect = PATH_PERSONAL;
    ?>
    <script>
        window.opener.location.href = '<?= $redirect ?>';
        window.close();
    </script>
    <?
endif;

if (empty($code) || empty($state)):
    $redirect = PATH_AUTH;
    ?>
    <script>
        window.opener.location.href = '<?= $redirect ?>';
        window.close();
    </script>
    <?
endif;


foreach ($netsCodes as $NET)
{
    if ($state == \COAuthExt::getState($NET))
    {

        //get access token
        $arToken = \COAuthExt::getToken($code, $NET);

        //get user info
        $arNetUser = \COAuthExt::getUser($arToken, $NET);


        $UF_CODE = "UF_" . $NET . "_USER_ID";

        //check if user exist
        $USER_ID = (int) \CUserExt::getByField($UF_CODE, $arNetUser['user_id']);

        if (empty($USER_ID))
        {
            $APPLICATION->set_cookie($UF_CODE, serialize($arNetUser), time() + 3600);
            $redirect = PATH_REGISTER . "?FROM=" . $NET;
        }
        else
        {
            \CUserExt::authByUserId($USER_ID);
            $redirect = PATH_PERSONAL . "?FROM=" . $NET;
        }

        break;
    }
}
?>
<script>
    window.opener.location.href = '<?= $redirect ?>';
    window.close();
</script>
    <?