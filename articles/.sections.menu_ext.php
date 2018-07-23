<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
global $APPLICATION;

$aMenuLinksExt = $APPLICATION->IncludeComponent("bitrix:menu.sections", "articles", Array(
    "IS_SEF"           => "Y",
    "SEF_BASE_URL"     => "/articles/",
    "SECTION_PAGE_URL" => "#SECTION_CODE#/",
    "DETAIL_PAGE_URL"  => "#SECTION_CODE#/#ELEMENT_CODE#/",
    "IBLOCK_TYPE"      => "news",
    "IBLOCK_ID"        => ARTICLES_IB,
    "DEPTH_LEVEL"      => "2",
    "CACHE_TYPE"       => "A",
    "CACHE_TIME"       => "360000"
        )
);

$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);
?>