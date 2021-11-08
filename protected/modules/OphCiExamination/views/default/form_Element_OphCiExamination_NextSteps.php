<?php
$active_pathway = $this->patient->getClinicPathwayInProgress();
?>
<!-- we need to provide a value for the controller to pick up the element, and this element has no data in it -->
<input name="OEModule_OphCiExamination_models_Element_OphCiExamination_NextSteps[id]" type="hidden" value="<?= $element->id ?>">
<?php if ($active_pathway !== null) { ?>
<div class="element-fields flex-layout full-width">
    <div class="cols-full">
        <?php
        $this->renderPartial(
            '//patient/_patient_clinic_pathway',
            [
                'pathway' => $active_pathway,
                'display_wait_duration' => false,
                'editable' => true,
                'quick_preset_adder' => ['display' => true, 'label' => 'Next presets'],
            ]
        );
        ?>
    </div>
</div>
<?php } else { ?>
Patient has no active clinical pathway
<?php } ?>