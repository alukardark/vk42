<?

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

global $APPLICATION;

\Axi::GF("catalog/init");
$arCatalogParams = \CCatalogExt::getParams(TIRES_IB);
?>

<?

$APPLICATION->IncludeComponent(
        "bitrix:catalog", "tires", $arCatalogParams, false, array("HIDE_ICONS" => "Y")
);
?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>