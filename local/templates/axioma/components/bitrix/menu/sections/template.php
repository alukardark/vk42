<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?
global $APPLICATION;
$sCurDir = $APPLICATION->GetCurDir();

$curAlias = \Axi::getAlias();
//printrau($sCurDir);
?>

<? if (!empty($arResult)): ?>
    <nav id="nav-sections" class="nav-sections <?= $curAlias != "index-page" ? "hidden-lg-down" : "" ?>">
        <ul class="nav-sections-list">
            <?
            foreach ($arResult as $i => $arItem):
                $selected = $arItem["SELECTED"] || startsWith($sCurDir, $arItem["PARAMS"]["path"]) ? 'selected' : '';
                $code     = $arItem["PARAMS"]["code"];
                ?>
                <li class="<?= $selected ?>">
                    <? if ($curAlias != "index-page"): ?>
                        <a
                            href="<?= $arItem["LINK"] ?>"
                            title="<?= $arItem["TEXT"] ?>"
                            ><span><?= $arItem["TEXT"] ?></span>
                        </a>
                    <? else: ?>
                        <button
                            onclick="Index.setFilterContainer(this, '<?= $code ?>')"
                            class="<?= $i === 0 ? "selected" : "" ?>"
                            title="<?= $arItem["TEXT"] ?>"
                            ><span><?= $arItem["TEXT"] ?></span>
                        </button>
                    <? endif; ?>
                </li>
            <? endforeach ?>
        </ul>
    </nav>
<? endif; ?>