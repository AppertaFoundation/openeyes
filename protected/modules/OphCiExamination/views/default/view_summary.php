<?php

use OEModule\OphCiExamination\models;

// The history element won't be displayed if it doesn't exist
$historyElement = $this->event->getElementByClass(models\Element_OphCiExamination_History::class);

// Find the elements for each tile, or create dummy elements so they will still render, but without any data
$pastSurgeryElement = $this->event->getElementByClass(models\PastSurgery::class) ?: new models\PastSurgery();
$systemicDiagnosesElement = $this->event->getElementByClass(models\SystemicDiagnoses::class) ?: new models\SystemicDiagnoses();
$diagnosesElement = $this->event->getElementByClass(models\Element_OphCiExamination_Diagnoses::class) ?: new models\Element_OphCiExamination_Diagnoses();
$medicationsElement = $this->event->getElementByClass(models\HistoryMedications::class) ?: new models\HistoryMedications();
$familyHistoryElement = $this->event->getElementByClass(models\FamilyHistory::class) ?: new models\FamilyHistory();
$socialHistoryElement = $this->event->getElementByClass(models\SocialHistory::class) ?: new models\SocialHistory();

if ($historyElement) {
    $this->renderElement($historyElement, $action, $form, $data);
}
?>

<div class="element-tile-group" data-collapse="expanded">
    <?php $this->renderElement($diagnosesElement, $action, $form, $data) ?>
    <?php $this->renderElement($pastSurgeryElement, $action, $form, $data) ?>

    <section class="element view-Eye-Medications tile"
             data-element-type-id="<?php echo $medicationsElement->elementType->id ?>"
             data-element-type-class="<?php echo $medicationsElement->elementType->class_name ?>"
             data-element-type-name="Eye Medications"
             data-element-display-order="<?php echo $medicationsElement->elementType->display_order ?>">
        <header class=" element-header">
            <h3 class="element-title">Eye Medications</h3>
        </header>
        <div class="element-data">
            <?php
            $filter_eye_medication = function ($med) {
                return $med->laterality !== null;
            };
            $current_eye_medications = array_filter($medicationsElement->current_entries, $filter_eye_medication);
            $stopped_eye_medications = array_filter($medicationsElement->closed_entries, $filter_eye_medication);
            ?>
            <?php if (!$current_eye_medications && !$stopped_eye_medications) { ?>
                <div class="data-value not-recorded">
                    No medications recorded during this encounter
                </div>
            <?php } else { ?>
                <?php if ($current_eye_medications) { ?>
                  <div class="data-value">
                      <div class="tile-data-overflow">
                          <table>
                              <colgroup>
                                  <col class="cols-7">
                              </colgroup>
                              <tbody>
                              <?php foreach ($current_eye_medications as $entry) { ?>
                                  <tr>
                                      <td>
                                          <i class="oe-i start small pad-right"></i>
                                          <?= $entry->getMedicationDisplay() ?>
                                      </td>
                                      <td></td>
                                      <td>
                                        <?php $tooltip_content = $entry->getTooltipContent();
                                        if (!empty($tooltip_content)) { ?>
                                            <i class="oe-i info small js-has-tooltip"
                                               data-tooltip-content="<?= $tooltip_content ?>">
                                            </i>
                                        <?php } ?>
                                      </td>
                                      <td>
                                          <?php
                                            $laterality = $entry->getLateralityDisplay();
                                            $this->widget('EyeLateralityWidget', array('laterality' => $laterality));
                                            ?>
                                      </td>
                                      <td><?= $entry->getStartDateDisplay() ?></td>
                                  </tr>
                              <?php } ?>
                              </tbody>
                          </table>
                      </div>
                  </div>
                <?php } else { ?>
                <div class="data-value none">
                    No current Eye Medications
                </div>
                <?php } ?>
                <?php if ($stopped_eye_medications) { ?>
          <div class="collapse-data">
              <div class="collapse-data-header-icon expand">
                  Stopped
                  <small>(<?= sizeof($stopped_eye_medications) ?>)</small>
              </div>
              <div class="collapse-data-content">
                  <div class="restrict-data-shown">
                      <div class="restrict-data-content rows-10">
                          <table>
                              <colgroup>
                                  <col class="cols-7">
                              </colgroup>
                              <tbody>
                              <?php foreach ($stopped_eye_medications as $entry) { ?>
                                  <tr>
                                      <td>
                                          <i class="oe-i stop small pad-right"></i>
                                          <?= $entry->getMedicationDisplay() ?>
                                      </td>
                                      <td>
                                          <?php $tooltip_content = $entry->getTooltipContent();
                                            if ($tooltip_content) {?>
                                              <i class="oe-i info small js-has-tooltip"
                                                 data-tooltip-content="<?= $tooltip_content ?>"
                                              </i>
                                            <?php } ?>
                                      </td>
                                      <td>
                                          <?php
                                            $laterality = $entry->getLateralityDisplay();
                                            $this->widget('EyeLateralityWidget', array('laterality' => $laterality));
                                            ?>
                                      </td>
                                      <td><?= $entry->getStartDateDisplay() ?></td>
                                  </tr>
                              <?php } ?>
                              </tbody>
                          </table>
                      </div>
                  </diV>
              </div>
          </div>
                <?php } ?>
            <?php } ?>
        </div>
    </section>

    <div class="collapse-tile-group">
        <i class="oe-i medium reduce-height js-tiles-collapse-btn" data-group="tile-group-exam-eyes"></i>
    </div>
