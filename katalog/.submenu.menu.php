<?php

$rsIBlocks = \CIBlock::GetList(
                array("sort" => "asc"), array(
            'TYPE'    => 'catalog',
            'SITE_ID' => SITE_ID,
            'ACTIVE'  => 'Y',
                ), false
);

while ($ib = $rsIBlocks->Fetch())
{
    global $APPLICATION;
    $aMenuLinksExt = $APPLICATION->IncludeComponent(
            "bitrix:menu.sections", "", array(
        "IS_SEF"      => "Y",
        "IBLOCK_TYPE" => "catalog",
        "IBLOCK_ID"   => $ib['ID'],
        "SECTION_URL" => $ib['SECTION_PAGE_URL'],
        "DEPTH_LEVEL" => "2",
        "CACHE_TYPE"  => "A",
            ), false
    );
    $aMenuLinks    = array_merge($aMenuLinks, $aMenuLinksExt);
}
?>