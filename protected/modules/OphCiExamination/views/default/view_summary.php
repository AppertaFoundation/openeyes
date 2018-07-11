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
$pastOphthalmicSurgeryElement = $this->event->getElementByClass(models\PastSurgery::class) ?: new models\PastSurgery();
$pastSystemicSurgeryElement = $this->event->getElementByClass(models\PastSurgery::class) ?: new models\PastSurgery();
$systemicDiagnosesElement = $this->event->getElementByClass(models\SystemicDiagnoses::class) ?: new models\SystemicDiagnoses();
$diagnosesElement = $this->event->getElementByClass(models\Element_OphCiExamination_Diagnoses::class) ?: new models\Element_OphCiExamination_Diagnoses();
$medicationsElement = $this->event->getElementByClass(models\HistoryMedications::class) ?: new models\HistoryMedications();
?>

<?php if ($historyElement): ?>
    <?php $this->renderElement($historyElement, $action, $form, $data) ?>
<?php endif; ?>

<div class="element-tile-group" id="tile-group-exam-eyes" data-collapse="expanded">
    <?php $this->renderElement($diagnosesElement, $action, $form, $data) ?>

    <?php
    $pastOphthalmicSurgeryElement->widgetClass = 'OEModule\OphCiExamination\widgets\PastOphthalmicSurgery';
    $pastOphthalmicSurgeryElement->getElementType()->name = 'Eye Procedures';
    $this->renderElement($pastOphthalmicSurgeryElement, $action, $form, $data);
    ?>

  <section class="element view-Eye-Medications tile"
           data-element-type-id="<?php echo $medicationsElement->elementType->id ?>"
           data-element-type-class="<?php echo $medicationsElement->elementType->class_name ?>"
           data-element-type-name="Eye Medications"
           data-element-display-order="<?php echo $medicationsElement->elementType->display_order ?>">
    <header class=" element-header">
      <h3 class="element-title">Eye Medications</h3>
    </header>
    <div class="element-data">
      <div class="data-value">
          <?php

          $filterEyeMedication = function ($med) {
              return $med['route_id'] == 1;
          };

          $currentEyeMedications = array_filter($medicationsElement->currentOrderedEntries, $filterEyeMedication);
          $StoppedEyeMedications = array_filter($medicationsElement->stoppedOrderedEntries, $filterEyeMedication);
          ?>
          <?php if (!$currentEyeMedications) { ?>
            No current medications.
              <?php if ($StoppedEyeMedications) { ?>
              <br/>
              Stopped Medications:
              <table>
                <colgroup>
                  <col>
                  <col width="55px">
                  <col width="85px">
                </colgroup>
                <tbody>
                <?php foreach ($StoppedEyeMedications as $entry) { ?>
                  <tr>
                    <td><?= $entry->getMedicationDisplay() ?></td>
                    <td><?php $laterality = $entry->getLateralityDisplay(); ?>
                      <span class="oe-eye-lat-icons">
                        <i class="oe-i laterality small <?php echo $laterality === 'R' || $laterality === 'B' ? 'R' : 'NA' ?>"></i>
                        <i class="oe-i laterality small <?php echo $laterality === 'L' || $laterality === 'B' ? 'L' : 'NA' ?>"></i>
                      </span>
                    </td>
                    <td><?= $entry->getStartDateDisplay() ?></td>
                  </tr>
                <?php } ?>
                </tbody>
              </table>
              <?php } ?>
          <?php } else { ?>
            <div class="tile-data-overflow">
              <table>
                <colgroup>
                  <col>
                  <col width="55px">
                  <col width="85px">
                </colgroup>
                <tbody>
                <?php foreach ($currentEyeMedications as $entry) { ?>
                  <tr>
                    <td><?= $entry->getMedicationDisplay() ?></td>
                    <td><?php $laterality = $entry->getLateralityDisplay(); ?>
                      <span class="oe-eye-lat-icons">
                        <i class="oe-i laterality small <?php echo $laterality === 'R' || $laterality === 'B' ? 'R' : 'NA' ?>"></i>
                        <i class="oe-i laterality small <?php echo $laterality === 'L' || $laterality === 'B' ? 'L' : 'NA' ?>"></i>
                      </span>
                    </td>
                    <td><?= $entry->getStartDateDisplay() ?></td>
                  </tr>
                <?php } ?>
                </tbody>
              </table>
            </div>
          <?php } ?>
      </div>
    </div>
  </section>


  <div class="collapse-tile-group">
    <i class="oe-i small collapse js-tiles-collapse-btn" data-group="tile-group-exam-eyes"></i>
  </div>
</div>


<div class="element-tile-group" id="tile-group-exam-eyes" data-collapse="expanded">

    <?php $this->renderElement($systemicDiagnosesElement, $action, $form, $data) ?>

    <?php
    $pastSystemicSurgeryElement->widgetClass = 'OEModule\OphCiExamination\widgets\PastSystemicSurgery';
    $pastSystemicSurgeryElement->getElementType()->name = 'Systemic Procedures';
    $this->renderElement($pastSystemicSurgeryElement, $action, $form, $data);
    ?>

  <section class=" element view-Systemic-Medications tile"
           data-element-type-id="<?php echo $medicationsElement->elementType->id ?>"
           data-element-type-class="<?php echo $medicationsElement->elementType->class_name ?>"
           data-element-type-name="Systemic Medications"
           data-element-display-order="<?php echo $medicationsElement->elementType->display_order + 1 ?>">
    <header class=" element-header">
      <h3 class="element-title">Systemic Medications</h3>
    </header>
    <div class="element-data">
      <div class="data-value">
          <?php if (!$medicationsElement || !$medicationsElement->orderedEntries) { ?>
            No current medications.
          <?php } else { ?>
            <div class="tile-data-overflow">
              <table>
                <colgroup>
                  <col class="cols-7">
                </colgroup>
                <tbody>
                <?php foreach ($medicationsElement->orderedEntries as $entry) {
                    if ($entry['route_id'] != 1) { ?>
                      <tr>
                        <td><?= $entry->getMedicationDisplay() ?></td>
                        <td><?= $entry->getStartDateDisplay() ?></td>
                      </tr>
                    <?php }
                } ?>
                </tbody>
              </table>
            </div>
          <?php } ?>
      </div>
    </div>
  </section>

  <div class="collapse-tile-group">
    <i class="oe-i small collapse js-tiles-collapse-btn" data-group="tile-group-exam-eyes"></i>
  </div>
</div>
