<?

class MigrationStart extends CWizardStep
{

    function onPostForm()
    {
        
    }

    function InitStep()
    {
        $this->SetStepID("MigrationStart");
        $this->SetTitle("Импорт");
        $this->SetNextStep("MigrationStart");
        $this->SetPrevStep("MigrationConfig");

        $wizard = &$this->GetWizard();
        $finish = $wizard->GetVar("finish");

        if ($finish == true) $this->SetNextStep("COMPLETE");

        $this->SetNextCaption(GetMessage("NEXT"));
    }

    function ShowStep()
    {
        global $DB;
        $obElement = new CIBlockElement;
        $wizard    = &$this->GetWizard();

        $onStep    = $wizard->GetVar("onStep");
        $onStepDel = $wizard->GetVar("onStepDel");

        $steps_del_cars   = $wizard->GetVar("steps_del_cars");
        $steps_del_tires  = $wizard->GetVar("steps_del_tires");
        $steps_migr_auto  = $wizard->GetVar("steps_migr_auto");
        $steps_migr_tires = $wizard->GetVar("steps_migr_tires");

        $step_del_cars   = $wizard->GetVar("step_del_cars");
        $step_del_tires  = $wizard->GetVar("step_del_tires");
        $step_migr_auto  = $wizard->GetVar("step_migr_auto");
        $step_migr_tires = $wizard->GetVar("step_migr_tires");

        $finish_del_cars   = $wizard->GetVar("finish_del_cars");
        $finish_del_tires  = $wizard->GetVar("finish_del_tires");
        $finish_migr_auto  = $wizard->GetVar("finish_migr_auto");
        $finish_migr_tires = $wizard->GetVar("finish_migr_tires");


        $this->content .= '<table>';

        if ($finish_del_cars != true)
        {
            $this->content .= "<b>Очистка инфоблока автомобилей</b> ... ";
            $this->content .= "шаг $step_del_cars из $steps_del_cars<br/>";
            $this->content .= "Очистка инфоблока шин<br/>";
            $this->content .= "Импорт базы автомобилей в инфоблок<br/>";
            $this->content .= "Импорт базы шин в инфоблок<br/>";

            $wizard->SetVar("step_del_cars", ++$step_del_cars);
            if (!clear_ib(TX_CARS_IB, $onStepDel))
            {
                $wizard->SetVar("finish_del_cars", true);
            }
        }
        elseif ($finish_del_tires != true)
        {
            $this->content .= "Очистка инфоблока автомобилей ... OK<br/>";
            $this->content .= "<b>Очистка инфоблока шин</b> ... ";
            $this->content .= "шаг $step_del_tires из $steps_del_tires<br/>";
            $this->content .= "Импорт базы автомобилей в инфоблок<br/>";
            $this->content .= "Импорт базы шин в инфоблок<br/>";

            $wizard->SetVar("step_del_tires", ++$step_del_tires);
            if (!clear_ib(TX_TYRES_IB, $onStepDel))
            {
                $wizard->SetVar("finish_del_tires", true);
            }
        }
        elseif ($finish_migr_auto != true)
        {
            $this->content .= "Очистка инфоблока автомобилей ... OK<br/>";
            $this->content .= "Очистка инфоблока шин ... OK<br/>";
            $this->content .= "<b>Импорт базы автомобилей в инфоблок</b> ... ";
            $this->content .= "шаг $step_migr_auto из $steps_migr_auto<br/>";
            $this->content .= "Импорт базы шин в инфоблок<br/>";

            //vendors
            $inprocess_mirg_auto = false;
            $res_lvl2            = $DB->Query("SELECT * FROM auto_parents where level=2 limit $step_migr_auto,1");
            while ($row_lvl2            = $res_lvl2->Fetch())
            {
                $inprocess_mirg_auto = true;
                $vendor_id           = $row_lvl2['id'];
                $vendor              = allTrim($row_lvl2['name']);

                if (empty($vendor))
                {
                    continue;
                }

                //models for vendor
                $res_lvl3 = $DB->Query("SELECT * FROM auto_parents where level=3 and parent_id=" . $vendor_id);
                while ($row_lvl3 = $res_lvl3->Fetch())
                {
                    $model_id = $row_lvl3['id'];
                    $model    = allTrim($row_lvl3['name']);

                    if (empty($model))
                    {
                        continue;
                    }

                    //bodies
                    $res_lvl4 = $DB->Query("SELECT * FROM auto_parents where level=4 and parent_id=" . $model_id);
                    while ($row_lvl4 = $res_lvl4->Fetch())
                    {
                        $body_id = $row_lvl4['id'];
                        $body    = allTrim($row_lvl4['name']);

                        if (empty($body))
                        {
                            continue;
                        }

                        //mods
                        $res_lvl5 = $DB->Query("SELECT * FROM auto_children where parent_id=" . $body_id);
                        while ($row_lvl5 = $res_lvl5->Fetch())
                        {
                            $modification = allTrim($row_lvl5['name']);

                            if (empty($modification))
                            {
                                continue;
                            }

                            $props = array(
                                'modification' => $modification,
                                'body'         => $body,
                                'model'        => $model,
                                'vendor'       => $vendor
                            );

                            $arItemElement                    = array();
                            $arItemElement["IBLOCK_ID"]       = TX_CARS_IB;
                            $arItemElement["NAME"]            = $vendor . ' ' . $model . ' ' . $body . ' ' . $modification;
                            $arItemElement["PROPERTY_VALUES"] = $row_lvl5 + $props;

                            $obElement->Add($arItemElement);
                            unset($arItemElement);
                        }
                    }
                }
            }

            $wizard->SetVar("step_migr_auto", ++$step_migr_auto);
            if (!$inprocess_mirg_auto)
            {
                $wizard->SetVar("finish_migr_auto", true);
            }
        }
        elseif ($finish_migr_tires != true)
        {
            $this->content .= "Очистка инфоблока автомобилей ... OK<br/>";
            $this->content .= "Очистка инфоблока шин ... OK<br/>";
            $this->content .= "Импорт базы автомобилей в инфоблок ...OK<br/>";
            $this->content .= "<b>Импорт базы шин в инфоблок</b> ... ";
            $this->content .= "шаг $step_migr_tires из $steps_migr_tires<br/>";

            $inprocess_migr_tires = false;
            $limit                = $step_migr_tires * $onStep;
            $res                  = $DB->Query("SELECT * FROM auto_context limit $limit,$onStep");
            while ($row                  = $res->Fetch())
            {
                $inprocess_migr_tires = true;
                $type                 = $row['type'];

                $params              = array();
                $params['id']        = $row['id'];
                $params['auto_id']   = $row['auto_id'];
                $params['row']       = $row['row'];
                $params['parent_id'] = $row['parent_id'];
                $params['back_os']   = $row['back_os'];
                $params['type']      = $row['type'];

                $obCache   = \Bitrix\Main\Data\Cache::createInstance();
                $lifeTime  = strtotime("1hour", 0);
                $cachePath = "/ccache_wizard/carmodels/";
                $cacheID   = "carmodels" . TX_CARS_IB . $row['auto_id'];

                if ($obCache->InitCache($lifeTime, $cacheID, $cachePath))
                {
                    $vars = $obCache->GetVars();
                    if (isset($vars["arCar"]))
                    {
                        $arCar    = $vars["arCar"];
                        $lifeTime = 0;
                    }
                }

                if ($lifeTime > 0)
                {
                    $arCarSelect = Array("ID", "NAME");
                    $arCarFilter = Array("IBLOCK_ID" => TX_CARS_IB, "PROPERTY_id" => $row['auto_id']);
                    $obCarList   = \CIBlockElement::GetList(Array(), $arCarFilter, false, false, $arCarSelect);
                    $arCar       = $obCarList->GetNext(false, false);

                    //кешируем
                    $obCache->StartDataCache($lifeTime, $cacheID, $cachePath);
                    $obCache->EndDataCache(array(
                        "arCar" => $arCar,
                    ));
                }

                if ($arCar)
                {
                    $iCarLink = $arCar['ID'];
                    $iCarName = $arCar['NAME'];

                    $params['carmodel_link'] = $iCarLink;

                    $arItemElement = array();

                    if ($row['type'] == 1 || $row['type'] == 3)
                    {
                        $arItemElement["IBLOCK_ID"] = TX_TYRES_IB;
                        $arItemElement["NAME"]      = 'Tire ' . $row['id'] . ' for ' . $iCarName;

                        //шины для передней оси, или для обеих осей, если они совпадают
                        if ($row['type'] == 1)
                        {
                            $params['front_width']    = allTrim($row['val1']);
                            $params['front_profile']  = allTrim($row['val2']);
                            $params['front_diameter'] = allTrim($row['val3']);
                        }
                        //шины для задней оси, если отличаются от передней
                        elseif ($row['type'] == 3)
                        {
                            $params['back_width']    = allTrim($row['val1']);
                            $params['back_profile']  = allTrim($row['val2']);
                            $params['back_diameter'] = allTrim($row['val3']);
                        }
                    }

                    if ($row['type'] == 2 || $row['type'] == 4)
                    {
                        $arItemElement["IBLOCK_ID"] = TX_DISKS_IB;
                        $arItemElement["NAME"]      = 'Disk ' . $row['id'] . ' for ' . $iCarName;

                        $params['oem']      = allTrim($row['val1']);
                        $params['diameter'] = allTrim($row['val2']);
                        $params['et_from']  = allTrim($row['val3']);
                        $params['et_to']    = allTrim($row['val4']);
                        $params['width']    = allTrim($row['val5']);
                    }

                    $arItemElement["PROPERTY_VALUES"] = $params;
                    $obElement->Add($arItemElement);
                    unset($arItemElement);
                }
            }

            $wizard->SetVar("step_migr_tires", ++$step_migr_tires);
            if (!$inprocess_migr_tires)
            {
                $wizard->SetVar("finish_migr_tires", true);
                $wizard->SetVar("finish", true);
            }
        }

        $this->content .= '</table>';
        $this->content .= "<script>SubmitForm('next');</script>";
    }

}
