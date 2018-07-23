<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>
<div class="order-gift col-16 col-xl-14 col-lg-11 col-md-24 hidden">
    <div class="row">
        <div class="col-16 col-md-24">
            <? if (\WS_PSettings::getFieldValue("SHOW_BONUSES_INFO", false)): ?>
                <?= \Axi::GT("catalog/catalog-detail-notes-bonus-full"); ?>
            <? else: ?>
                <div style="height: 1px;"></div>
            <? endif; ?>
        </div>
        <div class="hidden-md-up">
            <br/><br/>
        </div>
    </div>
</div>