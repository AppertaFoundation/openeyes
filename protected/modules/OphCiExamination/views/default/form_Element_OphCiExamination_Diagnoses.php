<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php
$js_path = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.assets.js') . '/OpenEyes.UI.DiagnosesSearch.js',
    false, -1);
Yii::app()->clientScript->registerScriptFile("{$this->assetPath}/js/Diagnoses.js", CClientScript::POS_HEAD);

$firm = Firm::model()->with(array(
    'serviceSubspecialtyAssignment' => array('subspecialty'),
))->findByPk(Yii::app()->session['selected_firm_id']);

$current_episode = Episode::getCurrentEpisodeByFirm($this->patient->id, $firm);

$other_episode_ids = array_map(function ($episodes) {
    return $episodes->id;
}, $this->patient->episodes);

unset($other_episode_ids[$current_episode->id]);

$read_only_diagnoses = [];
foreach ($this->patient->episodes as $ep) {
    $diagnosis = $ep->diagnosis; // Disorder model
    if ($diagnosis && $diagnosis->specialty && $diagnosis->specialty->code == 130 && $ep->id != $current_episode->id) {
        $read_only_diagnoses[] = [
            'diagnosis' => $diagnosis,
            'eye' => Eye::methodPostFix($ep->eye_id),
            'date' => $ep->disorder_date,
            'subspecialty' => $ep->getSubspecialtyText(),
        ];
    }
}
?>

<script type="text/javascript" src="<?= $js_path; ?>"></script>

<?php $model_name = CHtml::modelName($element); ?>

<div class="element-fields flex-layout full-width" id="<?= CHtml::modelName($element); ?>_element">
    <input type="hidden" name="<?php echo CHtml::modelName($element); ?>[force_validation]"/>

    <input type="hidden" name="<?= $model_name ?>[present]" value="1"/>

    <table id="<?= $model_name ?>_diagnoses_table" class="cols-10">
        <thead>
        <tr>
            <th>Diagnosis</th>
            <th>Eye</th>
            <th>Principal</th>
            <th>Date (optional)</th>
            <th></th>
        </tr>
        </thead>
        <tbody id="OphCiExamination_diagnoses" class="js-diagnoses">
        <?php

        $row_count = 0; //in case $read_only_diagnoses is an empty array
        foreach ($read_only_diagnoses as $row_count => $values) {
            $this->renderPartial('DiagnosesEntry_event_edit_readonly', ['values' => $values]);
        }

        $row = $row_count + 1;
        foreach ($element->diagnoses as $row_count => $diagnosis) {

            $row_count = $row + $row_count;
            $this->renderPartial(
                'DiagnosesEntry_event_edit',
                array(
                    'diagnosis' => $diagnosis,
                    'event_date' => isset($element->event) ? $element->event->event_date : null,
                    'model_name' => CHtml::modelName($element),
                    'row_count' => $row_count,
                    'field_prefix' => $model_name . "[entries][$row_count]",
                    'removable' => true,
                )
            );
        }
        ?>
        </tbody>
    </table>

    <div class="add-data-actions flex-item-bottom" id="ophthalmic-diagnoses-popup">
        <button class="button hint green add-entry" type="button" id="add-ophthalmic-diagnoses">
            <i class="oe-i plus pro-theme"></i>
        </button>
        <div id="add-to-ophthalmic-diagnoses" class="oe-add-select-search" style="display: none;">
            <!-- icon btns -->
            <div class="close-icon-btn" type="button"><i class="oe-i remove-circle medium"></i></div>
            <div class="select-icon-btn" type="button"><i id="ophthalmic-diagnoses-select-btn" class="oe-i menu"></i></div>
            <button class="button hint green add-icon-btn" type="button">
                <i class="oe-i plus pro-theme"></i>
            </button>

            <table class="select-options" id='ophthalmic-diagnoses-select-options'>
                <tbody>
                <tr>
                    <td>
                        <div class="flex-layout flex-top flex-left">
                            <ul class="add-options" data-multi="true" data-clickadd="false" id="ophthalmic-diagnoses-option">
                                <?php
                                $firm_id = Yii::app()->session['selected_firm_id'];
                                $firm = \Firm::model()->findByPk($firm_id);

                                foreach (CommonOphthalmicDisorder::getListByGroupWithSecondaryTo($firm) as $disorder) { ?>
                                    <li data-str="<?= $disorder['label'] ?>" data-id="<?= $disorder['id']; ?>">
                        <span class="auto-width">
                          <?= $disorder['label']; ?>
                      </span>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                        <!-- flex-layout -->
                    </td>
                </tr>
                </tbody>
            </table>
            <div class="search-icon-btn"><i id="ophthalmic-diagnoses-search-btn" class="oe-i search"></i></div>
            <div class="ophthalmic-diagnoses-search-options" style="display: none;">
                <table class="cols-full last-left">
                    <thead>
                    <tr>
                        <th>
                            <input id="ophthalmic-diagnoses-search-field"
                                   class="search"
                                   placeholder="Search for Diagnoses"
                                   type="text">
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <ul id="ophthalmic-diagnoses-search-results" class="add-options" data-multi="true" style="width: 100%;">
                            </ul>
                            <span id="ophthalmic-diagnoses-search-no-results">No results found</span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script type="text/template" class="entry-template hidden">
        <?php
        $empty_entry = new \OEModule\OphCiExamination\models\Element_OphCiExamination_Diagnoses();
        echo $this->renderPartial(
            'DiagnosesEntry_event_edit',
            array(
                'model_name' => $model_name,
                'field_prefix' => $model_name . '[entries][{{row_count}}]',
                'row_count' => '{{row_count}}',
                'removable' => true,

                'values' => array(
                    'id' => '',
                    'disorder_id' => '{{disorder_id}}',
                    'disorder_display' => '{{disorder_display}}',
                    'eye_id' => '{{eye_id}}',
                    'event_date' => '{{event_date}}',
                    'date' => '{{date}}',
                    'date_display' => '{{date_display}}',
                    'row_count' => '{{row_count}}',
                    'is_principal' => '{{is_principal}}',
                ),
            )
        );
        ?>
    </script>
</div>

<script type="text/javascript">
    var diagnosesController;
    $(document).ready(function () {
        diagnosesController = new OpenEyes.OphCiExamination.DiagnosesController({
            element: $('#<?=$model_name?>_element'),
            subspecialtyRefSpec: '<?=$firm->subspecialty->ref_spec;?>'
        });
        $('#OphCiExamination_diagnoses').data('controller', diagnosesController);

        var popup = $('#add-to-ophthalmic-diagnoses');

        function addOphthalmicDiagoses() {
            diagnosesController.addEntry();
        }

        setUpAdder(
            popup,
            'multi',
            addOphthalmicDiagoses,
            $('#add-ophthalmic-diagnoses'),
            popup.find('.add-icon-btn'),
            $('.close-icon-btn, .add-icon-btn')
        );
    });
</script>
