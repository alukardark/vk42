<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arItem     = $arParams['arItem'];
$arUserInfo = $arParams['arUserInfo'];
$arParams   = $arParams['arParams'];

$IBLOCK_ID    = $arParams["IBLOCK_ID"];
$SECTION_CODE = $arParams["SECTION_CODE"];

$arItem["SECTION"]["CODE"] = $SECTION_CODE;

$arPrices = $arItem['PRICES'];
$arProps  = $arItem['PROPERTIES'];

//$sName = \CCatalogExt::getName($arItem);
\CCatalogExt::setName($arItem);
$sName = $arItem["NAME"];
$sUrl  = \CCatalogExt::getProductUrl($arItem);

$sPicture       = $arItem['DETAIL_RESIZED'];
$sDetailPicture = $arItem['DETAIL_BIG'];
$sBrandPicture  = $arItem['BRAND_RESIZED'];
$iAmount        = $arItem['REAL_AMOUNT'];
$sBrand         = $arProps['MARKA']['VALUE'];

if ($iAmount > 4) $sAmount = "В наличии от 4 шт.";
elseif ($iAmount > 0) $sAmount = "В наличии $iAmount шт.";
else $sAmount = "Под заказ";

$easyzoom = !empty($sDetailPicture) && $arItem['DETAIL_PICTURE']['WIDTH'] > 300;


$isBackOs = false;

