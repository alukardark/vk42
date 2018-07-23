<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (!isPost() && empty($_REQUEST['q']))
{
    $arTree = null;

    if ($arResult['FOLDER'] == PATH_CATALOG)
    {
        $arTree = \CCatalogExt::getTree(TIRES_IB);
    }

    if ($arResult['FOLDER'] == PATH_OILS)
    {
        $arTree = \CCatalogExt::getTree(OILS_IB);
    }

    if ($arResult['FOLDER'] == PATH_AKB)
    {
        $arTree = \CCatalogExt::getTree(AKB_IB);
    }

    if ($arResult['FOLDER'] == PATH_DISCS)
    {
        $arTree = \CCatalogExt::getTree(DISCS_IB);
    }

    if (!empty($arTree))
    {
        //ссылка на первый раздел
        $arTemplateVars = array(
            '#SITE_DIR#'     => SITE_DIR,
            '#SECTION_CODE#' => $arTree[0]['CODE'],
            '#IBLOCK_CODE#'  => $arTree[0]['IBLOCK_CODE'],
        );

        $sLink = NormalizeLink(strtr($arTree[0]['SECTION_PAGE_URL'], $arTemplateVars));
        LocalRedirect($sLink, false, '301 Moved permanently');
        exit;
    }
}
