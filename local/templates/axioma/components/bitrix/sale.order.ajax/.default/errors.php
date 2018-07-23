<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>
<div id="order-errors" class="order-errors <?= empty($arErrors) ? "hidden" : "" ?> ">
    <div class="order-errors-title">Обнаружены ошибки!</div>
    <div class="order-errors-list">
        <? foreach ($arErrors as $sError): ?>
            <span><?= $sError ?></span>
        <? endforeach; ?>
    </div>
</div>