<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "title browser");
$APPLICATION->SetPageProperty("description", "Обратная связь");
$APPLICATION->SetTitle("Обратная связь");
$APPLICATION->AddChainItem($APPLICATION->GetTitle());
echo '<section class="section ve">';
?>Текст статьи...
!!!<?
echo '</section>';
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>