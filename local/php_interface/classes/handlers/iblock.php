<?php

namespace Axi\Handlers;

class IBlock
{
    private static $CATALOGS = array(TIRES_IB, OILS_IB, AKB_IB, DISCS_IB);

    function OnBeforeIBlockElementAdd(&$arFields)
    {
        //\CCatalogExt::setDiscSortPropValue($arFields);
    }

    function OnBeforeIBlockElementUpdate(&$arFields)
    {
        //\CCatalogExt::setDiscSortPropValue($arFields);
    }

    function OnBeforeIBlockUpdate(&$arParams)
    {
        global $USER;
        $arErrors = array();

        if ($_REQUEST['mode'] == 'import')
        {
            unset($arParams['PICTURE']);
            unset($arParams['DESCRIPTION']);
        }

        $USER_GROUPS = \CUser::GetUserGroup($USER->GetID());

        if (in_array(13, $USER_GROUPS))
        {
            $arErrors[] = "Недостаточно прав";
        }

        return AdminException($arErrors);
    }

    function OnAfterIBlockElementAdd(&$arFields)
    {

        if ($_REQUEST['mode'] != 'import' && in_array($arFields['IBLOCK_ID'], self::$CATALOGS))
        {
            
        }

        if ($_REQUEST['mode'] != 'import' && $arFields['IBLOCK_ID'] == DISCS_IB)
        {
            
        }
    }

    function OnAfterIBlockElementUpdate(&$arFields)
    {
        
    }

}
