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

$data_provider = $this->manager->getPatientsForWorklist($worklist);
$data_provider->pagination->pageVar = 'page' . $worklist->id;
// Get data so that pagination  works
$data_provider->getData();
$core_api = new CoreAPI();

$institution = Institution::model()->getCurrent();
$selected_site_id = Yii::app()->session['selected_site_id'];
$display_primary_number_usage_code = Yii::app()->params['display_primary_number_usage_code'];

$primary_identifier_prompt = PatientIdentifierHelper::getIdentifierDefaultPromptForInstitution(
    $display_primary_number_usage_code,
    $institution->id ,
    $selected_site_id);

Yii::app()->clientScript->registerScriptFile(Yii::app()->assetManager->createUrl('js/OpenEyes.UI.TableController.js'), ClientScript::POS_END);

$is_printing = isset($is_printing) && ($is_printing === true);
?>

<div class="worklist-group js-filter-group" id="js-worklist-<?= $worklist->id ?>-wrapper">
    <div class="worklist-summary flex-layout">
        <h2 id="worklist_<?= $worklist->id ?>"><?= $worklist->name ?> : <?= $worklist->getDisplayDate() ?></h2>
    </div>

    <?php if ($data_provider->totalItemCount <= 0) : ?>
        <div class="alert-box info">
            No patients in this worklist.
        </div>
    <?php else : ?>
    <table id="js-worklist-<?=$worklist->id?>" class="standard highlight-rows last-right js-table-controller <?=$is_printing?"allow-page-break":""?>">
            <colgroup>
                <col class="cols-1"><!--Time-->
                <col class="cols-1"><!--Hos Num-->
            <col class="cols-1"><!--All in one icon-->
                <col class="cols-3"><!--Name-->
                <!--spacing, this will be removed and PSD will be placed here, probably with auto widths for reset -->
                <?php for ($i = 1; $i <= 2 - count($worklist->displayed_mapping_attributes); $i++) : ?>
                    <col class="cols-2">
                <?php endfor; ?>
                <col class="cols-1"><!--Gender-->
                <col class="cols-2"><!--DOB-->
            </colgroup>
            <thead>
            <tr>
                <!--<th>
                    <label class="inline highlight ">
                        <input value="All" name="work-ls-patient-all" type="checkbox"> All
                    </label>
                </th>-->
                <th>Time</th>
                <th class="nowrap"><?= $primary_identifier_prompt ?></th>
                <!--<th class="nowrap">P1-3</th>-->
            <!--all in one icon-->
            <th></th>
                <th>Name</th>
                <?php for ($i = 1; $i <= 2 - count($worklist->displayed_mapping_attributes); $i++) : ?>
                    <th></th>
                <?php endfor; ?>
                <th>Gender</th>
                <th>DoB</th>
                <?php foreach ($worklist->displayed_mapping_attributes as $attr) : ?>
                    <th><?= $attr->name; ?></th>
                <?php endforeach; ?>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($data_provider->getData() as $wl_patient) : ?>
                <?php $link = $core_api->generatePatientLandingPageLink($wl_patient->patient, ['worklist_patient_id' => $wl_patient->id]); ?>
                <tr data-url="<?= $link; ?>" style="cursor:pointer">
                    <?php /*<!--PSD--><td><label class="highlight"><input value="<?=$wl_patient->id;?>" name="worklist_patient[]" type="checkbox"></label></td>*/ ?>
                    <td><?= $wl_patient->scheduledtime; ?></td>
                    <td class="nowrap">
                        <?php $primary_identifier = PatientIdentifierHelper::getIdentifierForPatient($display_primary_number_usage_code, $wl_patient->patient->id, $institution->id, $selected_site_id); ?>

                        <?= PatientIdentifierHelper::getIdentifierValue($primary_identifier);
                        $this->widget(
                            'application.widgets.PatientIdentifiers',
                            [
                                'patient' => $wl_patient->patient,
                                'show_all' => true,
                                'tooltip_size' => 'small'
                            ]); ?> </td>
                    <?php /*<!--PSD--><td><i class="oe-i triangle-amber js-has-tooltip" data-tooltip-content="Patient Risk: 2 (Medium).<br>Reversible harm from delayed appointment. <br>Previous Cancelled: 0"></i></td>*/ ?>
                    <td id="oe-patient-details" class="js-oe-patient" data-patient-id="<?= $wl_patient->patient->id ?>">
                        <i class="js-patient-quick-overview eye-circle medium pad  oe-i js-worklist-btn" id="js-worklist-btn"></i>
                    </td>
                    <td>
                        <?php if (!$is_printing) :
                        ?><a href="<?= $link; ?>"><?php
                            endif; ?>
                            <?= $wl_patient->patient->getHSCICName(); ?>
                            <?php if (!$is_printing) :
                            ?></a><?php
                    endif; ?>
                    </td>

                    <?php for ($i = 1; $i <= 2 - count($worklist->displayed_mapping_attributes); $i++) : ?>
                        <?php /*visibility: hidden; to keep the nice spacing, when PSD will be ready everything will be clean and shiny */ ?>
                        <td></td>
                    <?php endfor; ?>
                    <?php /*<!--PSD--><td><span class="oe-pathstep-btn " data-step="{&quot;title&quot;:&quot;PSD: Custom&quot;,&quot;status&quot;:&quot;todo&quot;,&quot;php&quot;:&quot;PSD-popups/todo.php&quot;}">PSD</span></td>*/ ?>
                    <td><?= $wl_patient->patient->genderString ?></td>
                    <td><?= '<span class="oe-date">' . Helper::convertDate2Html(Helper::convertMySQL2NHS($wl_patient->patient->dob)) . '</span>' ?></td>

                    <?php foreach ($worklist->displayed_mapping_attributes as $attr) : ?>
                        <td><?= $wl_patient->getWorklistAttributeValue($attr); ?></td>
                    <?php endforeach; ?>
                    <td><a href="<?= $link ?>"><i class="oe-i direction-right-circle medium pad"></i></a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot <?= $is_printing ? "class=\"hidden\"" : "" ?>>
            <tr>
                <td colspan="8">
                    <?php $this->widget('LinkPager', ['pages' => $data_provider->getPagination()]); ?>
                </td>
            </tr>
            </tfoot>
        </table>
    <?php endif; ?>
</div>

<?php
    $assetManager = Yii::app()->getAssetManager();
    $widgetPath = $assetManager->publish('protected/widgets/js');
    Yii::app()->clientScript->registerScriptFile($widgetPath . '/PatientPanelPopupMulti.js');
?>

<script type="text/javascript">
    $(document).ready(function () {
        $.ajax({
            type: "POST",
            url: "/worklist/renderPopups",
            data: {
                "worklistId" : (<?= $worklist->id?>),
                YII_CSRF_TOKEN: YII_CSRF_TOKEN
            },
            success: function (resp) {
                $("body.open-eyes.oe-grid").append(resp);
            }
        })
    })
    $('body').on('click', '.collapse-data-header-icon', function () {
        $(this).toggleClass('collapse expand');
        $(this).next('div').toggle();
    });
</script>
