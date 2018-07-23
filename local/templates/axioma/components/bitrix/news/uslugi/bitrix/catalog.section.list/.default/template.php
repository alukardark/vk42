<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);

$strSectionEdit        = \CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_EDIT");
$strSectionDelete      = \CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_DELETE");
$arSectionDeleteParams = array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM'));
?>

<div class="uslugi-list row">
    <? foreach ($arResult['SECTIONS'] as $arSection): ?>
        <?
        $this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], $strSectionEdit);
        $this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);

        $sTtitle  = $arSection["NAME"];
        $sIconSrc = $arSection["PICTURE"]["SRC"];
        $sIconAlt = $arSection["PICTURE"]["ALT"];
        ?>
        <div id="<?= $this->GetEditAreaId($arSection['ID']); ?>" class="uslugi-list-item">
            <a
                class="uslugi-list-item-content <?= !$arSection["ACTIVE"] ? "inactive" : "" ?>"
                href="<?= $arSection["SECTION_PAGE_URL"]; ?>"
                title="<?= $arSection["NAME"]; ?>"
                data-section-id="<?= $arSection["ID"]; ?>"
                >
                <figure class="uslugi-list-item-icon">
                    <img src="<?= $sIconSrc ?>" alt="<?= $sIconAlt ?>" />
                </figure>

                <div class="uslugi-list-item-title">
                    <span><?= $arSection["NAME"]; ?></span>
                </div>

                <div class="uslugi-list-item-next">
                    <figure class="uslugi-list-item-next-icon">
                        <i class="ion-ios-arrow-right"></i>
                    </figure>
                </div>

                <? if (!empty($arSection["ELEMENTS"])): ?>
                    <ul class="uslugi-list-item-elements">
                        <? foreach ($arSection["ELEMENTS"] as $arSectionElement): ?>
                            <li
                                class="<?= !$arSectionElement["ACTIVE"] ? "inactive" : "" ?>"
                                data-element-id="<?= $arSectionElement['ID'] ?>"
                                ><?= $arSectionElement['NAME'] ?>
                            </li>
                        <? endforeach; ?>
                    </ul>
                <? endif; ?>
            </a>
        </div>
    <? endforeach; ?>
</div>