<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/install/wizard_sol/wizard.php");

class UploadFile extends CWizardStep
{

    function InitStep()
    {
        $this->SetStepID("UploadFile");
        $this->SetTitle("Загрузка файла");
        $this->SetNextStep("MigrationConfig");
        $this->SetNextCaption(GetMessage("NEXT"));
        $this->SetPrevCaption(GetMessage("PREV"));
    }

    function ShowStep()
    {
        $this->content .= '<table>';

        $wizard = & $this->GetWizard();
        //$template_path = $wizard->GetVar("template");
        //$this->content .= $this->ShowHiddenField("template", $template_path);

        $prefix = $wizard->GetVar("prefix");

        $this->content .= '<tr><td>';
        $this->content .= "Файл *.sql: </td><td>" . $this->ShowFileField("file", Array()) . "";
        $this->content .= '</td></tr>';

        // $this->content .= $this->ShowHiddenField("left", 0);
        // $this->content .= $this->ShowHiddenField("right", 10);
        // $this->content .= $this->ShowHiddenField("step", 0);
        // $this->content .= $this->ShowHiddenField("finish", 0);

        $this->content .= '</table>';
    }

    function onPostForm()
    {
        global $DB;

        $wizard = & $this->GetWizard();
        $wizard->SetVar("file_uploaded", false);
        $this->SaveFile("file", Array("extensions" => "sql"));

        $fileID = $wizard->GetVar("file");
        $file   = $_SERVER['DOCUMENT_ROOT'] . \CFile::GetPath($fileID);

        $wizard->SetVar("file_uploaded", true);
        return true;

        if (!empty($fileID) && file_exists($file))
        {
            $cn = new MyConnection();
            $cn->close();
            $DB->RunSqlBatch($file);
            $wizard->SetVar("file_uploaded", true);
        }
    }

}

class MigrationConfig extends CWizardStep
{

    function onPostForm()
    {
        clear_ib(TX_CARS_IB);
        clear_ib(TX_TYRES_IB);
        clear_ib(TX_DISKS_IB);

        $obElement = new \CIBlockElement;

        //автомобили
        global $DB;
        $strSql = "SELECT * FROM tx_carmodels";
        $res    = $DB->Query($strSql, false, $err_mess . __LINE__);
        while ($row    = $res->Fetch())
        {
            $arItemElement["IBLOCK_ID"]       = TX_CARS_IB;
            $arItemElement["NAME"]            = $row['vendor'] . ' ' . $row['model'] . ' ' . $row['modification'] . ' ' . $row['year'];
            $arItemElement["PROPERTY_VALUES"] = $row;

            $obElement->Add($arItemElement);
            unset($arItemElement);
        }

        //шины
        $strSql = "SELECT * FROM tx_tyrespecifications";
        $res    = $DB->Query($strSql, false, $err_mess . __LINE__);
        while ($row    = $res->Fetch())
        {
            $iCarModel = $row['carmodel'];

            $arCarSelect = Array("ID", "NAME");
            $arCarFilter = Array("IBLOCK_ID" => TX_CARS_IB, "PROPERTY_id" => $iCarModel);
            $obCarList   = \CIBlockElement::GetList(Array(), $arCarFilter, false, false, $arCarSelect);
            if ($arCar       = $obCarList->GetNext(false, false))
            {
                $iCarLink = $arCar['ID'];
                $iCarName = $arCar['NAME'];
            }

            $arItemElement["IBLOCK_ID"]                        = TX_TYRES_IB;
            $arItemElement["NAME"]                             = 'Tyre ' . $row['id'] . ' for ' . $iCarName . ' ' . $row['spectype'];
            $arItemElement["PROPERTY_VALUES"]                  = $row;
            $arItemElement["PROPERTY_VALUES"]['carmodel_link'] = $iCarLink;

            $obElement->Add($arItemElement);
            unset($arItemElement);
        }

        //колеса
        $strSql = "SELECT * FROM tx_wheelspecifications";
        $res    = $DB->Query($strSql, false, $err_mess . __LINE__);
        while ($row    = $res->Fetch())
        {
            $iCarModel = $row['carmodel'];

            $arCarSelect = Array("ID", "NAME");
            $arCarFilter = Array("IBLOCK_ID" => TX_CARS_IB, "PROPERTY_id" => $iCarModel);
            $obCarList   = \CIBlockElement::GetList(Array(), $arCarFilter, false, false, $arCarSelect);
            if ($arCar       = $obCarList->GetNext(false, false))
            {
                $iCarLink = $arCar['ID'];
                $iCarName = $arCar['NAME'];
            }

            $arItemElement["IBLOCK_ID"]                        = TX_DISKS_IB;
            $arItemElement["NAME"]                             = 'Wheel ' . $row['id'] . ' for ' . $iCarName . ' ' . $row['spectype'];
            $arItemElement["PROPERTY_VALUES"]                  = $row;
            $arItemElement["PROPERTY_VALUES"]['carmodel_link'] = $iCarLink;

            $obElement->Add($arItemElement);
            unset($arItemElement);
        }
    }

    function InitStep()
    {
        $this->SetStepID("MigrationConfig");
        $this->SetTitle("Конвертация");
        $this->SetNextStep("COMPLETE");
        $this->SetPrevStep("UploadFile");

        $wizard       = & $this->GetWizard();
        $fileUploaded = $wizard->GetVar("file_uploaded");

        if ($fileUploaded == true) $this->SetNextCaption("Конвертировать");
        $this->SetPrevCaption(GetMessage("PREV"));
    }

    function ShowStep()
    {
        $this->content .= '<table>';
        $wizard        = & $this->GetWizard();
        $fileUploaded  = $wizard->GetVar("file_uploaded");

        if ($fileUploaded) $this->content .= '<b style="color: green">' . "Файл успешно загружен" . '</b><br/>';
        else $this->content .= '<b style="color: red">' . "Ошибка загрузки файла" . '</b><br/>';

        $this->content .= '</table>';
    }

}

class COMPLETE extends CFinishWizardStep
{

    function onPostForm()
    {
        
    }

    function InitStep()
    {
        $this->SetStepID("COMPLETE");
        $this->SetTitle(GetMessage("COMPLETE"));
        $this->SetPrevStep("MigrationConfig");
        //$this->SetNextStep("FinishStep");
        //$this->SetNextCaption(GetMessage("MORE"));
        //$this->SetPrevCaption(GetMessage("PREV"));
    }

    function ShowStep()
    {
        $this->content .= '
		<div style="float: left; margin-top: 30px;">
		<a href="/bitrix/admin/wizard_list.php" class="">
		<span id="next-button-caption" class="wizard-next-button">' . GetMessage("GO_TO_SITE") . '</span>
		</a>
		</div>';


        //$this->content .= '<b style="color: green">'.GetMessage("MASTER_COMPLETE").'</b><br/>';
        //$this->content .= '<br/><br/><b style="display:block; float: right; font-size: 12px; font-family: verdana; text-align: right;"><a href="/bitrix/admin/">'.GetMessage("ADMIN").'</a></b><br/><br/><br/>';
        //$this->content .= '<br/><span style="font-size: 12px; font-family: verdana;">'.GetMessage("NOTICE_FINISH").'</span>';
    }

}

class FinishStep extends CFinishWizardStep
{
    
}
?>
