<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

// Формируем массив объектов складов для показа на карте
$arStoresJS = json_encode(\CCatalogExt::getStoresJS());
?>

<script async="async">
    var STORES = <?= $arStoresJS ?>;
</script>
