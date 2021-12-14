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
$js_path = Yii::app()->getAssetManager()->publish(
    Yii::getPathOfAlias('application.assets.js') . '/OpenEyes.UI.DiagnosesSearch.js',
    true,
    -1
);

$assetManager = Yii::app()->getAssetManager();
$widgetPath = $assetManager->publish('protected/widgets/js/EyeSelector.js');
Yii::app()->clientScript->registerScriptFile($widgetPath);
//  90 is priority that is used in the CWidget , using the same value over here to mimic it
$assetManager->registerScriptFile('js/EyeSelector.js', 'application.widgets', 90);
Yii::app()->clientScript->registerScriptFile("{$this->assetPath}/js/Diagnoses.js", CClientScript::POS_HEAD);

$user_firm = Firm::model()->with(array(
    'serviceSubspecialtyAssignment' => array('subspecialty'),
))->findByPk(Yii::app()->session['selected_firm_id']);

$current_episode = Episode::getCurrentEpisodeByFirm($this->patient->id, $user_firm);

$read_only_diagnoses = [];
foreach ($this->patient->episodes as $ep) {
    $diagnosis = $ep->diagnosis; // Disorder model
    if ($diagnosis && $diagnosis->specialty && $diagnosis->specialty->code == 130 && (!$current_episode || $ep->id != $current_episode->id)) {
        $read_only_diagnoses[] = [
            'diagnosis' => $diagnosis,
            'eye' => Eye::methodPostFix($ep->eye_id),
            'subspecialty' => $ep->getSubspecialtyText(),
            'is_glaucoma' => isset($diagnosis->term) ? (strpos(strtolower($diagnosis->term), 'glaucoma')) !== false : false,
            'date' => $ep->getDisplayDate(),
        ];
    }
}
?>

<script type="text/javascript" src="<?= $js_path; ?>"></script>

<?php $model_name = CHtml::modelName($element); ?>

<div class="element-fields flex-layout full-width" id="<?= CHtml::modelName($element); ?>_element">
    <input type="hidden" name="<?= \CHtml::modelName($element); ?>[force_validation]"/>
    <input type="hidden" name="glaucoma_diagnoses[]"/>

    <input type="hidden" name="<?= $model_name ?>[present]" value="1"/>

    <div class="cols-1 align-left <?= $model_name ?>_no_ophthalmic_diagnoses_wrapper" style="display: <?php echo count($element->diagnoses)===0 ? '' : 'none'; ?>">
        <label class="inline highlight" for="<?= $model_name ?>_no_ophthalmic_diagnoses">
            <?= \CHtml::checkBox(
                $model_name . '[no_ophthalmic_diagnoses]',
                $element->no_ophthalmic_diagnoses_date ? true : false,
                array('class' => $model_name. '_no_ophthalmic_diagnoses')
            ); ?>
            No ophthalmic diagnoses
        </label>
    </div>

    <table id="<?= $model_name ?>_diagnoses_table" class="cols-10" style="display: <?php echo count($element->diagnoses)===1 ? '' : 'none'; ?>">
            <colgroup>
                <col class="cols-4">
                <col class="cols-2">
                <col class="cols-2">
                <col class="cols-3">
                <col class="cols-1">
            </colgroup>
            <thead>
                <tr>
                    <th>Diagnosis</th>
                    <th>Side</th>
                    <th>Principal</th>
                    <th>Date</th>
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
                'is_template' => true,

                'values' => array(
                    'id' => '',
                    'disorder_id' => '{{disorder_id}}',
                    'disorder_display' => '{{disorder_display}}',
                    'eye_id' => '{{eye_id}}',
                    'event_date' => '{{event_date}}',
                    'is_glaucoma' => '{{is_glaucoma}}',
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
            subspecialtyRefSpec: '<?=$user_firm->subspecialty->ref_spec;?>'
        });
        $('#OphCiExamination_diagnoses').data('controller', diagnosesController);
        <?php
        $disorder_list = CommonOphthalmicDisorder::getListByGroupWithSecondaryTo($user_firm);
        $commonNotEmptyOphthalmicDisorderGroups = [];
        foreach ($disorder_list as $disorder) {
            if (isset($disorder['group']) && $disorder['group'] != "") {
                $commonNotEmptyOphthalmicDisorderGroups[] = $disorder['group_id'];
            }
        }

        $commonOphthalmicDisorderGroups = CommonOphthalmicDisorderGroup::model()->findAllAtLevel(ReferenceData::LEVEL_INSTITUTION);
        $filteredOphthalmicDisorderGroups = [];

        foreach ($commonOphthalmicDisorderGroups as $disorderGroup) {
            if (in_array($disorderGroup->id, $commonNotEmptyOphthalmicDisorderGroups)) {
                $filteredOphthalmicDisorderGroups[] = $disorderGroup;
            }
        }?>

        new OpenEyes.UI.AdderDialog({
            openButton: $('#add-ophthalmic-diagnoses'),
            itemSets: [
                <?php if (!empty($filteredOphthalmicDisorderGroups)) { ?>
                new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
                    array_map(function ($disorderGroup) {
                        return [
                            'label' => $disorderGroup->name,
                            'filter-value' => $disorderGroup->id,
                            'is_filter' => true,
                        ];
                    },
                    $filteredOphthalmicDisorderGroups)
                                                    ) ?>, {
                    'header': 'Disorder Group',
                    'id': 'disorder-group-filter',
                    'deselectOnReturn': false,
                }),
                <?php }?>
                new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
                    array_map(function ($disorder_item) {
                        return [
                            'type' => $disorder_item['type'],
                            'label' => $disorder_item['label'],
                            'id' => $disorder_item['id'],
                            'filter_value' => $disorder_item['group_id'],
                            'is_glaucoma' => $disorder_item['is_glaucoma'],
                            'secondary' => json_encode($disorder_item['secondary']),
                            'alternate' => json_encode($disorder_item['alternate']),
                        ];
                    }, $disorder_list)
                ) ?>, {'multiSelect': true, 'id': 'disorder-list'}),
            ],
            searchOptions: {
                searchSource: diagnosesController.options.searchSource,
                code: diagnosesController.options.code,
            },
            onReturn: function (adderDialog, selectedItems, selectedAdditions) {
                var diag = [];
                for (let i in selectedItems) {
                    let item = selectedItems[i];
                    if (!item.is_filter) {
                        // If common item is a 'finding', we add it to the findings element instead
                        // Otherwise treat it as a diagnosis
                        if (item.type === 'finding') {
                            OphCiExamination_AddFinding(item.id, item.label);
                        } else {
                            diag.push(item);
                        }
                    }
                }
                diagnosesController.addEntry(diag);
                if (selectedAdditions) {
                    diagnosesController.addEntry(selectedAdditions);
                }
                return true;
            },
            listFilter: true,
            filterListId: "disorder-group-filter",
            listForFilterId: "disorder-list",
            liClass: "restrict-width extended"
        });
    })
</script>
