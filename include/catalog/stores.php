<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
global $USER;
$arResult                 = array();
$arResult["STORES"]       = array();
$arResult["TOTAL_AMOUNT"] = 0;

$iProductId          = $arParams['PRODUCT_ID'];
$arStores            = \CCatalogExt::getStores();
$arResult["STORES"]  = $arResult["STORES2"] = \CCatalogExt::getProductAmountByStores($iProductId);

//printrau($arResult["STORES"] );
//остатки на складе "под заказ" необходимо объединить со складом "оптовый склад"
if (isset($arResult["STORES"]['Оптовый склад']))
{
    $arResult["STORES"]['Оптовый склад']['AMOUNT'] += $arResult["STORES"]['Под заказ']['AMOUNT'];
}
else
{
    $arResult["STORES"]['Склад']['AMOUNT'] += $arResult["STORES"]['Под заказ']['AMOUNT'];
}

unset($arResult["STORES"]['Под заказ']);

foreach ($arResult["STORES"] as $storeName => $arProductStoreInfo)
{
    if (empty($arResult["STORES"][$storeName]['AMOUNT']))
    {
        unset($arResult["STORES"][$storeName]);
        continue;
    }

    $arResult["STORES"][$storeName]['STORE_INFO'] = $arStores[$storeName];
    $arResult["TOTAL_AMOUNT"]                     += $arResult["STORES"][$storeName]['AMOUNT'];

    if ($arResult["STORES"][$storeName]['AMOUNT'] > 4)
    {
        $arResult["STORES"][$storeName]['AMOUNT_PRINT'] = "Более 4 шт.";
    }
    else
    {
        $arResult["STORES"][$storeName]['AMOUNT_PRINT'] = $arResult["STORES"][$storeName]['AMOUNT'] . " шт.";
    }
}

foreach ($arResult["STORES2"] as $storeName => $arProductStoreInfo)
{
    $arResult["STORES2"][$storeName]['STORE_INFO'] = $arStores[$storeName];
}
?>
<div class="ve">
    <table class="catalog-detail-stores">
        <thead>
            <tr>
                <th>Адрес</th>
                <th>Наличие</th>
                <th>Режим работы</th>
            </tr>
        </thead>
        <tbody>
            <? if (!empty($arResult["TOTAL_AMOUNT"])): ?>
                <?
                foreach ($arResult["STORES"] as $storeName => $arStore):
                    $arStoreInfo          = $arStore["STORE_INFO"];
                    if (empty($arStore) || empty($arStoreInfo)) continue;
                    if ($arStoreInfo['TITLE'] == 'Оптовый склад') $arStoreInfo['TITLE'] = 'Склад';
                    ?>
                    <tr>
                        <td>
                            <?= $arStoreInfo["ADDRESS"] ?><br />
                            <strong class="uppercase"><?= $arStoreInfo["TITLE"] ?></strong>
                        </td>
                        <td class="bold"><?= $arStore["AMOUNT_PRINT"] ?></td>
                        <td>
                            <? if (!empty($arStoreInfo["SCHEDULE"])): ?>
                                <i class="ion-clock"></i><?= $arStoreInfo["SCHEDULE"] ?>
                            <? endif; ?>

                            <? if (empty($arStoreInfo["SCHEDULE"]) && empty($arStoreInfo["PHONE"])): ?>
                                Не указано
                            <? endif; ?>

                            <? if (!empty($arStoreInfo["SCHEDULE"]) && !empty($arStoreInfo["PHONE"])): ?>
                                <br/><br/>
                            <? endif; ?>

                            <? if (!empty($arStoreInfo["PHONE"])): ?>
                                <i class="ion-ios-telephone"></i><?= $arStoreInfo["PHONE"] ?>
                            <? endif; ?>
                        </td>
                    </tr>
                <? endforeach; ?>
            <? else: ?>
                <tr class="full-row">
                    <td colspan="3">
                        <div>Информация недоступна</div>
                    </td>
                </tr>

            <? endif; ?>

            <!--tr class="full-row">
                <td colspan="3">
                    <div class="catalog-detail-ordermore">
                        <span><?= \Axi::GT("catalog/catalog-detail-ordermore"); ?></span>
                        <a
                            title="Заказать больше"
                            href="<?= PATH_BASKET ?>"
                            >Заказать больше</a>
                    </div>
                </td>
            </tr-->
        </tbody>
    </table>


    <? if ($USER->IsAdmin()): ?>
        <br/>
        <table class="catalog-detail-stores">
            <thead>
                <tr>
                    <th>Адрес</th>
                    <th>Наличие</th>
                    <th>Режим работы</th>
                </tr>
            </thead>
            <tbody>
                <?
                foreach ($arResult["STORES2"] as $storeName => $arStore):
                    $arStoreInfo = $arStore["STORE_INFO"];
                    //if (empty($arStore) || empty($arStoreInfo)) continue;
                    //if ($arStoreInfo['TITLE'] == 'Оптовый склад') $arStoreInfo['TITLE'] = 'Склад';
                    ?>
                    <tr>
                        <td>
                            <?= $arStoreInfo["ADDRESS"] ?><br />
                            <strong class="uppercase"><?= $arStoreInfo["TITLE"] ?></strong>
                        </td>
                        <td class="bold"><?= $arStore["AMOUNT"] ?></td>
                        <td>
                            <? if (!empty($arStoreInfo["SCHEDULE"])): ?>
                                <i class="ion-clock"></i><?= $arStoreInfo["SCHEDULE"] ?>
                            <? endif; ?>

                            <? if (empty($arStoreInfo["SCHEDULE"]) && empty($arStoreInfo["PHONE"])): ?>
                                Не указано
                            <? endif; ?>

                            <? if (!empty($arStoreInfo["SCHEDULE"]) && !empty($arStoreInfo["PHONE"])): ?>
                                <br/><br/>
                            <? endif; ?>

                            <? if (!empty($arStoreInfo["PHONE"])): ?>
                                <i class="ion-ios-telephone"></i><?= $arStoreInfo["PHONE"] ?>
                            <? endif; ?>
                        </td>
                    </tr>
                <? endforeach; ?>

                <tr class="full-row">
                    <td colspan="3">
                        <div class="catalog-detail-ordermore">
                            <span><?= \Axi::GT("catalog/catalog-detail-ordermore"); ?></span>
                            <a
                                title="Заказать больше"
                                href="<?= PATH_BASKET ?>"
                                >Заказать больше</a>
                        </div>
                    </td>
                </tr
            </tbody>
        </table>

    <? endif; ?>
</div>