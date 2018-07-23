<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
global $axi;
$obExCatalog = \CCatalogExt::get();

$curAlias  = $axi->getAlias();
$arTXProps = $obExCatalog->getTXProps();
$arTXLists = $obExCatalog->getTXLists();

$arSectionFilter = $_SESSION['FILTER'][LEGKOVYE];
$activeFilter    = \CCatalogExt::getActivefilter($arSectionFilter);

$active = $activeFilter == "car" ? "active" : "";

//для определения выбрана ли модификация и следует ли блокировать кнокпу на главной странице
$lastKey     = get_last_array_key($arTXProps); //получаем последний ключ массива
$btnDisabled = true;
?>
<div data-filter-type="car" class="filter-wrap <?= $active ?>">
    <div class="filter-title">Поиск по автомобилю</div>
    <div class="filter">
        <div class='filter-spinner js-filter-spinner'><i></i><i></i><i></i></div>

        <div class="filter-container js-filter-container-cars">
            <?
            foreach ($arTXProps as $property => $title):
                $value  = $arSectionFilter[$property];
                $arList = $arTXLists[$property];

                $dataValue = !empty($value) && !empty($arList) ? $value : $title;
                ?>
                <div
                    class="filter-block clearfix <?= !empty($arList) ? "active" : "" ?>"
                    data-property="<?= $property ?>"
                    data-value="<?= $value ?>"
                    data-title="<?= $title ?>"
                    >
                    <div class="filter-block-htitle"><?= $title ?></div>

                    <div
                        class="filter-block-title"
                        onclick="Filter.dropdown_toggle(this)"
                        title="<?= $title ?>"
                        >
                        <span><?= $dataValue ?></span>
                        <i class="<?= $curAlias == "index-page" ? "ion-ios-arrow-down" : "ion-android-arrow-dropdown" ?>"></i>
                    </div>

                    <ul class="noliststyle">
                        <? if (!empty($arList)): ?>
                            <?
                            foreach ($arList as $arItem):
                                if ($property == $lastKey && $arItem['SELECTED']) $btnDisabled = false;
                                ?>
                                <li
                                    class="<?= $arItem['SELECTED'] ? "selected" : "" ?> "
                                    data-value="<?= $arItem ['VALUE'] ?>"
                                    title="<?= $arItem ['VALUE'] ?>"
                                    ><?= $arItem ['VALUE'] ?></li>
                                <? endforeach; ?>
                            <? endif; ?>
                    </ul>
                </div>
            <? endforeach; ?>
        </div>
    </div>

    <? if ($curAlias == "index-page"): ?>
        <button
            title="Подобрать шины по марке автомобиля"
            class="filter-wrap-button <?= $btnDisabled ? "disabled" : "" ?>"
            data-button-filter="car"
            onclick="Filter.redirectToFilter(this)"
            >
            <span>Подобрать шины</span>
            <i class="ion-chevron-right"></i>
        </button>
    <? endif; ?>
</div>