<?php
/**
 * @var $this TrialContext
 */
?>
<?php
$shortlisted_code = TrialPatientStatus::SHORTLISTED_CODE;
$accepted_code = TrialPatientStatus::ACCEPTED_CODE;
$rejected_code = TrialPatientStatus::REJECTED_CODE;
$shortlisted_status = TrialPatientStatus::model()->find('code = ?', array($shortlisted_code));
$accepted_status = TrialPatientStatus::model()->find('code = ?', array($accepted_code));
$rejected_status = TrialPatientStatus::model()->find('code = ?', array($rejected_code));
$current_patient_status_id = $this->isPatientInTrial() ? $this->getPatientTrialStatusId($this->patient->id, $this->trial->id) :'';
?>
<span class = "alert-box info js-show-shortlisted" style="<?= ($this->isPatientInTrial() && $current_patient_status_id == $shortlisted_status->id) ? '' : 'display:none;' ?>" >Shortlisted</span>

    <?php
    if($current_patient_status_id == $accepted_status->id){
        ?>
        <span class = "alert-box success" >Accepted</span>
        <?php
    }else if($current_patient_status_id == $rejected_status->id){
        ?>
        <span class = "alert-box error" >Rejected</span>
        <?php
    }
    ?>
</span>