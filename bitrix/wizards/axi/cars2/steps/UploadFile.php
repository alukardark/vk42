<?

class UploadFile extends CWizardStep
{

    function InitStep()
    {
        $this->SetStepID("UploadFile");
        $this->SetTitle("Загрузка файлов");
        $this->SetNextStep("MigrationConfig");
        //$this->SetPrevStep("Info");
        $this->SetNextCaption("Загрузить");
        //$this->SetPrevCaption(GetMessage("PREV"));
    }

    function ShowStep()
    {
        $this->content .= '<table>';

        $this->content .= "<tr><td>Файл auto_parents.sql: </td><td>" . $this->ShowFileField("auto_parents", Array()) . "<td></tr>";
        $this->content .= "<tr><td>Файл auto_children.sql: </td><td>" . $this->ShowFileField("auto_children", Array()) . "<td></tr>";
        $this->content .= "<tr><td>Файл auto_context.sql: </td><td>" . $this->ShowFileField("auto_context", Array()) . "<td></tr>";

        //$this->content .= "<tr><td><br/><b>Внимание! Не нажимайте кнопку «Загрузить» дважды.</b><td></tr>";

        $this->content .= '</table>';
    }

    function onPostForm()
    {
        $wizard  = &$this->GetWizard();
        $wizard->SetVar("file_uploaded", false);
        $bErrors = false;

        $resSavefile1 = $this->SaveFile("auto_parents", Array("extensions" => "sql"));
        $resSavefile2 = $this->SaveFile("auto_children", Array("extensions" => "sql"));
        $resSavefile3 = $this->SaveFile("auto_context", Array("extensions" => "sql"));

        if (!intval($resSavefile1) || !intval($resSavefile2) || !intval($resSavefile3))
        {
            $bErrors = true;
        }

        if (!$bErrors)
        {
            $auto_parents_fileID  = $wizard->GetVar("auto_parents");
            $auto_children_fileID = $wizard->GetVar("auto_children");
            $auto_context_fileID  = $wizard->GetVar("auto_context");

            $file_auto_parents  = $_SERVER['DOCUMENT_ROOT'] . CFile::GetPath($auto_parents_fileID);
            $file_auto_children = $_SERVER['DOCUMENT_ROOT'] . CFile::GetPath($auto_children_fileID);
            $file_auto_context  = $_SERVER['DOCUMENT_ROOT'] . CFile::GetPath($auto_context_fileID);
        }

        if (!$bErrors && file_exists($file_auto_parents))
        {
            SplitSQL($file_auto_parents);
        }
        else
        {
            $bErrors = true;
        }

        if (!$bErrors && file_exists($file_auto_children))
        {
            SplitSQL($file_auto_children);
        }
        else
        {
            $bErrors = true;
        }

        if (!$bErrors && file_exists($file_auto_context))
        {
            SplitSQL($file_auto_context);
        }
        else
        {
            $bErrors = true;
        }

        if (!$bErrors)
        {
            $wizard->SetVar("file_uploaded", true);
        }
    }

}
