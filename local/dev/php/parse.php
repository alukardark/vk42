<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$arParams["dbhost"] = "localhost";
$arParams["dblogin"] = "cs43658_site";
$arParams["dbpass"] = "cs43658_site";
$arParams["dbname"] = "cs43658_site";

$arParams["IBLOCK_ID"] = 17;
$arParams["prop_small"] = 85; //id Свойства для small картинки
$arParams["prop_medium"] = 86; //id Свойства для medium картинки
$arParams["prop_big"] = 87; //id Свойства для big картинки
$arParams["section"] = 5; //id секции с каталогом
$arParams["tbl_categories"] = "jos_categories";  //таблица с категориями
$arParams["tbl_content"] = "jos_content";  //таблица с контентом
$arParams["old_site"] = "/upload/old_site/";
$arParams["new_site"] = "/upload/old_site/images/stories/";
//db connect
if (!$obConnect = mysql_pconnect($arParams["dbhost"], $arParams["dblogin"], $arParams["dbpass"])) {
    echo '<b style="color: red">mysql_pconnect fail</b>';
    $err = true;
    die();
}
echo '<b style="color: green">mysql_pconnect ok</b><br/>';

if (!mysql_select_db($arParams["dbname"])) {
    echo '<b style="color: red">mysql_select_db fail</b>';
    die();
}
echo '<b style="color: green">mysql_select_db ok</b><br/>';

//set encoding
if (SITE_CHARSET == 'windows-1251')
    mysql_query('SET NAMES cp1251');
else
    mysql_query('SET NAMES utf8');


CModule::IncludeModule("iblock");
//$obIBlock = new CIBlockType;
$obSection = new CIBlockSection;
$obElement = new CIBlockElement;

//создаем инфоблок
/* $arFields = Array(
  'ID' => 'ArchiveCatalog',
  'SECTIONS' => 'Y',
  'SORT' => 100,
  'LANG' => Array(
  'ru' => Array(
  'NAME' => 'Архивный каталог',
  ),
  'en' => Array(
  'NAME' => 'Archive catalog',
  )
  )
  );
  $res = $obBlocktype->Add($arFields); */


//разедлы
$sQuery = "SELECT "
        . "title as NAME, "
        . "ordering as SORT, "
        . "id as CATID "
        . "FROM " . $arParams["tbl_categories"] . " WHERE section = " . $arParams["section"];
$obResult = mysql_query($sQuery, $obConnect);
while ($arItem = mysql_fetch_assoc($obResult)) {
    $arItem["IBLOCK_ID"] = $arParams["IBLOCK_ID"];
    $iSectionId = $obSection->Add($arItem);
    echo "<hr/><b>Add section $iSectionId</b><br/>";
    echo $obSection->LAST_ERROR;

    //элементы
    $sQuery2 = "SELECT "
            . "title as NAME, "
            . "ordering as SORT, "
            . "alias as CODE, "
            . "introtext as DETAIL_TEXT "
            . "FROM " . $arParams["tbl_content"] . " WHERE catid = " . $arItem["CATID"];
    $obResult2 = mysql_query($sQuery2, $obConnect);
    while ($arItemElement = mysql_fetch_assoc($obResult2)) {
        $arItemElement["IBLOCK_ID"] = $arParams["IBLOCK_ID"];
        $arItemElement["IBLOCK_SECTION_ID"] = $iSectionId;
        $arItemElement["DETAIL_TEXT_TYPE"] = "html";
        $arItemElement["PREVIEW_TEXT_TYPE"] = "html";

        //найдем изображения
        $arImagesHtml = array();
        $arImages = array();
        $PROPERTY_IMAGES_SMALL = array();
        $PROPERTY_IMAGES_MEDIUM = array();
        $PROPERTY_IMAGES_BIG = array();

        preg_match_all('/<img[^>]+>/i', $arItemElement["DETAIL_TEXT"], $arImagesHtml);

        if (is_array($arImagesHtml[0]) && count($arImagesHtml[0]) > 0) {

            foreach ($arImagesHtml[0] as $sImageHtml) {
                preg_match_all('/src=("[^"]*")/i', $sImageHtml, $arImages[]);
            }

            if (is_array($arImages) && count($arImages) > 0) {
                foreach ($arImages as $sImage) {
                    $sUrlSmall = $_SERVER["DOCUMENT_ROOT"] . $arParams["old_site"] . str_replace('"', '', $sImage[1][0]);
                    $sUrlMedium = str_replace('/small/', '/medium/', $sUrlSmall);
                    $sUrlBig = str_replace('/small/', '/big/', $sUrlSmall);
   
                    $PROPERTY_IMAGES_SMALL[] = CFile::MakeFileArray($sUrlSmall);
                    $PROPERTY_IMAGES_MEDIUM[] = CFile::MakeFileArray($sUrlMedium);
                    $PROPERTY_IMAGES_BIG[] = CFile::MakeFileArray($sUrlBig);
                }
            }
        }

        $PROPS[$arParams["prop_small"]] = $PROPERTY_IMAGES_SMALL;
        $PROPS[$arParams["prop_medium"]] = $PROPERTY_IMAGES_MEDIUM;
        $PROPS[$arParams["prop_big"]] = $PROPERTY_IMAGES_BIG;
        $arItemElement["PROPERTY_VALUES"] = $PROPS;

        $arItem["DETAIL_TEXT"] = str_replace('src="images/stories/', 'src="' . $arParams['new_site'], $arItem["DETAIL_TEXT"]);
        $arItem["DETAIL_TEXT"] = str_replace('src="items/', 'src="' . $arParams['new_site'] . "items/", $arItem["DETAIL_TEXT"]);


        $iElementId = $obElement->Add($arItemElement);
        echo $obElement->LAST_ERROR;
        //echo "__add element $iElementId<br />";
    }
}


echo "<hr>End.";

