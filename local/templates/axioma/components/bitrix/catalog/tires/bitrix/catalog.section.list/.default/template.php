<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>
<?
//printra($arResult); 
if ($arResult["SECTIONS_COUNT"] > 1):
    ?>
    <div class="header-inner-sections clearfix">
        <ul class="noliststyle">
            <? foreach ($arResult['SECTIONS'] as $arSection) : ?>
                <li class="<?= $APPLICATION->GetCurDir() == $arSection['SECTION_PAGE_URL'] ? "active" : "" ?>">
                    <a href="<?= $arSection['SECTION_PAGE_URL'] ?>" title="<?= $arSection['NAME'] ?>">
                        <?= $arSection['NAME'] ?>
                    </a>
                </li>
            <? endforeach; ?>
        </ul>
    </div>
<? endif; ?>