</div>

<div class="element-tile-group" data-collapse="expanded">

    <?php $this->renderElement($systemicDiagnosesElement, $action, $form, $data) ?>

    <section class="element tile view-family-social-history">
        <header class="element-header">
            <h3 class="element-title">Family-Social</h3>
        </header>
        <div class="element-data">
            <?php $entries = array_merge($familyHistoryElement->entries, $socialHistoryElement->getDisplayAllEntries());
            if (!$entries) { ?>
                <div class="data-value not-recorded">
                    No family or social history recorded during this encounter
                </div>
            <?php } else { ?>
                <div class="data-value">
                    <div class="tile-data-overflow">
                        <table class="last-left">
                            <tbody>
                            <?php foreach ($entries as $entry) { ?>
                                <tr>
                                    <td><?= $entry ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php } ?>
        </div>
    </section>

    <section class=" element view-Systemic-Medications tile"
             data-element-type-id="<?php echo $medicationsElement->elementType->id ?>"
             data-element-type-class="<?php echo $medicationsElement->elementType->class_name ?>"
             data-element-type-name="Systemic Medications"
             data-element-display-order="<?php echo $medicationsElement->elementType->display_order + 1 ?>">
        <header class=" element-header">
            <h3 class="element-title">Systemic Medications</h3>
        </header>
        <div class="element-data">
            <?php

            $filterSystemicMedication = function ($med) {
                return $med->laterality === null;
            };

            $current_systemic_medications = $medicationsElement ?
                array_filter($medicationsElement->current_entries, $filterSystemicMedication) : [];
            $stopped_systemic_medications = $medicationsElement ?
                array_filter($medicationsElement->closed_entries, $filterSystemicMedication) : [];
            ?>
            <?php if (!$current_systemic_medications && !$stopped_systemic_medications) { ?>
                <div class="data-value not-recorded">
                    No medications recorded during this encounter
                </div>
            <?php } else { ?>
                <?php if ($current_systemic_medications) { ?>
                    <div class="data-value">
                        <div class="tile-data-overflow">
                            <table id="view-Systemic-Medications-Current">
                                <colgroup>
                                    <col class="cols-8">
                                    <col>
                                </colgroup>
                                <tbody>
                                <?php foreach ($current_systemic_medications as $entry) { ?>
                                    <tr>
                                        <td class="nowrap">
                                            <i class="oe-i start small pad"></i>
                                            <?php if (isset($patient) && $this->patient->hasDrugAllergy($entry->medication_id)) {
                                                echo '<i class="oe-i warning small pad js-has-tooltip js-allergy-warning" data-tooltip-content="Allergic to ' . implode(',', $patient->getPatientDrugAllergy($entry->medication_id)) . '"></i>';
                                            } ?>
                                            <?= $entry->getMedicationDisplay() ?>
                                        </td>
                                        <td>
                                            <?php
                                            $tooltip_content = $entry->getTooltipContent();
                                            if (!empty($tooltip_content)) { ?>
                                                <i class="oe-i info small-icon js-has-tooltip"
                                                   data-tooltip-content="<?= $tooltip_content ?>">
                                                </i>
                                            <?php } ?>
                                        </td>
                                        <td><?= $entry->getStartDateDisplay() ?></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php } else { ?>
                <div class="data-value none">
                    No current Systemic Medications
                </div>
                <?php } ?>

                <?php if ($stopped_systemic_medications) { ?>
                <div class="collapse-data">
                    <div class="collapse-data-header-icon expand">
                        Stopped
                        <small>(<?= sizeof($stopped_systemic_medications) ?>)</small>
                    </div>
                    <div class="collapse-data-content">
                        <div class="restrict-data-shown">
                            <div class="restrict-data-content rows-10">
                                <table>
                                    <colgroup>
                                        <col class="cols-7">
                                    </colgroup>
                                    <tbody>
                                    <?php foreach ($stopped_systemic_medications as $entry) { ?>
                                        <tr>
                                            <td>
                                                <i class="oe-i stop small pad"></i>
                                                <?= $entry->getMedicationDisplay() ?>
                                            </td>
                                            <td>
                                                <?php $tooltip_content = $entry->getTooltipContent();
                                                if (!empty($tooltip_content)) {?>
                                                    <i class="oe-i info small js-has-tooltip"
                                                       data-tooltip-content="<?= $tooltip_content ?>">
                                                    </i>
                                                <?php } ?>
                                            </td>
                                            <td><?= $entry->getStartDateDisplay() ?></td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </section>

    <div class="collapse-tile-group">
        <i class="oe-i medium reduce-height js-tiles-collapse-btn" data-group="tile-group-exam-eyes"></i>
    </div>
</div>
