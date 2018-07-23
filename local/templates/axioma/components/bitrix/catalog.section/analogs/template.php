<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);

global $USER;

$arUserInfo = array(
    'ID'    => $USER->GetID(),
    'NAME'  => $USER->GetFullName(),
    'PHONE' => \CUserExt::getPhone(),
    'CITY'  => \Axi::getCityName(),
);

//printra($arResult);
//printra($arParams);
?>
<? if (!empty($arResult['ITEMS'])): ?>
    <div class="analogs-list-wrap" data-record-count="<?= $arResult["NavRecordCount"] ?>">
        <div class="analogs-list-title">
            <div class="analogs-list-title-text">К вашему автомобилю также подойдут</div>
            
            <? if (!empty($arParams["ANALOGS_URL"])): ?>
                <div class="analogs-list-title-link">
                    <span>/</span>
                    <a href="<?= $arParams["ANALOGS_URL"] ?>" title="Смотреть все">Смотреть все (<?= $arResult["NavRecordCount"] ?>)</a>
                </div>
            <? endif; ?>
        </div>

        <div id="analogs-list" class="catalog-list row slider-catalog-list loading">
            <figure class="slider-catalog-list-spinner"><i></i></figure>
            <div id="analogs-list-slider">
                <? foreach ($arResult['ITEMS'] as $arItem): ?>
                    <?
                    $APPLICATION->IncludeFile("/include/catalog/item.php", array(
                        'arItem'     => $arItem,
                        'arUserInfo' => $arUserInfo,
                    ));
                    ?>
                <? endforeach; ?>
            </div>
        </div>
    </div>
<? else: ?>
    <div class="catalog-list-empty">Ничего не найдено</div>
<? endif; ?>
