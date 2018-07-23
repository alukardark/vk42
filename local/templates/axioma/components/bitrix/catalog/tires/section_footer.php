<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>
<?
if (isPost("get_section_list"))
{
    die();
}
?>
</div> <!--end catalog-list-wrap -->
</div> <!--end catalog-list-wrap0 -->
</div> <!--end catalog-list-wrap00 -->
</div> <!--end catalog-section -->


<?
//printra($_SESSION['FILTER_PRESETS']);

$SECTION_FILTER_PRESETS = $_SESSION['FILTER_PRESETS'][$arParams["IBLOCK_ID"]][$arResult["VARIABLES"]["SECTION_CODE"]];

$SECTION_FILTER         = $_SESSION['FILTER'][$arParams["IBLOCK_ID"]][$arResult["VARIABLES"]["SECTION_CODE"]];
$SECTION_ACTIVE_PRESETS = $SECTION_FILTER["PRESET"];

$arAnalogSizes      = array();
$arAnalogSizesArray = array();
//$ANALOGS_FOR        = $SECTION_FILTER['vendor'] . ' ' . $SECTION_FILTER['model'] . ' ' . $SECTION_FILTER['year'] . 'г. ' . $SECTION_FILTER['modification'];
//$analogsUrl         = "ANALOGS_FOR=$ANALOGS_FOR&ANALOGS_PRESETS=";

$analogsUrl = false;

$arFilterUrl = json_decode(urldecode(\CCatalogExt::getFilterUrl($IBLOCK_ID, $SECTION_CODE)), true);

if (0 && !empty($SECTION_ACTIVE_PRESETS))
{
    $arAnalogSizes["LOGIC"] = "OR";

    foreach ($SECTION_ACTIVE_PRESETS as $SECTION_ACTIVE_PRESET)
    {
        //$analogsUrl           .= $SECTION_ACTIVE_PRESET . ";";
        $arAnalogSizes[]      = \CFilterExt::parseDiskPreset($SECTION_ACTIVE_PRESET, "analogs");
        $arAnalogSizesArray[] = \CFilterExt::parseDiskPreset($SECTION_ACTIVE_PRESET, "analogs_array");
    }

    if (count($SECTION_ACTIVE_PRESETS) == 1)
    {
        $analogsUrl = "?";

        foreach ($arAnalogSizesArray[0] as $paramKey => $paramValue)
        {
            if ($paramKey == KREPLENIEDISKA)
            {
                $property_enums = \CIBlockPropertyEnum::GetList(Array("DEF" => "DESC", "SORT" => "ASC"), Array(
                            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                            "CODE"      => $paramKey,
                            "VALUE"     => $paramValue,
                ));
                while ($enum_fields    = $property_enums->GetNext())
                {
                    $paramValueData = $enum_fields["VALUE"];
                    $analogsUrl     .= "FILTER[$paramKey][]=$paramValueData&";
                }
            }
            elseif ($paramKey == SHIRINADISKA)
            {
                $analogsUrl .= "FILTER[$paramKey][FROM]=$paramValue&";
                $analogsUrl .= "FILTER[$paramKey][TO]=$paramValue&";
            }
            elseif ($paramKey == DIA)
            {
                $enums          = array();
                $property_enums = \CIBlockPropertyEnum::GetList(Array("DEF" => "DESC", "SORT" => "ASC"), Array(
                            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                            "CODE"      => $paramKey,
                            "VALUE"     => $paramValue,
                ));
                while ($enum_fields    = $property_enums->GetNext())
                {
                    $enums[] = $enum_fields["VALUE"];
                }

                foreach ($paramValue as $paramValueKey => $paramValueData)
                {
                    if (in_array($paramValueData, $enums)) $analogsUrl .= "FILTER[$paramKey][]=$paramValueData&";
                }
            }
            elseif (!is_array($paramValue))
            {
                $analogsUrl .= "FILTER[$paramKey]=$paramValue&";
            }
            else
            {
                foreach ($paramValue as $paramValueKey => $paramValueData)
                {

                    if ($paramValueKey == "FROM" || $paramValueKey == "TO")
                    {
                        $analogsUrl .= "FILTER[$paramKey][$paramValueKey]=$paramValueData&";
                    }
                    else
                    {
                        $analogsUrl .= "FILTER[$paramKey][]=$paramValueData&";
                    }
                }
            }
        }

        $analogsUrl = rtrim($analogsUrl, "&");
    }
}
elseif (empty($arFilterUrl["FILTER"]["TUNING"]) && !empty($SECTION_FILTER_PRESETS))
{
    $arAnalogSizes["LOGIC"] = "OR";

    foreach ($SECTION_FILTER_PRESETS as $SECTION_FILTER_PRESET)
    {
        $arAnalogSizes[] = \CFilterExt::parseDiskPreset($SECTION_FILTER_PRESET["value"], "analogs");
        //$arAnalogSizesArray[] = \CFilterExt::parseDiskPreset($SECTION_FILTER_PRESET["value"], "analogs_array");
    }

    if (isset($arFilterUrl["FILTER"]["PRESET"])) unset($arFilterUrl["FILTER"]["PRESET"]);
    if (isset($arFilterUrl["FILTER"]["TUNING"])) unset($arFilterUrl["FILTER"]["TUNING"]);

    $arFilterUrl["FILTER"]["TUNING"] = "Y";

    $analogsUrl = "?" . urldecode(http_build_query($arFilterUrl));
}
?>

<? if ($arParams["IBLOCK_ID"] == DISCS_IB && !empty($arAnalogSizes)): ?>
    <?
    global $arAnalogFilter;
    if (HIDE_NULL) $arAnalogFilter = array($arAnalogSizes, array(">CATALOG_QUANTITY" => 0));


    //printrau($arAnalogFilter);

    $arCatalogParams                        = \CCatalogExt::getParams(DISCS_IB);
    $arCatalogParams["SHOW_ALL_WO_SECTION"] = "Y";
    $arCatalogParams["PAGE_ELEMENT_COUNT"]  = 24;
    $arCatalogParams["ELEMENT_SORT_FIELD"]  = 'SHOWS';
    $arCatalogParams["ELEMENT_SORT_ORDER"]  = 'desc';
    $arCatalogParams["FILTER_NAME"]         = 'arAnalogFilter';
    $arCatalogParams["ANALOGS_URL"]         = $analogsUrl;
    $arCatalogParams["SECTION_CODE"]        = $arResult["VARIABLES"]["SECTION_CODE"];
    ?>

    <?
    $APPLICATION->IncludeComponent(
            "bitrix:catalog.section", "analogs", $arCatalogParams, false, array("HIDE_ICONS" => "Y")
    );
    ?>
<? endif; ?>


</div> <!--end catalog-list-wrap000 -->
</div> <!--end catalog-list-wrap0000 -->



<?
if (isPost("get_section"))
{
    die();
}
?>
</div> <!-- end catalog-wrap -->