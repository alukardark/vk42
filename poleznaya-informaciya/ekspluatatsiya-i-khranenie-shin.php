<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Эксплуатация и хранение шин");
$APPLICATION->SetTitle("Эксплуатация и хранение шин");
$APPLICATION->AddChainItem($APPLICATION->GetTitle());
echo '<section class="section ve">';
?>Эксплуатация и хранение шин<?
echo '</section>';
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>