<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
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
                foreach ($arResult["STORES"] as $arProperty):
                    $arPropertyOriginal = $arResult["STORES_ORIGINAL"][$arProperty['ID']];
                    ?>
                    <? if (isset($arProperty['REAL_AMOUNT']) && $arProperty['REAL_AMOUNT'] <= 0 && $arParams['SHOW_EMPTY_STORE'] != 'Y') continue; ?>
                    <tr>
                        <td>
                            <?= $arPropertyOriginal["ADDRESS"] ?><br />
                            <strong class="uppercase"><?= $arPropertyOriginal["TITLE"] ?></strong>
                        </td>
                        <td class="bold"><?= $arProperty["REAL_AMOUNT"] ?> шт.</td>
                        <td>
                            <? if (!empty($arPropertyOriginal["SCHEDULE"])): ?>
                                <i class="ion-clock"></i><?= $arPropertyOriginal["SCHEDULE"] ?>
                            <? else: ?>
                                Не указано
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
                        <span><?= Axi::GT("catalog/catalog-detail-ordermore"); ?></span>                        
                        <a
                            title="Заказать больше"
                            href="<?= PATH_BASKET ?>"
                            >Заказать больше</a>                        
                    </div>
                </td>
            </tr-->

        </tbody>
    </table>
</div>