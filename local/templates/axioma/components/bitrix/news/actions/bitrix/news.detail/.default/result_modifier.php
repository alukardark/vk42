<?

/*
  BX_RESIZE_IMAGE_EXACT - масштабирует в прямоугольник $arSize c сохранением пропорций, обрезая лишнее;
  BX_RESIZE_IMAGE_PROPORTIONAL - масштабирует с сохранением пропорций, размер ограничивается $arSize;
  BX_RESIZE_IMAGE_PROPORTIONAL_ALT - масштабирует с сохранением пропорций за ширину при этом принимается максимальное значение
 * из высоты/ширины, размер ограничивается $arSize, улучшенная обработка вертикальных картинок.
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arResult['DETAIL_RESIZED'] = \CPic::getDetailSrc($arResult, 750, 370, BX_RESIZE_IMAGE_EXACT);

$arResult['ICON_RESIZED'] = false;
if (!empty($arResult['PROPERTIES']['ICON']['VALUE']))
{
    $arResult['ICON_RESIZED'] = \CPic::getResized($arResult['PROPERTIES']['ICON']['VALUE'], 150, 38, BX_RESIZE_IMAGE_EXACT);
}
?>