if ($IBLOCK_ID == DISCS_IB && !empty($_SESSION['FILTER_PRESETS'][$IBLOCK_ID][$SECTION_CODE]))
{
    $PRESETS = $_SESSION['FILTER_PRESETS'][$IBLOCK_ID][$SECTION_CODE];

    $arPresets = array();
    foreach ($PRESETS as $key => $arPreset)
    {
        $arPresetParts = explode("|", $arPreset["value"]);
        $TYPE          = $arPresetParts[0]; //2 - front; 4 - back

        if ($TYPE != 4) continue;

        if (!if_in_array($arPreset, $arPresets))
        {
            $arPresets[] = $arPreset;
        }
    }

    //проходим по массиву пресетов для задней оси
    foreach ($arPresets as $i => $arPreset)
    {
        $isBackOs     = true;
        $arPresetSize = \CFilterExt::parseDiskPreset($arPreset["value"], true);

        //echo "start preset $i<br/>";
        //проверяем все параметры пресета - они должны совпасть со свойствами текущего товара $arProps
        foreach ($arPresetSize as $prop => $value)
        {
            //echo "see preset prop $prop<br/>";
            $propVal = $arProps[$prop]["VALUE"];
            //printra($propVal);

            if ($prop == KREPLENIEDISKA)
            {
                $holes = $value[0];
                $pcd   = $value[1];

                $pattern = "/$holes\*($pcd|.*\/$pcd)/";
                //printra($pattern);
                if (!preg_match($pattern, $propVal))
                {
                    $isBackOs = false;
                    break;
                }
            }
            elseif ($prop == VYLET)
            {
                if ($propVal < $value[0] || $propVal > $value[1])
                {
                    $isBackOs = false;
                    break;
                }
            }
            elseif (is_array($value))
            {
                if (!in_array($propVal, $value))
                {
                    $isBackOs = false;
                    break;
                }
            }
            else
            {
                if ($propVal != $value)
                {
                    $isBackOs = false;
                    break;
                }
            }
        }

        if ($isBackOs)
        {
            break;
        }
    }
}
?>
<div class="catalog-item col-6 col-xxl-8 col-xl-12 col-lg-8 col-md-12 col-sm-24">
    <div class="catalog-item__container">
        <div
            onclick="javascript:window.location.href = '<?= $sUrl ?>'"
            class="catalog-item-picture easyzoom easyzoom--overlay <?= $easyzoom ? "js-easyzoom" : "" ?> ">
            <a
                href="<?= $sUrl ?>"
                class="catalog-item-picture-link"
                title="<?= $sName ?>"
                data-detail="<?= $sDetailPicture ?>"
                style="background-image: url(<?= $sPicture ?>)"
                >
            </a>

            <? if ($isBackOs): ?>
                <div class="catalog-item-backos">Для задней оси выбранного авто</div>
            <? endif; ?>
        </div>



        <div class="catalog-item__container__desc">
            <a
                href="<?= $sUrl ?>"
                class="catalog-item-title"
                title="<?= $sName ?>"
                ><?= $sName ?></a>

            <?
            if (!empty($arPrices)):
                $HIDDEN_DISCOUNTS_IBLOCK_ID = $arParams['HIDDEN_DISCOUNTS_IBLOCK_ID'];
                $IBLOCK_ID                  = $arItem['IBLOCK_ID'];

                //$bDiscount = $arPrices['VALUE'] != $arPrices['DISCOUNT_VALUE'];
                $bDiscount = true;

                $showOldPrice = true;
                if (in_array($IBLOCK_ID, $HIDDEN_DISCOUNTS_IBLOCK_ID))
                {
                    $showOldPrice = false;
                }
                ?>

                <div class="catalog-item-prices">
                    <? if (USE_DISCOUNT): ?>
                        <div class="catalog-item-prices-row row <?= $showOldPrice ?: "hidden" ?>">
                            <span class="catalog-item-prices-description float-left float-sm-none">В магазине:</span>
                            <span class="catalog-item-prices-discount float-right float-sm-none"><?= printPrice($arPrices[RETAIL_PRICE_NAME]['VALUE']) ?></span>
                        </div>

                        <div class="catalog-item-prices-row row">
                            <span class="catalog-item-prices-description float-left float-sm-none">На сайте:</span>
                            <span class="catalog-item-prices-priceinline float-right float-sm-none"><?= printPrice($arPrices[CATALOG_PRICE_NAME]['VALUE']) ?></span>
                        </div>
                    <? else: ?>
                        <span class="catalog-item-prices-price"><?= printPrice($arPrices[CATALOG_PRICE_NAME]['VALUE']) ?></span>
                    <? endif; ?>
                </div>
            <? endif; ?>

            <div class="catalog-item-quantity">
                <a
                    href="<?= $sUrl ?>#clarify"
                    class="catalog-item-quantity-link"
                    title="Уточнить остатки <?= $sName ?>"
                    >
                        <?= $sAmount ?>
                </a>
            </div>

            <div class="catalog-item-brand">
                <? if (!empty($sBrandPicture)): ?>
                    <img src="<?= $sBrandPicture ?>" alt="<?= $sBrand ?>" title="<?= $sBrand ?>" />
                <? endif; ?>
            </div>
        </div>

        <div class="catalog-item-buy">
            <button
                class="catalog-item-buy-regular"
                data-product-id="<?= $arItem['ID'] ?>"
                data-basket-action="plus"
                title="В корзину"
                onclick="Basket.doAction(this);"
                >
                <mark><i></i><i></i><i></i></mark>В корзину
            </button>

            <button
                class="catalog-item-buy-oneclick"
                data-product-name="<?= $arItem['NAME'] ?>"
                data-product-xml-id="<?= $arItem['EXTERNAL_ID'] ?>"
                data-quantity="1"
                data-user-id="<?= $arUserInfo['ID'] ?>"
                data-user-city="<?= $arUserInfo['CITY'] ?>"
                data-user-name="<?= $arUserInfo['NAME'] ?>"
                data-user-phone="<?= $arUserInfo['PHONE'] ?>"
                title="Купить в 1 клик"
                onclick="Form.toggleBuyOneClickForm(this);"
                >Купить в 1 клик</button>
        </div>
    </div>

    <? $APPLICATION->IncludeFile("/include/catalog/actions_shields.php", array("arProps" => $arProps)); ?>

    <div class="catalog-item-season">
        <? if ($arProps['SEZON']['VALUE'] == SUMMER): ?>
            <i class="catalog-item-season-summer"></i>
        <? endif; ?>
        <? if ($arProps['SEZON']['VALUE'] == WINTER): ?>
            <i class="catalog-item-season-winter"></i>
        <? endif; ?>
    </div>
</div>