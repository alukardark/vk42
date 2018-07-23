<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetPageProperty("title", "Статья");
$APPLICATION->SetPageProperty("description", "Купить импортные и отечественные шины и диски. Онлайн-запись на услуги обслуживания автомобиля в сети сервис-центров «ВК»");
$APPLICATION->SetPageProperty("keywords", "шины, диски, купить шины, летние шины, зимние шины, каталог шин, покупка шин, каталог шин, шины каталог цены, шина цена, диски цена, купить диски, подобрать диски, записаться на услуги, ВК сервис, онлайн-запись, автосервис, сервис-центры, шины и диски, каталог дисков, отечественные шины, отечественные диски, импортные диски, импортные шины, диски под заказ, заказать диски шины под заказ, заказать шины");
$APPLICATION->SetTitle("Статья");

if (!strstr($APPLICATION->GetCurPage(true), "index.php"))
{
    $APPLICATION->AddChainItem($APPLICATION->GetTitle());
}
echo '<section class="section ve">';
?>
Текст статьи...
<?
echo '</section>';
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>