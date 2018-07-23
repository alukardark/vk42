<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
?>

<?if (!empty($arResult)):?>
    <nav class="nav-top hidden-lg-down">
        <ul class="nav-top-list">

    <?
    $previousLevel = 0;
    foreach($arResult as $arItem):
        $selected = $arItem["SELECTED"] ? 'selected' : '';
        $target   = $arItem['PARAMS']['EXTERNAL'] ? ' target="_blank" ' : '';
        ?>

            <?if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):?>
                    <?=str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));?>
            <?endif?>

            <?if ($arItem["DEPTH_LEVEL"] == 1):?>

                <li class="<?= $selected ?>">                            
                    <a class="<?= $arItem["IS_PARENT"] ? 'drop' : '' ?>" href="<?= $arItem["LINK"] ?>" title="<?= $arItem["TEXT"] ?>" data-rel="innerlink" <?= $target ?> >
                        <?
                        $showed   = false;

                        if (!empty($arItem['PARAMS'])):
                            ?>
                            <?
                            foreach ($arItem['PARAMS'] as $key => $param):
                                if ($key == "EXTERNAL") continue;
                                $showed = true;
                                ?>
                                <span class="hidden-<?= $key ?>-down"><?= $arItem["TEXT"] ?></span>
                                <span class="hidden-<?= $key ?>-up"><?= $param ?></span>
                            <? endforeach; ?>
                        <? endif; ?>

                        <? if (!$showed): ?>
                            <span><?= $arItem["TEXT"] ?></span>
                        <? endif; ?>
                    </a>
                    <?if($arItem["IS_PARENT"]):?>
                        <ul class="submenu-top-list paddings">
                    <?endif?>

            <?else:
                $alias = end(array_filter(explode('/', $arItem['LINK'])));
                ?>

                <li>
                    <i class="item-section-icon item-section-icon--<?=$alias?>"></i>
                    <a href="<?= $arItem["LINK"] ?>"><?=$arItem["TEXT"]?></a>
                    <?if($arItem["IS_PARENT"]):?>
                        <ul class="submenu-top-list">
                    <?endif?>

            <?endif?>

            <?$previousLevel = $arItem["DEPTH_LEVEL"];?>

    <?endforeach?>

    <?if ($previousLevel > 1)://close last item tags?>
            <?=str_repeat("</ul></li>", ($previousLevel-1) );?>
    <?endif?>

        </ul>
    </nav>
<?endif?>