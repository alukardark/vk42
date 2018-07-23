<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Полезная инфа");
$APPLICATION->SetPageProperty("description", "");
$APPLICATION->SetTitle("Полезная инфо");
if (!strstr($APPLICATION->GetCurPage(true), "index.php"))
{
    $APPLICATION->AddChainItem($APPLICATION->GetTitle());
}
echo '<section class="section ve">';
?><br>

<a href="/poleznaya-informaciya/ekspluatatsiya-i-khranenie-shin/">Эксплуатация и хранение шин</a>
    <?
echo '</section>';
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>