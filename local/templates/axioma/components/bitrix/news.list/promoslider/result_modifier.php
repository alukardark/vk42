<?

/*
  BX_RESIZE_IMAGE_EXACT - масштабирует в прямоугольник $arSize c сохранением пропорций, обрезая лишнее;
  BX_RESIZE_IMAGE_PROPORTIONAL - масштабирует с сохранением пропорций, размер ограничивается $arSize;
  BX_RESIZE_IMAGE_PROPORTIONAL_ALT - масштабирует с сохранением пропорций за ширину при этом принимается максимальное значение
 * из высоты/ширины, размер ограничивается $arSize, улучшенная обработка вертикальных картинок.
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

foreach ($arResult["ITEMS"] as &$arItem)
{
    $arItem['DETAIL_RESIZED'] = \CPic::getDetailSrc($arItem, 317, 360, BX_RESIZE_IMAGE_EXACT);
}
?>