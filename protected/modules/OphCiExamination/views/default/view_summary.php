<?php

use OEModule\OphCiExamination\models;

/**
 * @var \OEModule\OphCiExamination\controllers\DefaultController $this
 * @var string $action
 * @var BaseEventTypeCActiveForm $form
 * @var array $data
 */

// The history element won't be displayed if it doesn't exist
$historyElement = $this->event->getElementByClass(models\Element_OphCiExamination_History::class);

// Find the elements for each tile, or create dummy elements so they will still render, but without any data
$pastSurgeryElement = $this->event->getElementByClass(models\PastSurgery::class) ?: new models\PastSurgery();
$systemicDiagnosesElement = $this->event->getElementByClass(models\SystemicDiagnoses::class) ?: new models\SystemicDiagnoses();
$diagnosesElement = $this->event->getElementByClass(models\Element_OphCiExamination_Diagnoses::class) ?: new models\Element_OphCiExamination_Diagnoses();
$medicationsElement = $this->event->getElementByClass(models\HistoryMedications::class) ?: new models\HistoryMedications();
$familyHistoryElement = $this->event->getElementByClass(models\FamilyHistory::class) ?: new models\FamilyHistory();
$socialHistoryElement = $this->event->getElementByClass(models\SocialHistory::class) ?: new models\SocialHistory();
?>


<?php if ($historyElement) : ?>
    <?php $this->renderElement($historyElement, $action, $form, $data) ?>
<?php endif; ?>

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
          $filterEyeMedication = function ($med) {
              return $med->laterality !== null;
          };
          $currentEyeMedications = array_filter($medicationsElement->current_entries, $filterEyeMedication);
          $stoppedEyeMedications = array_filter($medicationsElement->closed_entries, $filterEyeMedication);
          ?>
          <?php if (!$currentEyeMedications && !$stoppedEyeMedications) { ?>

              <div class="data-value not-recorded">
                  No medications recorded during this encounter
              </div>
            <?php } else { ?>
              <div class="data-value">
                  <div class="tile-data-overflow">
                      <table>
                          <colgroup>
                              <col class="cols-7">
                          </colgroup>
                          <tbody>
                          <?php foreach ($currentEyeMedications as $entry) { ?>
                              <tr>
                                  <td>
                                      <?php $this->widget('MedicationInfoBox', array('medication_id' => $entry->medication_id)); ?>
                                      <?= $entry->getMedicationDisplay() ?>
                                  </td>
                                  <td>
                                      <?php
                                        $laterality = $entry->getLateralityDisplay();
                                        $this->widget('EyeLateralityWidget', array('laterality' => $laterality));
                                        ?>
                                  </td>
                                  <td>
                                      <?php if ($entry->getDoseAndFrequency()) {?>
                                          <i class="oe-i info small pro-theme js-has-tooltip"
                                             data-tooltip-content="<?= $entry->getDoseAndFrequency() ?>"
                                          </i>
                                        <?php } ?>
                                  </td>
                                  <td><?= $entry->getStartDateDisplay() ?></td>
                              </tr>
                            <?php } ?>
                          </tbody>
                      </table>
                  </div>

                  <table>
                      <colgroup>
                          <col class="cols-7">
                      </colgroup>
                      <thead>
                      <tr>
                          <th>Stopped</th>
                          <th></th>
                          <th></th>
                          <th><i class="oe-i small pro-theme js-patient-expand-btn pad expand"></i></th>
                      </tr>
                      </thead>
                      <tbody style="display: none;">
                      <?php foreach ($stoppedEyeMedications as $entry) { ?>
                          <tr>
                              <td>
                                  <?php $this->widget('MedicationInfoBox', array('medication_id' => $entry->medication_id)); ?>
                                  <?= $entry->getMedicationDisplay() ?>
                              </td>
                              <td><?php
                                  $laterality = $entry->getLateralityDisplay();
                                  $this->widget('EyeLateralityWidget', array('laterality' => $laterality));
                                ?>
                              </td>
                              <td>
                                  <?php if ($entry->getDoseAndFrequency()) {?>
                                      <i class="oe-i info small pro-theme js-has-tooltip"
                                         data-tooltip-content="<?= $entry->getDoseAndFrequency() ?>"
                                      </i>
                                    <?php } ?>
                              </td>
                              <td><?= $entry->getStartDateDisplay() ?></td>
                          </tr>
                        <?php } ?>
                      </tbody>
                  </table>
              </div>
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

        $currentSystemicMedications = $medicationsElement ?
            array_filter($medicationsElement->current_entries, $filterSystemicMedication) : [];
        $stoppedSystemicMedications = $medicationsElement ?
            array_filter($medicationsElement->closed_entries, $filterSystemicMedication) : [];
        ?>
        <?php if (!$currentSystemicMedications && !$stoppedSystemicMedications) { ?>
            <div class="data-value not-recorded">
              No medications recorded during this encounter
            </div>
        <?php } else { ?>
                <div class="element-data">
                        <div class="data-value">
                            <div class="tile-data-overflow">
                                <table>
                                    <colgroup>
                                        <col class="cols-7">
                                    </colgroup>
                                    <tbody>
                                    <?php foreach ($currentSystemicMedications as $entry) { ?>
                                        <tr>
                                            <td><?= $entry->getMedicationDisplay() ?></td>
                                            <td>
                                                <?php if ($entry->getDoseAndFrequency()) {?>
                                                    <i class="oe-i info small pro-theme js-has-tooltip"
                                                       data-tooltip-content="<?= $entry->getDoseAndFrequency() ?>"
                                                    </i>
                                                <?php } ?>
                                            </td>
                                            <td><?= $entry->getStartDateDisplay() ?></td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>

                            <table>
                                <colgroup>
                                    <col class="cols-7">
                                </colgroup>
                                <thead>
                                <tr>
                                    <th>Stopped</th>
                                    <th></th>
                                    <th></th>
                                    <th><i class="oe-i small pro-theme js-patient-expand-btn pad expand"></i></th>
                                </tr>
                                </thead>
                                <tbody style="display: none;">
                                <?php foreach ($stoppedSystemicMedications as $entry) { ?>
                                    <tr>
                                        <td><?= $entry->getMedicationDisplay() ?></td>
                                        <td>
                                            <?php if ($entry->getDoseAndFrequency()) {?>
                                                <i class="oe-i info small pro-theme js-has-tooltip"
                                                   data-tooltip-content="<?= $entry->getDoseAndFrequency() ?>"
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
        <?php } ?>
    </div>
  </section>

  <div class="collapse-tile-group">
    <i class="oe-i medium reduce-height js-tiles-collapse-btn" data-group="tile-group-exam-eyes"></i>
  </div>
</div>
