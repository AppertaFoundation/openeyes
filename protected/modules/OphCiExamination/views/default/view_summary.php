<?php

use OEModule\OphCiExamination\models;

// The history element won't be displayed if it doesn't exist
$historyElement = $this->event->getElementByClass(models\Element_OphCiExamination_History::class);

// Find the elements for each tile, or create dummy elements so they will still render, but without any data
$pastSurgeryElement = $this->event->getElementByClass(models\PastSurgery::class) ?: new models\PastSurgery();
$systemicSurgeryElement = $this->event->getElementByClass(models\SystemicSurgery::class) ?: new models\SystemicSurgery();
$systemicDiagnosesElement = $this->event->getElementByClass(models\SystemicDiagnoses::class) ?: new models\SystemicDiagnoses();
$diagnosesElement = $this->event->getElementByClass(models\Element_OphCiExamination_Diagnoses::class) ?: new models\Element_OphCiExamination_Diagnoses();
$medicationsElement = $this->event->getElementByClass(models\HistoryMedications::class) ?: new models\HistoryMedications();
$familyHistoryElement = $this->event->getElementByClass(models\FamilyHistory::class) ?: new models\FamilyHistory();
$socialHistoryElement = $this->event->getElementByClass(models\SocialHistory::class) ?: new models\SocialHistory();
$managementElement = $this->event->getElementByClass(models\Element_OphCiExamination_Management::class) ?: new models\Element_OphCiExamination_Management();
$followupElement = $this->event->getElementByClass(models\Element_OphCiExamination_ClinicOutcome::class) ?: new models\Element_OphCiExamination_ClinicOutcome();


$medicationsWidget = $this->createWidget(
    $medicationsElement->widgetClass,
    array(
        'patient' => $this->patient,
        'element' => $medicationsElement,
        'mode' => $this->getElementWidgetMode($action),
    )
);
$medicationsWidget->setElementFromDefaults();

$current_filter = function ($e) {
    /** @var EventMedicationUse $e */
    return !$e->isStopped();
};

$stopped_filter = function ($e) {
    /** @var EventMedicationUse $e */
    return $e->isStopped() && !$e->isChangedMedication() && is_null($e->latest_med_use_id);
};

$medication_entries = EventMedicationUse::model()->findAll('event_id=?', [$this->event->id]);
$current_medication_entries = array_filter($medication_entries, $current_filter);
$current_medication_entries = $medicationsElement->filterHistoryAndManagementMedications($current_medication_entries);
$current_medication_entries = $medicationsElement->widget->sortEntriesByDate($current_medication_entries);

$stopped_medication_entries = array_filter($medication_entries, $stopped_filter);
$stopped_medication_entries = $medicationsElement->widget->sortEntriesByDate($stopped_medication_entries, false);
if ($historyElement) {
    $this->renderElement($historyElement, $action, $form, $data);
}
?>

