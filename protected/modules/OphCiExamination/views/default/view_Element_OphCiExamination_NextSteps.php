<?php
$active_pathway = $this->patient->getClinicPathwayInProgress();
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
                'editable' => false,
            ]
        );
        ?>
    </div>
</div>
<?php } else { ?>
Patient has no active clinical pathway
<?php } ?>