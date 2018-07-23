<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$curAlias     = \Axi::getAlias();
$IBLOCK_ID    = DISCS_IB;
$SECTION_CODE = $arParams['SECTION_CODE'];
$arParams     = $arParams['arParams'];

if (empty($SECTION_CODE))
{
    $SECTION_CODE = DISCS_LIGHT;
}

$arTXProps       = \CCatalogExt::getTXProps();
$arTXLists       = \CCatalogExt::getTXLists($IBLOCK_ID, $SECTION_CODE);
$arSectionFilter = \CFilterExt::getFilterSession($IBLOCK_ID, $SECTION_CODE);
$activeFilter    = \CCatalogExt::getActiveFilterTab($IBLOCK_ID, $SECTION_CODE);


if (isPost("get_filter_discs_params"))
{
    $active = "";
}
else
{
    //$active = $activeFilter == "discs_car" ? "active" : "";
    $active = "active";
}

//для определения выбрана ли модификация и следует ли блокировать кнокпу на главной странице
$lastKey     = get_last_array_key($arTXProps); //получаем последний ключ массива
$btnDisabled = true;
?>
<div data-filter-type="discs_car" class="active filter-wrap <?= $active ?> <?= FILTER_BY_AUTO_ENABLE ?: "hidden-" ?>">
    <div class="filter-title">Поиск по автомобилю</div>
    <div class="filter filter-with-reset">
        <div class='filter-spinner js-filter-spinner'><i></i><i></i><i></i></div>

        <div class="filter-container js-filter-container-cars" data-ib="<?= $IBLOCK_ID ?>" data-sc="<?= $SECTION_CODE ?>">
            <?
            foreach ($arTXProps as $property => $title):
                $value  = $arSectionFilter[$property];
                $arList = $arTXLists[$property];

                $dataValue = !empty($value) && !empty($arList) ?
                        (is_array($value) ? "Несколько" : $value) :
                        $title;
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
                        onclick="Filter.toggleDropdown(this)"
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

                    <? if (!$btnDisabled && $curAlias != "index-page"): ?>
                        <button
                            title="Сбросить фильтр по автомобилю"
                            class="filter-reset-button"
                            data-button-filter="discs_car"
                            onclick="Filter.clearCar(this)"
                            >
                            <i class="ion-close-round"></i>
                            <span>сбросить</span>
                        </button>
                    <? endif; ?>
                </div>
            <? endforeach; ?>
        </div>
    </div>

    <? if ($curAlias == "index-page"): ?>
        <button
            title="Подобрать диски по марке автомобиля"
            class="filter-wrap-button <?= $btnDisabled ? "disabled" : "" ?>"
            data-button-filter="discs_car"
            onclick="Filter.redirectToFilter(this)"
            >
            <span>Подобрать диски</span>
            <i class="ion-chevron-right"></i>
        </button>
    <? endif; ?>
</div>