<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
?>

<div class="actions actions-wrap">
    <?
    if (isPost("get_list"))
    {
        $APPLICATION->RestartBuffer();
    }
    ?>

    <?
    $APPLICATION->IncludeComponent(
            "bitrix:news.list", "", Array(
        "IBLOCK_TYPE"                     => $arParams["IBLOCK_TYPE"],
        "IBLOCK_ID"                       => $arParams["IBLOCK_ID"],
        "NEWS_COUNT"                      => $arParams["NEWS_COUNT"],
        "SORT_BY1"                        => $arParams["SORT_BY1"],
        "SORT_ORDER1"                     => $arParams["SORT_ORDER1"],
        "SORT_BY2"                        => $arParams["SORT_BY2"],
        "SORT_ORDER2"                     => $arParams["SORT_ORDER2"],
        "FIELD_CODE"                      => $arParams["LIST_FIELD_CODE"],
        "PROPERTY_CODE"                   => $arParams["LIST_PROPERTY_CODE"],
        "DETAIL_URL"                      => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["detail"],
        "SECTION_URL"                     => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
        "IBLOCK_URL"                      => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["news"],
        "DISPLAY_PANEL"                   => $arParams["DISPLAY_PANEL"],
        "SET_TITLE"                       => $arParams["SET_TITLE"],
        "SET_LAST_MODIFIED"               => $arParams["SET_LAST_MODIFIED"],
        "MESSAGE_404"                     => $arParams["MESSAGE_404"],
        "SET_STATUS_404"                  => $arParams["SET_STATUS_404"],
        "SHOW_404"                        => $arParams["SHOW_404"],
        "FILE_404"                        => $arParams["FILE_404"],
        "INCLUDE_IBLOCK_INTO_CHAIN"       => $arParams["INCLUDE_IBLOCK_INTO_CHAIN"],
        "CACHE_TYPE"                      => $arParams["CACHE_TYPE"],
        "CACHE_TIME"                      => $arParams["CACHE_TIME"],
        "CACHE_FILTER"                    => $arParams["CACHE_FILTER"],
        "CACHE_GROUPS"                    => $arParams["CACHE_GROUPS"],
        "DISPLAY_TOP_PAGER"               => $arParams["DISPLAY_TOP_PAGER"],
        "DISPLAY_BOTTOM_PAGER"            => $arParams["DISPLAY_BOTTOM_PAGER"],
        "PAGER_TITLE"                     => $arParams["PAGER_TITLE"],
        "PAGER_TEMPLATE"                  => $arParams["PAGER_TEMPLATE"],
        "PAGER_SHOW_ALWAYS"               => $arParams["PAGER_SHOW_ALWAYS"],
        "PAGER_DESC_NUMBERING"            => $arParams["PAGER_DESC_NUMBERING"],
        "PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
        "PAGER_SHOW_ALL"                  => $arParams["PAGER_SHOW_ALL"],
        "PAGER_BASE_LINK_ENABLE"          => $arParams["PAGER_BASE_LINK_ENABLE"],
        "PAGER_BASE_LINK"                 => $arParams["PAGER_BASE_LINK"],
        "PAGER_PARAMS_NAME"               => $arParams["PAGER_PARAMS_NAME"],
        "DISPLAY_DATE"                    => $arParams["DISPLAY_DATE"],
        "DISPLAY_NAME"                    => "Y",
        "DISPLAY_PICTURE"                 => $arParams["DISPLAY_PICTURE"],
        "DISPLAY_PREVIEW_TEXT"            => $arParams["DISPLAY_PREVIEW_TEXT"],
        "PREVIEW_TRUNCATE_LEN"            => $arParams["PREVIEW_TRUNCATE_LEN"],
        "ACTIVE_DATE_FORMAT"              => $arParams["LIST_ACTIVE_DATE_FORMAT"],
        "USE_PERMISSIONS"                 => $arParams["USE_PERMISSIONS"],
        "GROUP_PERMISSIONS"               => $arParams["GROUP_PERMISSIONS"],
        "FILTER_NAME"                     => $arParams["FILTER_NAME"],
        "HIDE_LINK_WHEN_NO_DETAIL"        => $arParams["HIDE_LINK_WHEN_NO_DETAIL"],
        "CHECK_DATES"                     => $arParams["CHECK_DATES"],
            ), $component
    );
    ?>
    <?
    if (isPost("get_list"))
    {
        die();
    }
    ?>

