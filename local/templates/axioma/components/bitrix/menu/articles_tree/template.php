<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<?
if (!empty($arResult)):
    $previousLevel = 0;
    ?>

    <div class="articles-menu-title">
        <a
            href="/articles/"
            data-target="#root"
            data-toggle="collapse"
            data-menu-item="title"
            title="Рубрики"
            onclick="Actions.doMenu(this, event)"
            >Рубрики<i class="ion-ios-arrow-down float-right hidden-sm-up2"></i></a>
    </div>

    <ul class="root collapse" id="root">
        <? foreach ($arResult as $id => $arItem): ?>

            <? if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel): ?>
                <?= str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"])); ?>
            <? endif; ?>

            <?
            if ($arItem["IS_PARENT"]):
                $selected = $arItem["SELECTED"] || $arItem["CHILD_SELECTED"];
                ?>

                <li class="parent">
                    <div class="item <?= $arItem["SELECTED"] ? "selected" : "" ?>">
                        <a
                            href="<?= $arItem["LINK"] ?>"
                            data-target="#<?= md5($arItem["LINK"]) ?>"
                            data-toggle="collapse"
                            data-menu-item="parent"
                            title="<?= $arItem["TEXT"] ?>"
                            onclick="Actions.doMenu(this, event)"
                            >
                                <?= $arItem["TEXT"] ?>
                            <i class="<?= $selected ? "ion-arrow-down-b" : "ion-arrow-right-b" ?>"></i>
                        </a>
                    </div>
                    <ul
                        class="node collapse <?= $selected ? "in" : "" ?>"
                        id="<?= md5($arItem["LINK"]) ?>"
                        >

                    <? else: ?>

                        <li class="<?= $arItem["DEPTH_LEVEL"] == 1 ? "parent" : "child" ?>">
                            <div class="item <?= $arItem["SELECTED"] ? "selected" : "" ?>">
                                <a
                                    href="<?= $arItem["LINK"] ?>"
                                    title="<?= $arItem["TEXT"] ?>"
                                    data-menu-item="child"
                                    onclick="Actions.doMenu(this, event)"
                                    ><?= $arItem["TEXT"] ?></a>
                            </div>
                        </li>

                    <? endif; ?>

                    <? $previousLevel = $arItem["DEPTH_LEVEL"]; ?>

                <? endforeach ?>

                <? if ($previousLevel > 1)://close last item tags?>
                    <?= str_repeat("</ul></li>", ($previousLevel - 1)); ?>
                <? endif; ?>

            </ul>
        <? endif; ?>