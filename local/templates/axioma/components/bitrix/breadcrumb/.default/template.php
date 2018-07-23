<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

global $APPLICATION;

if (empty($arResult)) return "";

$strReturn = '';

$strReturn .= '<a href="/" title="Главная">Главная</a><i>/</i>';
$itemSize  = count($arResult);
for ($index = 0; $index < $itemSize; $index++)
{
    $title = htmlspecialcharsex($arResult[$index]["TITLE"]);
    if ($arResult[$index]["LINK"] <> "" && $index != $itemSize - 1)
    {
        $strReturn .= '<a href="' . $arResult[$index]["LINK"] . '" title="' . $title . '">' . $title . '</a><i>/</i>';
    }
    else
    {
        $strReturn .= '<span title="' . $title . ' (текущая страница)">' . $title . '</span>';
    }
}

return '<div class="header-inner-breadcrumbs">' . $strReturn . '</div>';