</div>

<div class="bg-gray">
    <?
    $APPLICATION->IncludeComponent(
            "bitrix:news", "news", Array(
        "FROM_ACTIONS"                    => "Y", //флаг указывает, что компонент подключен в акциях
        "IBLOCK_ID"                       => NEWS_IB,
        "NEWS_COUNT"                      => "2",
        "ADD_ELEMENT_CHAIN"               => "N",
        "ADD_SECTIONS_CHAIN"              => "N",
        "AJAX_MODE"                       => "N",
        "AJAX_OPTION_ADDITIONAL"          => "",
        "AJAX_OPTION_HISTORY"             => "N",
        "AJAX_OPTION_JUMP"                => "N",
        "AJAX_OPTION_STYLE"               => "Y",
        "BROWSER_TITLE"                   => "-",
        "CACHE_FILTER"                    => "Y",
        "CACHE_GROUPS"                    => "N",
        "CACHE_TIME"                      => "36000000",
        "CACHE_TYPE"                      => "A",
        "CHECK_DATES"                     => "Y",
        "DETAIL_ACTIVE_DATE_FORMAT"       => "j F Y",
        "DETAIL_DISPLAY_BOTTOM_PAGER"     => "N",
        "DETAIL_DISPLAY_TOP_PAGER"        => "N",
        "DETAIL_FIELD_CODE"               => array(),
        "DETAIL_PAGER_SHOW_ALL"           => "N",
        "DETAIL_PAGER_TEMPLATE"           => "",
        "DETAIL_PAGER_TITLE"              => "Страница",
        "DETAIL_PROPERTY_CODE"            => array("TAGS"),
        "DETAIL_SET_CANONICAL_URL"        => "N",
        "DISPLAY_BOTTOM_PAGER"            => "N",
        "DISPLAY_NAME"                    => "Y",
        "DISPLAY_TOP_PAGER"               => "N",
        "HIDE_LINK_WHEN_NO_DETAIL"        => "N",
        "IBLOCK_TYPE"                     => "news",
        "INCLUDE_IBLOCK_INTO_CHAIN"       => "N",
        "LIST_ACTIVE_DATE_FORMAT"         => "j F Y",
        "LIST_FIELD_CODE"                 => array(),
        "LIST_PROPERTY_CODE"              => array("TAGS"),
        "MESSAGE_404"                     => "Новость не найдена",
        "META_DESCRIPTION"                => "-",
        "META_KEYWORDS"                   => "-",
        "PAGER_BASE_LINK_ENABLE"          => "N",
        "PAGER_DESC_NUMBERING"            => "N",
        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
        "PAGER_SHOW_ALL"                  => "N",
        "PAGER_SHOW_ALWAYS"               => "N",
        "PAGER_TEMPLATE"                  => "catalog",
        "PAGER_TITLE"                     => "Новости",
        "PREVIEW_TRUNCATE_LEN"            => "150",
        "SEF_FOLDER"                      => "/novosti/",
        "SEF_MODE"                        => "Y",
        "SEF_URL_TEMPLATES"               => Array(
            "section" => "",
            "detail"  => "#ELEMENT_CODE#/",
            "news"    => "",
        ),
        "SET_LAST_MODIFIED"               => "N",
        "SET_STATUS_404"                  => "N",
        "SET_TITLE"                       => "N",
        "SHOW_404"                        => "N",
        "SORT_BY1"                        => "ACTIVE_FROM",
        "SORT_BY2"                        => "SORT",
        "SORT_ORDER1"                     => "DESC",
        "SORT_ORDER2"                     => "ASC",
        "USE_CATEGORIES"                  => "N",
        "USE_FILTER"                      => "N",
        "FILTER_NAME"                     => "arNewssFilter",
        "USE_PERMISSIONS"                 => "N",
        "USE_RATING"                      => "N",
        "USE_RSS"                         => "N",
        "USE_SEARCH"                      => "N"
            ), false, array("HIDE_ICONS" => "Y")
    );
    ?>
</div>