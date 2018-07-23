<?
include_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/urlrewrite.php');

\CHTTP::SetStatus("404 Not Found");
@define("ERROR_404", "Y");

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$assets = \Bitrix\Main\Page\Asset::getInstance();
$assets->addCss(SITE_TEMPLATE_PATH . "/styles/kabinet.css");

$APPLICATION->SetTitle("Страница не найдена");
?>

<div class="page404">
    <div class="page404-content">
        <figure class="page404-content-figure"></figure>
        <span class="page404-content-title">Ошибка</span>
        <span class="page404-content-note">Такой страницы не существует</span>
        <a class="page404-content-button" href="/" title="Вернуться на главную">
            <span>На главную</span><i class="ion-ios-arrow-forward"></i>
        </a>
    </div>
</div>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>