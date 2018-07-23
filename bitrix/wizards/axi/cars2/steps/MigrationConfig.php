<?

class MigrationConfig extends CWizardStep
{

    function onPostForm()
    {
        
    }

    function InitStep()
    {
        $this->SetStepID("MigrationConfig");
        $this->SetTitle("Подготовка импорта");
        $this->SetNextStep("MigrationStart");
        $this->SetNextCaption("Импортировать");
    }

    function ShowStep()
    {
        $this->content .= '<table>';
        $wizard        = &$this->GetWizard();
        $fileUploaded  = $wizard->GetVar("file_uploaded");

        if ($fileUploaded)
        {
            //собираем данные для конвертации
            global $DB;

            $onStepDel = 25; //кол-во элементов за один шаг
            $onStep = 100; //кол-во элементов за один шаг

            $count_cars     = getCountElements(TX_CARS_IB);
            $steps_del_cars = ceil($count_cars / $onStepDel); //шагов очистки ИБ автомобилей

            $count_tires     = getCountElements(TX_TYRES_IB);
            $steps_del_tires = ceil($count_tires / $onStepDel); //шагов очистки ИБ шин


            $res = $DB->Query("SELECT count(*) as steps FROM auto_parents where level=2");
            if ($row = $res->Fetch())
            {
                $steps_migr_auto = $row['steps']; //шагов конвертации автомобилей
            }

            $res2 = $DB->Query("SELECT count(*) as items FROM auto_context where type=1 or type=3");
            if ($row2 = $res2->Fetch())
            {
                $steps_migr_tires = ceil($row2['items'] / $onStep); //шагов конвертации шин
            }

            $wizard->SetVar("onStep", $onStep);
            $wizard->SetVar("onStepDel", $onStepDel);
            $wizard->SetVar("finish", false);

            $wizard->SetVar("steps_del_cars", $steps_del_cars);
            $wizard->SetVar("steps_del_tires", $steps_del_tires);
            $wizard->SetVar("steps_migr_auto", $steps_migr_auto);
            $wizard->SetVar("steps_migr_tires", $steps_migr_tires);

            $wizard->SetVar("step_del_cars", 0);
            $wizard->SetVar("step_del_tires", 0);
            $wizard->SetVar("step_migr_auto", 0);
            $wizard->SetVar("step_migr_tires", 0);

            $wizard->SetVar("finish_del_cars", $count_cars > 0 ? false : true);
            $wizard->SetVar("finish_del_tires", $count_tires > 0 ? false : true);
            $wizard->SetVar("finish_migr_auto", false);
            $wizard->SetVar("finish_migr_tires", false);

            $this->content .= '<b style="color: green">' . "Файлы успешно загружены" . '</b><br/>';
            $this->content .= "<tr><td><br/><b style=\"color: red\">Импорт может занять длительное время (1-2 часа), "
                    . "втечение которого сайт может работать медленнее обычного."
                    . "<br/> Фильтр по автомобилям во время импорта работать не будет, поэтому запускайте процесс "
                    . " в период наименьшей посещаемости сайта (например ночью).<br/>"
                    . " <b>Не нажимайте кнопку «Импортировать» дважды и не прерывайте процесс.</b></b><td></tr>";
        }
        else
        {
            $this->content .= '<b style="color: red">' . "Ошибка загрузки файла" . '</b><br/>';
        }

        $this->content .= '</table>';
    }

}
