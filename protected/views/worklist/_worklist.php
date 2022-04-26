<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>
<?php
/**
 * @var $worklist Worklist|Array
 */
if (empty($filter)) {
     $filter = new WorklistFilterQuery();
}

$data_provider = $this->manager->getPatientsForWorklistSQL($worklist, $filter);
$data_provider->pagination->pageVar = $filter->getCombineWorklistsStatus() ? 'page' : 'page' . $worklist->id;
// Get data so that pagination  works
$data_provider->getData();
$core_api = new CoreAPI();
$wl_attrs = array();

$institution = Institution::model()->getCurrent();
$selected_site_id = Yii::app()->session['selected_site_id'];

$display_primary_number_usage_code = Yii::app()->params['display_primary_number_usage_code'];

$primary_identifier_prompt = PatientIdentifierHelper::getIdentifierDefaultPromptForInstitution(
    $display_primary_number_usage_code,
    $institution->id,
    $selected_site_id
);

$exam_api = \Yii::app()->moduleAPI->get('OphCiExamination');
Yii::app()->clientScript->registerScriptFile(Yii::app()->assetManager->createUrl('js/OpenEyes.UI.TableController.js'), ClientScript::POS_END);

$is_printing = isset($is_printing) && ($is_printing === true);
if (!isset($coreapi)) {
    $coreapi = new CoreAPI();
}

$section_classes = $data_provider->itemCount === 0 ? 'oec-group no-patients' : 'oec-group';
$quick_filter_name = $filter->getQuickFilterTypeName();
?>
<?php if ($filter->getCombineWorklistsStatus()) : ?>
<section class="<?= $section_classes ?>" id="js-worklist-combined" data-title="Combined Worklists">
    <header>
        <h3><?= 'Combined worklists : ' . date('d M Y') ?></h3>
    </header>
<?php else : ?>
<section class="<?= $section_classes ?>" id="js-worklist-<?= $worklist->id ?>" data-id="<?= $worklist->id ?>" data-title="<?= $worklist->name ?>">
    <header>
        <h3><?= $worklist->name . ' : ' . $worklist->getDisplayDate() ?></h3>
    </header>
<?php endif; ?>
<?php if ($data_provider->itemCount === 0): ?>
    <div class="result-msg">
        <?= $filter->hasQuickFilter() ? "No matches for quick filter '" . $quick_filter_name . "'" : 'No patients found' ?>
    </div>
