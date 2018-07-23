<?
/*
  Категории товаров
 */

if (SITE_CHARSET == 'windows-1251')
    mysql_query('SET NAMES cp1251');
else
    mysql_query('SET NAMES utf8');

CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");

$this->content .= '<b style="color: green">' . GetMessage("VM_STEP1") . '</b><br/>';

/* если первый шаг - создаем тип и инфоблок */
if ($left == 0) {
    $arFields = Array(
        'ID' => 'VirtueMart',
        'SECTIONS' => 'Y',
        'SORT' => 100,
        'LANG' => Array(
            'en' => Array(
                'NAME' => 'VirtueMart',
            )
        )
    );
    $obBlocktype = new CIBlockType;
    $res = $obBlocktype->Add($arFields);

    $arFields = array(
        "SITE_ID" => "s1",
        "ACTIVE" => "Y",
        "IBLOCK_TYPE_ID" => "VirtueMart",
        "NAME" => GetMessage("CATALOG"),
        "CODE" => "catalog",
        "SORT" => "100",
    );

    $arFields2 = array(
        "SITE_ID" => "s1",
        "ACTIVE" => "Y",
        "IBLOCK_TYPE_ID" => "VirtueMart",
        "NAME" => GetMessage("MANUFACTURERS"),
        "CODE" => "Manufacturer",
        "SORT" => "100",
    );
}

$iblock = new CIBlock;
$res = CIBlock::GetList(array(), array("CODE" => "Manufacturer"))->GetNext();
if ($res)
    $id = $res["ID"];
else {
    $id = $iblock->Add($arFields2);

    $ibp = new CIBlockProperty;
    $prop = Array("NAME" => GetMessage("EMAIL"), "ACTIVE" => "Y", "CODE" => "EMAIL", "PROPERTY_TYPE" => "S", "IBLOCK_ID" => $id);
    $PropID = $ibp->Add($prop);
    $prop = Array("NAME" => GetMessage("URL"), "ACTIVE" => "Y", "CODE" => "URL", "PROPERTY_TYPE" => "S", "IBLOCK_ID" => $id);
    $PropID = $ibp->Add($prop);
}



$res = CIBlock::GetList(array(), array("CODE" => "catalog"))->GetNext();
if ($res)
    $id = $res["ID"];
else {
    $ibp = new CIBlockProperty;
    $id = $iblock->Add($arFields);

    /* Создаем свойства товаров */

    $prop = Array("NAME" => GetMessage("MANUFACTURER"), "ACTIVE" => "Y", "CODE" => "MANUFACTURER", "PROPERTY_TYPE" => "E", "IBLOCK_ID" => $id);
    $PropID = $ibp->Add($prop);

    $prop = Array("NAME" => GetMessage("HEIGHT"), "ACTIVE" => "Y", "CODE" => "HEIGHT", "PROPERTY_TYPE" => "S", "IBLOCK_ID" => $id);
    $PropID = $ibp->Add($prop);

    $prop = Array("NAME" => GetMessage("WIDTH"), "ACTIVE" => "Y", "CODE" => "WIDTH", "PROPERTY_TYPE" => "S", "IBLOCK_ID" => $id);
    $PropID = $ibp->Add($prop);

    $prop = Array("NAME" => GetMessage("LENGTH"), "ACTIVE" => "Y", "CODE" => "LENGTH", "PROPERTY_TYPE" => "S", "IBLOCK_ID" => $id);
    $PropID = $ibp->Add($prop);

    $prop = Array("NAME" => GetMessage("ARTICUL"), "ACTIVE" => "Y", "CODE" => "ARTICUL", "PROPERTY_TYPE" => "S", "IBLOCK_ID" => $id);
    $PropID = $ibp->Add($prop);

    CCatalog::Add(array("IBLOCK_ID" => $id, "YANDEX_EXPORT" => "N", "SUBSCRIPTION" => "N"));

    $arF = array(
        "NAME" => "Base",
        "SORT" => 100,
        "BASE" => "Y",
        "USER_GROUP" => array(1),
        "USER_GROUP_BUY" => array(1),
        "USER_LANG" => array(
            "ru" => "Base",
            "en" => "Base"
        )
    );
    if (!CCatalogGroup::GetList(array(), array())->Fetch())
        $pid = CCatalogGroup::Add($arF);
}



