<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();



foreach ($arResult["BASKET"] as $k => &$prod)
{
    if (floatval($prod['DISCOUNT_PRICE'])) $hasDiscount = true;
    // move iblock props (if any) to basket props to have some kind of consistency
    if (isset($prod['IBLOCK_ID']))
    {
        $iblock       = $prod['IBLOCK_ID'];
        if (isset($prod['PARENT'])) $parentIblock = $prod['PARENT']['IBLOCK_ID'];
        foreach ($arParams['CUSTOM_SELECT_PROPS'] as $prop)
        {
            $key = $prop . '_VALUE';
            if (isset($prod[$key]))
            {
                // in the different iblocks we can have different properties under the same code
                if (isset($arResult['PROPERTY_DESCRIPTION'][$iblock][$prop]))
                        $realProp        = $arResult['PROPERTY_DESCRIPTION'][$iblock][$prop];
                elseif (isset($arResult['PROPERTY_DESCRIPTION'][$parentIblock][$prop]))
                        $realProp        = $arResult['PROPERTY_DESCRIPTION'][$parentIblock][$prop];
                if (!empty($realProp))
                        $prod['PROPS'][] = array(
                        'NAME'  => $realProp['NAME'],
                        'VALUE' => htmlspecialcharsEx($prod[$key])
                    );
            }
        }
    }
    // if we have props, show "properties" column
    if (!empty($prod['PROPS'])) $hasProps                          = true;
    $productSum                        += $prod['PRICE'] * $prod['QUANTITY'];
    $basketRefs[$prod['PRODUCT_ID']][] = & $arResult["BASKET"][$k];

    $prod['PICTURE_RESIZED'] = \CPic::getResized($prod['DETAIL_PICTURE'], 150, 100, BX_RESIZE_IMAGE_PROPORTIONAL_ALT);
}


$arResult['HAS_DISCOUNT'] = $hasDiscount;
$arResult['HAS_PROPS']    = $hasProps;

$arResult['PRODUCT_SUM_FORMATTED'] = SaleFormatCurrency($productSum, $arResult['CURRENCY']);
?>