<?php else: ?>
    <table class="oec-patients">
        <thead>
        <tr>
            <th>Time</th>
            <th>Clinic</th>
            <th>Patient</th>
            <th><!-- quicklook --></th>
            <th>Pathway</th>
            <th>
                <!-- Select all patients in worklist -->
                <label class="patient-checkbox">
                    <input class="js-check-patient" value="all" type="checkbox"/>
                    <div class="checkbox-btn"></div>
                </label>
            </th>
            <th>
                <!-- Assignee -->
                <i class="oe-i person no-click small"></i>
            </th>
            <th>
                <!-- Priority/Risk -->
                <?php if ($filter->priorityIsUsed()) { ?>
                    <i class="oe-i circle-grey no-click small"></i>
                <?php } else { ?>
                    <i class="oe-i triangle-grey no-click small"></i>
                <?php } ?>
            </th>
            <th>
                <!-- Comments -->
                <i class="oe-i comments no-click small"></i>
            </th>
            <th>Total duration</th>
            <th>
                <!-- Pathway completion action icon -->
            </th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($data_provider->getData() as $wl_patient_data) :
            $wl_patient = WorklistPatient::model()->findByPk($wl_patient_data['id']);

            $num_visits = Yii::app()->db->createCommand()
                ->select('COUNT(*)')
                ->from('worklist_patient')
                ->where('patient_id = :patient_id AND worklist_id = :worklist_id')
                ->bindValues([':patient_id' => $wl_patient->patient_id, ':worklist_id' => $wl_patient->worklist_id])
                ->queryScalar();
            /** @var $wl_patient WorklistPatient */
            $hide_add_step_btn = $wl_patient->pathway && (int)$wl_patient->pathway->status === Pathway::STATUS_DONE ? 'style="display:none;"' : null;
            ?>
            <tr class="<?= $wl_patient->pathway ? $wl_patient->pathway->getStatusString() : 'later' ?>" data-timestamp="<?= time() ?>" id="js-pathway-<?= $wl_patient->id ?>"
                  data-status="<?= $wl_patient->pathway ? $wl_patient->pathway->getStatusString() : 'later' ?>">
                <td><?= $wl_patient->scheduledtime ?></td>
                <td>
                    <div class="list-name"><?= $wl_patient->worklist->name ?></div>
                    <div class="code"><?= (int)$num_visits === 1 ? 'First Attendance' : null ?></div>
                </td>
                <td>
                    <?php $this->renderPartial('application.widgets.views.PatientMeta', array('patient' => $wl_patient->patient, 'coreapi' => $coreapi, 'pathway' => $wl_patient->pathway)); ?>

                </td>
                <td id="oe-patient-details" class="js-oe-patient" data-patient-id="<?= $wl_patient->patient_id ?>">
                    <i class="eye-circle medium pad oe-i js-worklist-btn" onmouseenter="onMouseEnterPatientQuickOverview(this)" onmouseleave="hidePatientQuickOverview()" onclick="onClickPatientQuickOverview(this)" id="js-worklist-btn"></i>
                </td>
                <td class="js-pathway-container">
                    <!--Render full pathway in a separate view. -->
                    <?php $this->renderPartial(
                        '//worklist/_clinical_pathway',
                        ['visit' => $wl_patient]
                    ); ?>
                </td>
                <td>
                    <label class="patient-checkbox" <?=$hide_add_step_btn?>>
                        <input class="js-check-patient" value="<?= $wl_patient->id ?>" type="checkbox"/>
                        <div class="checkbox-btn"></div>
                    </label>
                </td>
                <td class="js-pathway-assignee" data-id="<?= $wl_patient->pathway->owner_id ?? null ?>">
                    <!-- Assignee -->
                    <?= $wl_patient->pathway && $wl_patient->pathway->owner ? $wl_patient->pathway->owner->getInitials() : null ?>
                </td>
                <td>
                    <!-- Priority/Risk -->
                    <?php
                    if ($filter->priorityIsUsed()) {
                        echo $exam_api->getLatestTriagePriority($wl_patient->patient, $wl_patient->worklist);
                    } else {
                        echo $exam_api->getLatestOutcomeRiskStatus($wl_patient->patient, $wl_patient->worklist);
                    }
                    ?>

                </td>
                <td>
                    <span class="oe-pathstep-btn buff comments <?= $wl_patient->pathway && $wl_patient->pathway->checkForComments() ? 'comments-added' : '' ?>"
                          data-worklist-patient-id="<?= $wl_patient->id?>"
                          data-pathway-id="<?= $wl_patient->pathway->id ?? null ?>"
                          data-patient-id="<?= $wl_patient->patient_id ?>"
                          data-visit-id="<?= $wl_patient->id?>"
                          data-pathstep-id="comment"
                          data-pathstep-type-id="">
                        <span class="step i-comments"></span>
                        <span class="info" style="display: none;"></span>
                    </span>
                </td>
                <td>
                    <div class="wait-duration<?= $wl_patient->pathway && (int)$wl_patient->pathway->status === Pathway::STATUS_DONE ? ' stopped' : ''?>">
                        <?php if ($wl_patient->pathway) {
                            echo $wl_patient->pathway->getTotalDurationHTML(true);
                        } elseif ($wl_patient->when) {
                            if ($this->worklist_patient->when instanceof DateTime) {
                                $start_time = $this->worklist_patient->when;
                            } else {
                                $start_time = DateTime::createFromFormat('Y-m-d H:i:s', $this->worklist_patient->when);
                            }
                            $end_time = new DateTime();
                            $wait_length = $start_time->diff($end_time);
                            if ($wait_length->h < 2) {
                                $wait_color = 'green';
                            } elseif ($wait_length->h < 3) {
                                $wait_color = 'yellow';
                            } elseif ($wait_length->h < 4) {
                                $wait_color = 'orange';
                            } else {
                                $wait_color = 'red';
                            }
                            $duration_graphic = '<svg class="duration-graphic ' . $wait_color . '" viewBox="0 0 48 12" height="12" width="48">
                                                <circle class="c0" cx="6" cy="6" r="6"></circle>
                                                <circle class="c1" cx="18" cy="6" r="6"></circle>
                                                <circle class="c2" cx="30" cy="6" r="6"></circle>
                                                <circle class="c3" cx="42" cy="6" r="6"></circle>
                                            </svg>';
                            // Show duration of the pathway
                            $duration_graphic .= '<div class="mins">';
                            if ((int)$this->status === self::STATUS_DONE) {
                                $duration_graphic .= $wait_length->format('%h:%I');
                            } else {
                                $duration_graphic .= '<small>' . $wait_length->format('%h:%I') . '</small>';
                            }
                            echo $duration_graphic .= '</div>';
                        } ?>
                    </div>
                </td>
                <td class="js-pathway-status">
                    <!-- Completion icon/actions -->
                    <?php
                    $class = 'oe-i pad js-has-tooltip ';
                    if ($wl_patient->pathway) {
                        switch ($wl_patient->pathway->status) {
                            case Pathway::STATUS_LATER:
                                $class .= 'no-permissions small-icon';
                                $tooltip_text = 'Pathway not started';
                                break;
                            case Pathway::STATUS_DISCHARGED:
                                $class .= 'save medium-icon js-pathway-complete';
                                $tooltip_text = 'Pathway completed';
                                break;
                            case Pathway::STATUS_DONE:
                                // Done.
                                $class .= 'undo medium-icon js-pathway-reactivate';
                                $tooltip_text = 'Re-activate pathway to add steps';
                                break;
                            default:
                                // Covers all 'active' statuses, including long-wait and break.
                                $class .= 'save-blue medium-icon js-pathway-finish';
                                $tooltip_text = 'Patient has left<br/>Quick complete pathway';
                                break;
                        }
                    } else {
                        $class .= 'no-permissions small-icon';
                        $tooltip_text = 'Pathway not started';
                    }
                    ?>
                    <i class="<?= $class ?>" data-tooltip-content="<?= $tooltip_text ?>" data-pathway-id="<?= $wl_patient->pathway->id ?? null ?>"></i>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="11">
                <?php $this->widget('LinkPager', ['pages' => $data_provider->getPagination()]); ?>
            </td>
        </tr>
        </tfoot>
    </table>
    <?php endif; ?>
    <div class="oec-clock">
        <!-- Use JS to bind this element to the bottom of the entry closest to the current time. -->
        <?= date('H:i') ?>
    </div>
</section>

<?php
    $assetManager = Yii::app()->getAssetManager();
    $widgetPath = $assetManager->publish('protected/widgets/js');
    Yii::app()->clientScript->registerScriptFile($widgetPath . '/PatientPanelPopupMulti.js');
?>
