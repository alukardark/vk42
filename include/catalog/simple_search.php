<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>
<div class="smartfilter clearfix">
    <div class="smartfilter-title">
        <span>Поиск:</span>
    </div>

    <div class="smartfilter-separator"></div>

    <div class="smartfilter-block smartfilter-block-notitle">
        <form class="smartfilter-search-form" action="" method="get" onsubmit="yaCounter12153865.reachGoal('poisk');">
            <input
                class="smartfilter-search-form__input"
                type="search"
                name="q"
                placeholder="Поиск"
                value="<?= !empty($_REQUEST['q']) ? htmlspecialchars($_REQUEST['q']) : "" ?>"
                />
            <input
                class="smartfilter-search-form__submit"
                type="submit"
                value="Искать"
                />
        </form>        
    </div>
</div>