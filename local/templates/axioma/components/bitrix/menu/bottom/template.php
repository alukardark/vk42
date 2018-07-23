<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
?>

<? if (!empty($arResult)): ?>
    <ul class="root-list noliststyle">
        <?
        $previousLevel = 0;
        foreach ($arResult as $arItem):
            $isParent = $arItem["IS_PARENT"];
            $level    = $arItem["DEPTH_LEVEL"];
            $link     = $arItem["LINK"];
            $text     = $arItem["TEXT"];
            $title    = htmlspecialchars($arItem["TEXT"]);
            $selected = $arItem["SELECTED"];
            $target   = $arItem['PARAMS']["EXTERNAL"] ? ' target="_blank" ' : '';
            ?>

            <? if ($previousLevel && $level < $previousLevel): ?>
                <?= str_repeat("</ul></li>", ($previousLevel - $level)); ?>
            <? endif ?>

            <? if ($isParent): ?>
                <? if ($level == 1): ?>
                    <li data-menu-root-item="1" class="root-item root-item-parent clearfix noselect <?= $selected ? "selected" : "" ?>" title="<?= $title ?>">
                        <? if ($link == "#" || empty($link)): ?>
                            <span><?= $text ?></span>
                        <? else: ?>
                            <a href="<?= $link ?>" title="<?= $title ?>" <?= $target ?>><?= $text ?></a>
                        <? endif; ?>
                        <ul data-menu-children-list="1" class="children-list noliststyle">
                        <? else: ?>
                            <li class="children-item <?= $selected ? "selected" : "" ?>">
                                <a href="<?= $link ?>" class="parent" title="<?= $title ?>" <?= $target ?>><?= $text ?></a>
                                <ul class="noliststyle">
                                <? endif; ?>
                            <? else: ?>
                                <? if ($level == 1): ?>
                                    <li class="root-item clearfix noselect <?= $selected ? "selected" : "" ?>">
                                        <? if ($link == "#" || empty($link)): ?>
                                            <span><?= $text ?></span>
                                        <? else: ?>
                                            <a href="<?= $link ?>" title="<?= $title ?>" <?= $target ?>><?= $text ?></a>
                                        <? endif; ?>
                                    </li>
                                <? else: ?>
                                    <li class="children-item <?= $selected ? "selected" : "" ?>">
                                        <a
                                            href="<?= $link ?>"
                                            title="<?= $title ?>"
                                            <?= $target ?>
                                            onclick="Menu.navMobileClose(this)"
                                            ><?= $text ?></a>
                                    </li>
                                <? endif; ?>
                            <? endif; ?>

                            <? $previousLevel = $level; ?>
                        <? endforeach; ?>

                        <? if ($previousLevel > 1): ?>
                            <?= str_repeat("</ul></li>", ($previousLevel - 1)); ?>
                        <? endif; ?>
                    </ul>
                <? endif; ?>