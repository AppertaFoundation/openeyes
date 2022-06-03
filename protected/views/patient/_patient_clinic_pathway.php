<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 *
 * @var $pathway Pathway
 * @var $editable bool
 * @var $display_wait_duration bool
 */
$exam_api = \Yii::app()->moduleAPI->get('OphCiExamination');
$pick_behavior = new SetupPathwayStepPickerBehavior();
$path_step_type_ids = json_encode($pick_behavior->getPathwayStepTypesRequirePicker());
$picker_setup = $pick_behavior->setupPicker();
?>
<div class="clinic-pathway">
    <table class="oec-patients in-event">
        <tbody>
        <tr>
            <td><?= $pathway->worklist_patient->scheduledtime ?></td>
            <td>
                <div class="list-name"><?= $pathway->worklist_patient->worklist->name ?></div>
            </td>
            <td class="js-pathway-container">
                <?php $this->renderPartial(
                    '//worklist/_clinical_pathway',
                    ['visit' => $pathway->worklist_patient]
                ) ?>
            </td>
            <td class="js-pathway-assignee" data-id="<?= $pathway->owner_id ?>">
                <div class="flex">
                    <i class="oe-i person no-click small"></i>
                    <?= $pathway->owner ? $pathway->owner->getInitials() : null ?>
                </div>
            </td>
            <td>
                <?= $exam_api->getLatestTriagePriority($pathway->worklist_patient->patient, $pathway->worklist_patient->worklist) ?>
            </td>
            <td>
                <span class="oe-pathstep-btn buff comments <?= $pathway->checkForComments() ? 'comments-added' : '' ?>"
                      data-worklist-patient-id="<?= $pathway->worklist_patient_id?>"
                      data-pathway-id="<?= $pathway->id ?>"
                      data-patient-id="<?= $pathway->worklist_patient->patient_id ?>"
                      data-pathstep-id="comment">
                    <span class="step i-comments"></span>
                    <span class="info" style="display: none;"></span>
                </span>
            </td>
            <?php if ($display_wait_duration) { ?>
            <td>
                <div class="wait-duration">
                    <?= $pathway->getTotalDurationHTML(true) ?>
                </div>
            </td>
            <?php }
            if ($editable) {
                if (isset($quick_preset_adder) && $quick_preset_adder['display'] === true) {
                    //Register script file only if it hasn't already been registered
                    if (!array_key_exists(Yii::getPathOfAlias('application.assets.js.worklist') . '/worklist.js', Yii::app()->clientScript->scriptMap)) {
                        $worklist_js = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.assets.js.worklist') . '/worklist.js', true);
                        Yii::app()->clientScript->registerScriptFile(Yii::app()->assetManager->createUrl('js/OpenEyes.UI.PathStep.js'), ClientScript::POS_END);
                        Yii::app()->clientScript->registerScriptFile($worklist_js, ClientScript::POS_END);
                    }
                    $psd_step_type_id = Yii::app()->db->createCommand()
                        ->select('id')
                        ->from('pathway_step_type')
                        ->where('short_name = \'drug admin\'')
                        ->queryScalar(); ?>
                <td>
                    <button id=<?= "add-next-steps-preset-" . $pathway->id?> type="button" class="green hint js-add-select-btn">Next presets</button>
                </td>
                <script type="text/javascript">
                    // This check will prevent double-definition errors when both the next-step element and the pathway status widgets are present on the same screen.
                    if (typeof pathwaySetupData === undefined) {
                        const pathwaySetupData = <?=$picker_setup?>;
                    }

                    $(document).ready(function () {
                        let picker = new OpenEyes.UI.PathwayStepPicker({
                            ...<?=$path_step_type_ids?>,
                            ...pathwaySetupData,
                            pathways: <?= $pathway->getPathwaysJSON() ?>,
                        });
                        picker.init();

                        let pathwayId = <?= $pathway->id ?>;

                        let itemSets = [
                            new OpenEyes.UI.AdderDialog.ItemSet(
                            <?= $pathway->getPathwaysJSON() ?>,
                            {'hideByDefault': false, 'multiSelect': false},
                            ),
                        ]

                        let $adderButton = $(`#add-next-steps-preset-${pathwayId}`);

                        new OpenEyes.UI.AdderDialog({
                            openButton: $adderButton,
                            deselectOnReturn: true,
                            parentContainer: 'body',
                            itemSets: itemSets,
                            onReturn: function (adderDialog, selectedItems, selectedAdditions) {
                                let success = false;

                                let selectedValues = [{value: selectedItems[0].id}, {value: 1}];

                                $.ajax({
                                    url: baseUrl + '/worklist/addPathwayStepsToPathway',
                                    data: {
                                        selected_values: selectedValues,
                                        target_pathway_id: pathwayId,
                                        YII_CSRF_TOKEN: YII_CSRF_TOKEN,
                                    },
                                    async: false,
                                    type: 'POST',
                                    success: function (response) {
                                        $adderButton.closest('tr').find('td.js-pathway-container').html(response.step_html);
                                        success = true;
                                    },
                                });
                                return success;
                            }
                        })
                    });
                </script>
                <?php } ?>
                <td>
                    <label class="patient-checkbox">
                        <input class="js-check-patient" value="<?= $pathway->worklist_patient_id ?>" type="checkbox"/>
                        <div class="checkbox-btn"></div>
                    </label>
                </td>
            <?php } ?>
        </tr>
        </tbody>
    </table>
</div>
<?php
if ($editable) {
    echo $this->renderPartial(
        '//worklist/pathway_step_picker',
        [
            'path_steps' => PathwayStepType::getPathTypes(),
            'pathways' => PathwayType::model()->findAll(),
            'standard_steps' => PathwayStepType::getStandardTypes(),
            'custom_steps' => PathwayStepType::getCustomTypes(),
            'show_pathway_selected' => false,
            'show_undo_step' => false,
        ],
        true
    );
}
?>