<div class="element-tile-group" id="tile-group-exam-eyes" data-collapse="expanded">
    <?php $this->renderElement($diagnosesElement, $action, $form, $data) ?>
    <?php $this->renderElement($pastSurgeryElement, $action, $form, $data) ?>

    <section class="element view-Eye-Medications tile" data-element-type-id="<?php echo $medicationsElement->elementType->id ?>" data-element-type-class="<?php echo $medicationsElement->elementType->class_name ?>" data-element-type-name="Eye Medications" data-element-display-order="<?php echo $medicationsElement->elementType->display_order ?>">
        <header class=" element-header">
            <h3 class="element-title">Eye Medications</h3>
        </header>
        <div class="element-data">
            <?php
            $filter_eye_medication = function ($med) {
                return $med->laterality !== null;
            };
            $current_eye_medications = array_filter($current_medication_entries, $filter_eye_medication);
            $stopped_eye_medications = array_filter($stopped_medication_entries, $filter_eye_medication);
            ?>
            <?php if (!$current_eye_medications && !$stopped_eye_medications && !$medicationsElement->no_ophthalmic_medications_date) { ?>
                <div class="data-value not-recorded">
                    Nil recorded this examination
                </div>
            <?php } elseif (!$current_eye_medications && !$stopped_eye_medications && $medicationsElement->no_ophthalmic_medications_date) { ?>
                <div id="examination-no-eye-meds" class="data-value">
                    Patient takes no eye medications
                </div>
            <?php } else { ?>
                <?php if ($current_eye_medications) { ?>
                    <div class="data-value">
                        <div class="tile-data-overflow">
                            <table id="view-Eye-Medications-Current">
                                <colgroup>
                                    <col class="cols-7">
                                </colgroup>
                                <thead style="display:none;">
                                    <th>Drug</th>
                                    <th>Tooltip</th>
                                    <th>Laterality</th>
                                    <th>Date</th>
                                </thead>
                                <tbody>
                                <?php foreach ($current_eye_medications as $entry) { ?>
                                    <tr>
                                        <td>
                                            <?= $entry->getMedicationDisplay(true) ?>
                                            <?php $change_history = $entry->getChangeHistory();
                                            if (!empty($change_history)) {
                                                $tooltip_content = $entry->getChangeHistoryTooltipContent($change_history);
                                                ?>
                                                <i class="oe-i change small js-has-tooltip pad-right" data-tooltip-content="<?= $tooltip_content ?>"></i>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php
                                                $info_box = new MedicationInfoBox();
                                                $info_box->medication_id = $entry->medication->id;
                                                $info_box->init();

                                            $tooltip_content = $entry->getTooltipContent() . "<br />" . $info_box->getAppendLabel();
                                            if (!empty($tooltip_content)) { ?>
                                                <i class="oe-i <?=$info_box->getIcon();?> small js-has-tooltip"
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
                                        <td>
                                            <?= $entry->getStartDateDisplay() ?>
                                        </td>
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
                                    <table id="view-Eye-Medications-Stopped">
                                        <colgroup>
                                            <col class="cols-7">
                                        </colgroup>
                                        <thead style="display:none;">
                                            <th>Drug</th>
                                            <th>Tooltip</th>
                                            <th>Laterality</th>
                                            <th>Date</th>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($stopped_eye_medications as $entry) { ?>
                                                <tr>
                                                    <td>
                                                        <?= $entry->getMedicationDisplay(true) ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $info_box = new MedicationInfoBox();
                                                        $info_box->medication_id = $entry->medication->id;
                                                        $info_box->init();

                                                        $tooltip_content = $entry->getTooltipContent() . "<br />" . $info_box->getAppendLabel();
                                                        if ($tooltip_content) { ?>
                                                            <i class="oe-i <?= $info_box->getIcon(); ?> small js-has-tooltip" data-tooltip-content="<?= $tooltip_content ?>">
                                                            </i>
                                                        <?php } ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $laterality = $entry->getLateralityDisplay();
                                                        $this->widget('EyeLateralityWidget', array('laterality' => $laterality));
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?= $entry->getEndDateDisplay() ?>
                                                    </td>
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

<div class="element-tile-group" id="tile-group-exam-systemic" data-collapse="expanded">

    <?php $this->renderElement($systemicDiagnosesElement, $action, $form, $data) ?>

    <?php $this->renderElement($systemicSurgeryElement, $action, $form, $data) ?>

    <section class="element view-Systemic-Medications tile"
             data-element-type-id="<?php echo $medicationsElement->elementType->id ?>"
             data-element-type-class="<?php echo $medicationsElement->elementType->class_name ?>"
             data-element-type-name="Systemic Medications"
             data-element-display-order="<?php echo $medicationsElement->elementType->display_order + 1 ?>">
        <header class=" element-header">
            <h3 class="element-title">Systemic Medications</h3>
        </header>
        <div class="element-data">
            <div class="element-data">
                <?php

                $filterSystemicMedication = function ($med) {
                    return $med->laterality === null;
                };

                $current_systemic_medications = $medicationsElement ?
                    array_filter($current_medication_entries, $filterSystemicMedication) : [];
                $stopped_systemic_medications = $medicationsElement ?
                    array_filter($stopped_medication_entries, $filterSystemicMedication) : [];
                ?>
                <?php if (!$current_systemic_medications && !$stopped_systemic_medications && !$medicationsElement->no_systemic_medications_date) { ?>
                    <div class="data-value not-recorded">
                        Nil recorded this examination
                    </div>
                <?php } elseif (!$current_systemic_medications && !$stopped_systemic_medications && $medicationsElement->no_systemic_medications_date) { ?>
                    <div id="examination-no-systemic-meds" class="data-value">
                        Patient takes no systemic medications
                    </div>
                <?php } else { ?>
                    <?php if ($current_systemic_medications) { ?>
                <div class="data-value">
                    <div class="tile-data-overflow">
                        <table id="view-Systemic-Medications-Current">
                            <colgroup>
                                <col class="cols-7">
                                <col>
                            </colgroup>
                                <thead style="display:none;">
                                    <th>Drug</th>
                                    <th>Tooltip</th>
                                    <th></th>
                                    <th>Date</th>
                                </thead>
                            <tbody>
                            <?php foreach ($current_systemic_medications as $entry) { ?>
                                <tr>
                                    <td>
                                        <?php if (isset($patient) && $this->patient->hasDrugAllergy($entry->medication_id)) {
                                            echo '<i class="oe-i warning small pad js-has-tooltip js-allergy-warning" data-tooltip-content="Allergic to ' . implode(',', $patient->getPatientDrugAllergy($entry->medication_id)) . '"></i>';
                                        } ?>
                                        <?= $entry->getMedicationDisplay(true) ?>
                                        <?php $change_history = $entry->getChangeHistory();
                                        if (!empty($change_history)) {
                                            $tooltip_content = $entry->getChangeHistoryTooltipContent($change_history);
                                            ?>
                                            <i class="oe-i change small js-has-tooltip pad-right" data-tooltip-content="<?= $tooltip_content ?>"></i>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php
                                        $info_box = new MedicationInfoBox();
                                        $info_box->medication_id = $entry->medication->id;
                                        $info_box->init();

                                        $tooltip_content = $entry->getTooltipContent() . "<br />" . $info_box->getAppendLabel();
                                        if (!empty($tooltip_content)) { ?>
                                            <i class="oe-i <?=$info_box->getIcon();?> small-icon js-has-tooltip"
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
                                    <td>
                                        <?= $entry->getStartDateDisplay() ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                    <?php } else { ?>
            </div>
            <div class="data-value none">
                No current Systemic Medications
            </div>
                    <?php } ?>

                    <?php if ($stopped_systemic_medications) { ?>
            <div class="collapse-data">
                <div class="collapse-data-header-icon expand" data-blujay="0">
                    Stopped
                    <small>(<?= sizeof($stopped_systemic_medications) ?>)</small>
                </div>
                <div class="collapse-data-content">
                    <!-- <div class="restrict-data-shown"> -->
                    <div class="restrict-data-content rows-10">
                        <table id="view-Systemic-Medications-Stopped">
                            <colgroup>
                                <col class="cols-7">
                            </colgroup>
                            <thead style="display:none;">
                                <th>Drug</th>
                                <th>Tooltip</th>
                                <th>Laterality</th>
                                <th>Date</th>
                            </thead>
                            <tbody>
                                <?php foreach ($stopped_systemic_medications as $entry) { ?>
                                    <tr>
                                        <td>

                                            <?= $entry->getMedicationDisplay(true) ?>
                                        </td>
                                        <td>
                                            <?php
                                            $info_box = new MedicationInfoBox();
                                            $info_box->medication_id = $entry->medication->id;
                                            $info_box->init();

                                            $tooltip_content = $entry->getTooltipContent() . "<br />" . $info_box->getAppendLabel();
                                            if (!empty($tooltip_content)) { ?>
                                                <i class="oe-i <?= $info_box->getIcon(); ?> small js-has-tooltip" data-tooltip-content="<?= $tooltip_content ?>">
                                                </i>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php
                                            $laterality = $entry->getLateralityDisplay();
                                            $this->widget('EyeLateralityWidget', array('laterality' => $laterality));
                                            ?>
                                        </td>
                                        <td>
                                            <?= $entry->getEndDateDisplay() ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <!-- </div> -->
                    </div>
                </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </section>

    <div class="collapse-tile-group">
        <i class="oe-i medium reduce-height js-tiles-collapse-btn" data-group="tile-group-exam-eyes"></i>
    </div>
</div>

<div class="element-tile-group" id="tile-group-exam-patient" data-collapse="expanded">
    <section class="element tile">
        <header class="element-header">
            <h3 class="element-title">Family Social</h3>
        </header>
        <div class="element-data full-width">
            <?php $entries = array_merge($familyHistoryElement->entries, $socialHistoryElement->getDisplayAllEntries());
            if (!$entries) { ?>
                <div class="data-value not-recorded">
                    Nil recorded this examination
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

    <section class="element tile">
        <header class="element-header">
            <h3 class="element-title">Management</h3>
        </header>
        <div class="element-data full-width">
            <?php if (!$managementElement->comments) { ?>
                <div class="data-value not-recorded">
                    Nil recorded this examination
                </div>
            <?php } else { ?>
                <div class="data-value">
                    <?= Yii::app()->format->Ntext($managementElement->comments) ?>
                </div>
            <?php } ?>
        </div>
    </section>

    <section class="element tile">
        <header class="element-header">
            <h3 class="element-title">Follow-up</h3>
        </header>
        <div class="element-data full-width">
            <?php if (!$followupElement->entries) { ?>
                <div class="data-value not-recorded">
                    Nil recorded this examination
                </div>
            <?php } else { ?>
                <div class="data-value restrict-data-shown">
                    <div class="tile-data-overflow restrict-data-content">
                        <table class="last-left">
                            <colgroup>
                                <col class="cols-2">
                            </colgroup>
                            <tbody>
                                <?php $row_count = 0;
                                $api = Yii::app()->moduleAPI->get('PatientTicketing');
                                $ticket = $api->getTicketForEvent($this->event);
                                $queue_set_service = Yii::app()->service->getService('PatientTicketing_QueueSet');
                                $ticket_entries = [];
                                $non_ticket_entries = [];
                                foreach ($followupElement->entries as $entry) {
                                    if ($entry->isPatientTicket() && $ticket) {
                                        $ticket_entries[] = $entry;
                                    } else {
                                        $non_ticket_entries[] = $entry;
                                    }
                                }
                                foreach ($non_ticket_entries as $entry) { ?>
                                    <tr>
                                        <td><?= $row_count === 0 ? '' : 'AND' ?></td>
                                        <?php $infos = $entry->getInfos(); ?>
                                        <td><?= $entry->getStatusLabel() ?> <span class="fade"><?= !empty($infos) ? '&nbsp;[ ' . $entry->getInfos() . ' ] ' : ''; ?></span></td>
                                    </tr>
                                    <?php $row_count++; ?>
                                <?php }
                                foreach ($ticket_entries as $entry) { ?>
                                    <tr>
                                        <td>VC</td>
                                        <td>
                                            <a href="#vc-clinic-outcome">
                                                <i class="oe-i direction-down-circle small pad-right"></i>
                                                <span class="oe-vc-mode in-element"><?= $queue_set_service->getQueueSetForQueue($ticket->current_queue->id)->name ?></span>
                                                <?php if ($ticket->priority) { ?>
                                                    <span class="highlighter <?= $ticket->priority->colour ?>"><?= $ticket->priority->name[0] //gets first letter of word
                                                    ?></span>
                                                <?php } ?>
                                            </a>
                                        </td>
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
