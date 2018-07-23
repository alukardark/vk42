<?

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * полностью скопировано из шаблона tires
 */
$arIds              = array();
$arSectionsIds      = array();
$arSectionsIdsTilda = array();

foreach ($arResult['ITEMS'] as &$arItem)
{
    $arIds[]              = $arItem["ID"];
    $arSectionsIds[]      = $arItem['IBLOCK_SECTION_ID'];
    $arSectionsIdsTilda[] = $arItem['~IBLOCK_SECTION_ID'];

    $arItem['DETAIL_RESIZED'] = \CPic::getDetailSrc($arItem, 200, 200, BX_RESIZE_IMAGE_PROPORTIONAL_ALT);
}

$obCache   = \Bitrix\Main\Data\Cache::createInstance();
$lifeTime  = strtotime("1day", 0);
$cachePath = "/ccache_catalog/arBrandImages/";
$cacheID   = "arBrandImages" . serialize($arSectionsIdsTilda);

if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
{
    $vars = $obCache->GetVars();
    if (isset($vars["arBrandImages"]))
    {
        $arBrandImages = $vars["arBrandImages"];
        $lifeTime      = 0;
    }
}

if ($lifeTime > 0)
{
    $arBrandImages = array();

    foreach ($arSectionsIdsTilda as $iSectionsIdTilda)
    {
        if (empty($arBrandImages[$iSectionsIdTilda]))
        {
            $obEelement = \CIBlockSection::GetByID($iSectionsIdTilda);
            if ($arFetch    = $obEelement->Fetch())
            {
                $arBrandImages[$iSectionsIdTilda] = \CPic::getResized($arFetch['PICTURE'], 200, 30, BX_RESIZE_IMAGE_PROPORTIONAL_ALT, false);
            }
        }
    }

    //кешируем
    $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
    $obCache->EndDataCache(array(
        "arBrandImages" => $arBrandImages,
    ));
}

$arProductsAmountInStores = \CCatalogExt::getProductsAmountInStores($arIds);



foreach ($arResult['ITEMS'] as $key => &$arItem)
{
    $arItem["REAL_AMOUNT"]   = $arProductsAmountInStores[$arItem["ID"]];
    $arItem["BRAND_RESIZED"] = $arBrandImages[$arItem["~IBLOCK_SECTION_ID"]];

    if (empty($arItem["REAL_AMOUNT"]))
    {
        unset($arResult['ITEMS'][$key]);
        continue;
    }
}

$obNavResult                = $arResult['NAV_RESULT'];
$arResult["NavNum"]         = $obNavResult->NavNum;
$arResult["NavPageCount"]   = $obNavResult->NavPageCount;
$arResult["NavPageNomer"]   = $obNavResult->NavPageNomer;
$arResult["NavPageSize"]    = $obNavResult->NavPageSize;
$arResult["NavRecordCount"] = $obNavResult->NavRecordCount;
$arResult["MORE_COUNT"]     = $arResult["NavRecordCount"] - $arResult["NavPageNomer"] * $arResult["NavPageSize"];
