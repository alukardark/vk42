<?

class Info extends CWizardStep
{

    function InitStep()
    {
        $this->SetStepID("Info");
        $this->SetTitle("Описание");
        $this->SetNextStep("UploadFile");
        $this->SetNextCaption(GetMessage("NEXT"));
        //$this->SetPrevCaption(GetMessage("PREV"));
    }

    function ShowStep()
    {
        $this->content .= '<table>';
        //$this->content .= GetMessage("ABOUT_WIZARD");
        $this->content .= GetMessage("ABOUT_BASE");
        $this->content .= '</table>';
    }

}
