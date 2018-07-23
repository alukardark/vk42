<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arParams = $arParams['arParams'];

$arSettings = \CCatalogExt::getSettings();
$arOnPages  = $arSettings['ONPAGE_VARIANTS'];
$arSorts    = $arSettings['SORT_VARIANTS'];
?>
<div class="catalog-section-settings row">
    <div class="col-6 col-md-24 hidden-lg-up settings-wrapper-btn">
        <button class="noselect" onclick="Filter.toggleSmartFilters(this)" id="btn-filters">Фильтры</button>
    </div>

    <div class="col-24 col-lg-18 col-md-24 settings-wrapper-fields">
        <div class="catalog-section-settings-block catalog-section-settings-onpage js-settings-onpage">
            <div class="catalog-section-settings-title">Показать:</div>
            <div
                class="catalog-section-settings-current noselect"
                onclick="Catalog.dropdown(this)"
                data-event-alias="onpage"
                title="Количество элементов на странице"
                >
                <span><?= $arParams['PAGE_ELEMENT_COUNT'] ?></span>
                <i class="ion-android-arrow-dropdown"></i>
            </div>

            <ul class="catalog-section-settings-list noliststyle">
                <? foreach ($arOnPages as $iOnPage): ?>
                    <li
                        title="<?= $iOnPage ?>"
                        onclick="Catalog.onpage(this, {'onpage': <?= $iOnPage ?>}, '<?= htmlspecialcharsbx($_REQUEST['q']) ?>')"
                        ><?= $iOnPage ?></li>
                    <? endforeach; ?>
            </ul>
        </div>
        <div class="catalog-section-settings-block catalog-section-settings-sort">
            <div class="catalog-section-settings-title">Сортировка:</div>
            <div
                class="catalog-section-settings-current noselect"
                onclick="Catalog.dropdown(this)"
                data-event-alias="sort"
                title="Порядок сортировки"
                >
                <span><?= $arSorts[$arParams['ELEMENT_SORT_FIELD'] . ";" . $arParams['ELEMENT_SORT_ORDER']] ?></span>
                <i class="ion-android-arrow-dropdown"></i>
            </div>

            <ul class="catalog-section-settings-list noliststyle">
                <? foreach ($arSorts as $sSortCode => $sSortTitle): ?>
                    <li
                        title="<?= $sSortTitle ?>"
                        onclick="Catalog.sort(this, {'sort': '<?= $sSortCode ?>'}, '<?= htmlspecialcharsbx($_REQUEST['q']) ?>')"
                        ><?= $sSortTitle ?></li>
                    <? endforeach; ?>
            </ul>
        </div>
    </div>
</div>