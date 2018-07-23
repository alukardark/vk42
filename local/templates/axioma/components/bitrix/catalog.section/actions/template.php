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

?>
<? if (!empty($arResult['ITEMS'])): ?>
    <div id="catalog-list" class="catalog-list row slider-catalog-list loading">
        <figure class="slider-catalog-list-spinner"><i></i></figure>
        <div id="catalog-list-slider">
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
<? else: ?>
    <div class="catalog-list-empty">Ничего не найдено</div>
<? endif; ?>
