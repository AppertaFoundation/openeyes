<?php
$active_pathway = $this->patient->getClinicPathwayInProgress();
$acceptable_wait_time = Pathway::getAcceptableWaitTime();

?>

<?php if ($active_pathway !== null) { ?>
<div class="element-fields flex-layout full-width ">
    <div class="cols-full">
        <?php
        $this->renderPartial(
            '//patient/_patient_clinic_pathway',
            [
                'pathway' => $active_pathway,
                'display_wait_duration' => false,
                'acceptable_wait_time' => $acceptable_wait_time,
                'editable' => false,
            ]
        );
        ?>
    </div>
</div>
<?php } else { ?>
Patient has no active clinical pathway
<?php } ?>