if (SITE_CHARSET == 'windows-1251')
    mysql_query('SET NAMES cp1251');

/* количество записей */
$query = "SELECT COUNT(*) as CNT FROM `" . $arResult["prefix"] . "vm_category`";
$count = mysql_query($query, $link);
$count = mysql_fetch_assoc($count);
$sec = new CIBlockSection;


/* Если левая граница больше количества элементов - обнуляем границы завершаем шаг */

if ($left > $count["CNT"]) {

    $left = 0;
    $right = 10;

    /* Две эти строчки непосредственно завершают шаг и скрипт переходит к следеющему файлу(если он существует) */
    $step += 1;
    $this->content .= $this->ShowHiddenField("step", $step);
} else {

    /* Выбираем категории товаров в XML_ID записываем старый идентификатор, чтобы сразу добавлять родителя */
    $query = "SELECT "
            . "category_id AS XML_ID, "
            . "category_name as NAME, "
            . "category_publish as ACTIVE, "
            . "category_description as DESCRIPTION, "
            . "category_thumb_image as PREVIEW_PICTURE, "
            . "category_full_image as DETAIL_PICTURE, "
            . "( SELECT category_parent_id FROM jos_vm_category_xref WHERE category_child_id = category_id LIMIT 1) AS IBLOCK_SECTION_ID "
            . "FROM `" . $arResult["prefix"] . "vm_category` ORDER BY IBLOCK_SECTION_ID ASC LIMIT " . $left . ", " . $right;
    $result = mysql_query($query, $link);

    while ($arItem = mysql_fetch_assoc($result)) {
        $res1 = CIBlockSection::GetList(array(), array("IBLOCK_ID" => $id, "XML_ID" => $arItem["XML_ID"]))->GetNext();
        if ($res1)
            continue;

        if ($arItem["IBLOCK_SECTION_ID"] > 0) {
            $res = CIBlockSection::GetList(array(), array("IBLOCK_ID" => $id, "XML_ID" => $arItem["IBLOCK_SECTION_ID"]))->GetNext();
            $arItem["IBLOCK_SECTION_ID"] = $res["ID"];
        } else
            unset($arItem["IBLOCK_SECTION_ID"]);

        $image1 = $arResult["site"] . "/category/" . $arItem["PREVIEW_PICTURE"];
        if (substr_count($image1, "http://") == 0)
            $image1 = $_SERVER["DOCUMENT_ROOT"] . $image1;
        $image2 = $arResult["site"] . "/category/" . $arItem["DETAIL_PICTURE"];
        if (substr_count($image2, "http://") == 0)
            $image2 = $_SERVER["DOCUMENT_ROOT"] . $image2;

        if ($arItem["PREVIEW_PICTURE"])
            $arFile = CFile::MakeFileArray($image1);
        $arItem["PICTURE"] = $arFile;
        if ($arItem["DETAIL_PICTURE"])
            $arFile2 = CFile::MakeFileArray($image2);
        $arItem["DETAIL_PICTURE"] = $arFile2;

        $arItem["DESCRIPTION"] = str_replace('src="images/stories/', 'src="' . $arResult['newurl'], $arItem["DESCRIPTION"]);
        $arItem["DESCRIPTION"] = str_replace('src="items/', 'src="' . $arResult['newurl'] . "items/", $arItem["DESCRIPTION"]);
        $arItem["DESCRIPTION_TYPE"] = "html";

        $arItem["CODE"] = Cutil::translit($arItem["NAME"], "ru", array(
                    "max_len" => "32",
                    "replace_space" => "-",
                    "replace_other" => "-"));

        $arItem["IBLOCK_ID"] = $id;
        $sid = $sec->Add($arItem);
    }

    /* Увеличиваем левую и правую границу */
    $left += 10;
    $right += 10;
}

/* Устанавливаем левую и правую границу */
$this->content .= $this->ShowHiddenField("left", $left);
$this->content .= $this->ShowHiddenField("right", $right);
?>


