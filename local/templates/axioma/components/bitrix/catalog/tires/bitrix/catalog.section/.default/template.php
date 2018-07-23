<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);

global $USER;

$IBLOCK_ID    = $arParams["IBLOCK_ID"];
$SECTION_CODE = $arParams["SECTION_CODE"];

$arUserInfo = array(
    'ID'    => $USER->GetID(),
    'NAME'  => $USER->GetFullName(),
    'PHONE' => \CUserExt::getPhone(),
    'CITY'  => \Axi::getCityName(),
);

if (!empty($_REQUEST['q']))
{
    $searchIn = empty($arParams['SEARCH_IN']) ? "Найдено товаров" : $arParams['SEARCH_IN'];
    $searchIn .= " по запросу <strong>" . htmlspecialcharsbx($_REQUEST['q']) . "</strong>:&nbsp;";
    $searchIn .= $arResult["NavRecordCount"] == $arParams["PAGE_RESULT_COUNT"] ? "более " : "";
    $searchIn .= $arResult["NavRecordCount"] . " товаров";
}
else
{
    $searchIn = "Найдено товаров: " . $arResult["NavRecordCount"];
}
?>
<? if (!empty($arResult['ITEMS'])): ?>
    <div id="catalog-list" class="catalog-list row">
        <div class="col-24 catalog-list-count"><?= $searchIn ?></div>

        <div class="catalog-list-filter-labels">
            <?= \CCatalogExt::getFilterLabels($IBLOCK_ID, $SECTION_CODE); ?>
        </div>

        <? foreach ($arResult['ITEMS'] as $arItem): ?>
            <?
            //printrau($arItem);
            $APPLICATION->IncludeFile("/include/catalog/item.php", array(
                'arItem'       => $arItem,
                'arUserInfo'   => $arUserInfo,
                'arParams'     => $arParams,
                'USE_DISCOUNT' => USE_DISCOUNT,
            ));
            ?>
        <? endforeach; ?>
    </div>

    <div class="catalog-list-more <?= $arResult['MORE_COUNT'] > 0 ? "" : "hidden" ?>">
        <button
            title="Показать еще"
            onclick="Catalog.showMore(this);"
            data-navnum="<?= $arResult["NavNum"] ?>"
            data-pagenomer="<?= $arResult["NavPageNomer"] ?>"
            >
            <mark class='catalog-list-more-spinner'><i></i><i></i><i></i></mark>Показать еще
        </button>
    </div>

    <? if (!empty($arResult['NAV_STRING'])): ?>
        <div class="catalog-list-pagination float-right">
            <?= $arResult['NAV_STRING'] ?>
        </div>
    <? endif; ?>

    <? if (!empty($arResult['UF_SEO_TEXT'])): ?>
        <div class="catalog-description-text row col-24">
            <?= htmlspecialchars_decode($arResult['UF_SEO_TEXT']) ?>
        </div>
    <? endif; ?>
<? else: ?>
    <? if (empty($arParams['SEARCH_IN'])): ?>
        <div class="catalog-list-filter-labels">
            <?= \CCatalogExt::getFilterLabels($IBLOCK_ID, $SECTION_CODE); ?>
        </div>

        <div class="catalog-list-empty">По заданным Вами параметрам ничего не найдено.</div>
        <div>
            <button class="catalog-list-reset" onclick="Filter.clear(this)">
                <span>Сбросить фильтр</span>
            </button>
        </div>
    <? endif; ?>
<? endif; ?>
