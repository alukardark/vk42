<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
?>

<? if (!empty($arResult)): ?>
    <nav class="nav-top hidden-lg-down">
        <ul class="nav-top-list">
            <?
            foreach ($arResult as $arItem):
                $selected = $arItem["SELECTED"] ? 'selected' : '';
                $target   = $arItem['PARAMS']['EXTERNAL'] ? ' target="_blank" ' : ''
                ?>
                <li class="<?= $selected ?>">
                    <a
                        href="<?= $arItem["LINK"] ?>"
                        title="<?= $arItem["TEXT"] ?>"
                        data-rel="innerlink"
                        <?= $target ?>
                        >

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
                </li>
            <? endforeach ?>
        </ul>
    </nav>
<? endif; ?>