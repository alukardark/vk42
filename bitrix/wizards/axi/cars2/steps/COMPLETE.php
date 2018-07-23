<?

class COMPLETE extends CFinishWizardStep
{

    function onPostForm()
    {
        
    }

    function InitStep()
    {
        $this->SetStepID("COMPLETE");
        $this->SetTitle(GetMessage("COMPLETE"));
    }

    function ShowStep()
    {
        $this->content .= '
            <div style="float: left; margin-top: 30px;">
            <a href="/bitrix/admin/wizard_list.php" class="">
            <span id="next-button-caption" class="wizard-next-button">' . GetMessage("GO_TO_SITE") . '</span>
            </a>
            </div>';
    }

}
