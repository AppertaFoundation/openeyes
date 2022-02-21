<?php
/**
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php
use OEModule\OphCiExamination\models\OphCiExamination_Triage;
use OEModule\OphCiExamination\models\OphCiExamination_Triage_Priority;
use OEModule\OphCiExamination\models\OphCiExamination_Triage_ChiefComplaint;
use OEModule\OphCiExamination\models\OphCiExamination_Triage_EyeInjury;

$triage = $element->triage ?: new OphCiExamination_Triage();
$model_name = CHtml::modelName($element) . "[triage]";
$priority_list = OphCiExamination_Triage_Priority::model()->findAll();

$display_treat_as_adult = false;
$display_treat_as_paediatric = false;
$preselect_treat_as_adult = false;

$age = $this->patient->getAge();

if ($age < 13) {
    $display_treat_as_paediatric = true;
} elseif ($age < 16) {
    $display_treat_as_adult = true;
    $display_treat_as_paediatric = true;
} elseif ($age < 18) {
    $display_treat_as_adult = true;
    $display_treat_as_paediatric = true;
    $preselect_treat_as_adult = true;
} else {
    $display_treat_as_adult = true;
    $preselect_treat_as_adult = true;
}

if (!$element->isNewRecord) {
    $preselect_treat_as_adult = $element->triage->treat_as_adult;
}

$treat_as_input_type = $display_treat_as_paediatric && $display_treat_as_adult ? 'radio' : 'hidden';
?>

<div class="element-fields full-width">
    <div class="flex-t">
        <div class="cols-5">
            <table class="cols-full last-left">
                <colgroup>
                    <col class="cols-4">
                </colgroup>
                <tbody>
                <tr>
                    <th>Time</th>
                    <td>
                        <input type="time" name="<?= $model_name ?>[time]" value="<?= $triage->time ?>" class="fixed-width-medium">
                    </td>
                </tr>
                <tr>
                    <th>Treat as</th>
                    <td>
                        <fieldset>
                            <?php
                            if ($display_treat_as_paediatric) {
                                ?>
                                <label class="highlight inline">
                                    <input value="0" name="<?=$model_name?>[treat_as_adult]" type="<?=$treat_as_input_type?>"<?=!$preselect_treat_as_adult ? 'checked' : '' ?>>
                                    Paediatric
                                </label>
                                <?php
                            }
                            ?>
                            <?php
                            if ($display_treat_as_adult) {
                                ?>
                                <label class="highlight inline">
                                    <input value="1" name="<?=$model_name?>[treat_as_adult]" type="<?=$treat_as_input_type?>"<?=$preselect_treat_as_adult ? 'checked' : '' ?>>
                                    Adult
                                </label>
                                <?php
                            }
                            ?>
                        </fieldset>
                    </td>
                </tr>
                </tbody>
            </table>
            <hr class="divider">
            <div class="small-row fade">Is the patient also under the care of a different eye unit?</div>
            <?php $this->widget('application.widgets.AutoCompleteSearch', [
                'field_name' => 'triage_hospital_search',
                'htmlOptions' => ['placeholder' => 'Referred hospital'],
            ]); ?>
            <div id="triage-hospital">
                <?php if ($triage->site) {
                    echo '<ul class="oe-multi-select inline"><li>' . $triage->site->name . '<i class="oe-i remove-circle small-icon pad-left"></i></li></ul>';
                } ?>
                <input type="hidden" name="<?= $model_name ?>[site_id]" value="<?= $triage->site_id ?>" />
            </div>
        </div>
        <div class="cols-5">
            <table class="cols-full last-left">
                <colgroup>
                    <col class="cols-4">
                </colgroup>
                <tbody>
                <tr>
                    <th>Set priority</th>
                    <td>
                        <fieldset>
                            <?php foreach ($priority_list as $list) { ?>
                                <label class="highlight">
                                    <input value="<?= $list->id ?>" name="<?= $model_name ?>[priority_id]" type="radio"
                                        <?= ($triage->priority && $triage->priority->id === $list->id) ? 'checked' : '' ?>>
                                    <i class="oe-i small pad circle-<?= $list->label_colour ?>"></i>
                                    <?= $list->description ?>
                                </label>
                            <?php } ?>
                        </fieldset>
                    </td>
                </tr>
                </tbody>
            </table>
            <hr class="divider">
            <div>Chief complaint</div>
            <div class="flex-l row large-text js-chief-complaint">
                <?php $this->widget('EyeLateralityWidget', ['laterality' => $triage->eye]) ?>
                <span id="chief_complaint_text"><?= $triage->getChiefComplaint() ?: 'None' ?></span>
                <input type="hidden" name="<?= $model_name ?>[chief_complaint_id]" value="<?= $triage->chief_complaint_id ?>" />
                <input type="hidden" name="<?= $model_name ?>[eye_injury_id]" value="<?= $triage->eye_injury_id ?>" />
                <input type="hidden" name="<?= $model_name ?>[eye_id]" value=" <?= $triage->eye_id ?>" />
            </div>
            <div id="triage-comment" class="cols-full" style="<?= $triage->comments !== null ? 'display: block;' : 'display: none;' ?>">
                <div class="comment-group flex-layout flex-left">
                    <textarea placeholder="Comments" autocomplete="off" rows="1" class="js-input-comments cols-full"
                              name="<?= $model_name ?>[comments]"><?= $triage->comments ?></textarea>
                    <i class="oe-i remove-circle small-icon pad-left  js-remove-add-comments"></i>
                </div>
            </div>
        </div>
        <div class="add-data-actions flex-item-bottom" >
            <button class="button js-add-comments">
                <i class="oe-i comments small-icon"></i>
            </button>
            <button class="adder js-add-select-btn" type="button" id="add-chief-complaint"></button>
        </div>
    </div>
</div>

<?php
$chief_complaints = OphCiExamination_Triage_ChiefComplaint::model()->findAll();
$eye_injuries = OphCiExamination_Triage_EyeInjury::model()->findAll();
$eyes = [
    ['id' => \Eye::LEFT, 'label' => 'Left Only'],
    ['id' => \Eye::RIGHT, 'label' => 'Right Only'],
    ['id' => \Eye::BOTH, 'label' => 'Right & Left Eye'],
]
?>

<script type="text/javascript" src="<?= Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.widgets.js') . '/AutoCompleteSearch.js', true, -1); ?>"></script>
<script type="text/javascript">
    OpenEyes.UI.AutoCompleteSearch.init({
        input: $('#triage_hospital_search'),
        url: '/site/listSites',
        onSelect: function (){
            let AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
            $('#triage-hospital').append('<ul class="oe-multi-select inline"><li>'+AutoCompleteResponse.label+'<i class="oe-i remove-circle small-icon pad-left"></i></li></ul>');
            $('#triage-hospital input').val(AutoCompleteResponse.value);
            return false;
        }
    });

    $(function () {
        new OpenEyes.UI.AdderDialog({
            openButton: $('#add-chief-complaint'),
            itemSets: [
                new OpenEyes.UI.AdderDialog.ItemSet(<?= json_encode(
                    array_map(function ($item) {
                        return [
                            'id' => $item->id,
                            'label' => $item->description,
                        ];
                    }, $chief_complaints)
                ) ?>, {'header':'Chief Complaint', 'id':'chief_complaint_id', 'multiSelect': false, 'deselectOnReturn': false}),
                new OpenEyes.UI.AdderDialog.ItemSet(<?= json_encode(
                    array_map(function ($item) {
                        return [
                            'id' => $item->id,
                            'label' => $item->description,
                        ];
                    }, $eye_injuries)
                ) ?>, {'header':'Eye Injury', 'id':'eye_injury_id', 'multiSelect': false, 'deselectOnReturn': false }),
                new OpenEyes.UI.AdderDialog.ItemSet(<?= json_encode(
                    array_map(function ($item) {
                        return [
                            'id' => $item['id'],
                            'label' => $item['label'],
                        ];
                    }, $eyes)
                ) ?>, {'header':'Eye','id':'eye_id', 'multiSelect': false, 'deselectOnReturn': false}),
            ],
            onOpen: function (adderDialog) {
                adderDialog.toggleColumnById(['eye_injury_id'], false);
            },
            onReturn: function (adderDialog, selectedItems) {
                addSelectedItems(selectedItems);
                return true;
            },
        });
    });

    $(document).ready(function () {
        if ($('input[name$="[time]"]').val() === '') {
            $('input[name$="[time]"]').val(setCurrentTime());
        }

        $('#triage-hospital').on('click', '.oe-i.remove-circle.small-icon.pad-left', function (e) {
            e.preventDefault();
            $(e.target).closest('ul').remove();
            $('#triage-hospital input').val(null);
        });

        $('.js-add-comments').click(function (e) {
            e.preventDefault();
            $(this).hide();
            $('#triage-comment').show();
        });

        $('.js-remove-add-comments').click(function (e) {
            e.preventDefault();
            $(this).prev('textarea').val(null);
            $('#triage-comment').hide();
            $('.js-add-comments').show();
        });

        $('ul[data-id="chief_complaint_id"]').on('click', 'li', function (e) {
            e.preventDefault();
            if ($(e.target).text() === 'Eye injury' && !$(this).hasClass('selected')) {
                $('th[data-id="eye_injury_id"]').show();
                $('td[data-adder-id="eye_injury_id"]').show();
            } else {
                $('th[data-id="eye_injury_id"]').hide();
                $('td[data-adder-id="eye_injury_id"]').hide();
            }
        });

        $('input[name="OEModule_OphCiExamination_models_Element_OphCiExamination_Triage[triage][treat_as_adult]"]').change(
            function() {
                let treat_as_adult = $(this).val() === "1";

                OphCiExamination_ToggleSafeguardingPaediatricFields(!treat_as_adult);
            }
        )
    });

    function setEyeLaterality(eye_id) {
        let $eye_lat_icons = $('.js-chief-complaint').children();
        $eye_lat_icons.empty();
        if (eye_id === 1) {
            $eye_lat_icons.append('<i class="oe-i laterality small pad NA"></i>');
            $eye_lat_icons.append('<i class="oe-i laterality small pad L"></i>');
        }
        else if (eye_id === 2) {
            $eye_lat_icons.append('<i class="oe-i laterality small pad R"></i>');
            $eye_lat_icons.append('<i class="oe-i laterality small pad NA"></i>');
        }
        else if (eye_id === 3) {
            $eye_lat_icons.append('<i class="oe-i laterality small pad R"></i>');
            $eye_lat_icons.append('<i class="oe-i laterality small pad L"></i>');
        } else {
            $eye_lat_icons.append('<i class="oe-i laterality small pad NA"></i>');
            $eye_lat_icons.append('<i class="oe-i laterality small pad NA"></i>');
        }
        $('input[name$="[eye_id]"]').val(eye_id);
    }

    function addSelectedItems(selectedItems) {
        let chief_complaint_text = 'None';
        $('input[name$="[chief_complaint_id]"]').val(null);
        $('input[name$="[eye_injury_id]"]').val(null);
        setEyeLaterality();

        selectedItems.forEach(function (item) {
            let options_id = item['itemSet']['options']['id'];
            if (options_id === 'chief_complaint_id') {
                chief_complaint_text = item['label'];
                $('input[name$="[chief_complaint_id]"]').val(item['id']);
            }
            else if (options_id === 'eye_injury_id') {
                chief_complaint_text = chief_complaint_text + ' - ' + item['label'];
                $('input[name$="[eye_injury_id]"]').val(item['id']);
            }
            else {
                setEyeLaterality(item['id']);
            }
        });

        $('#chief_complaint_text').text(chief_complaint_text);
    }

    function setCurrentTime()
    {
        let date = new Date();
        let hours = date.getHours();
        let minutes = date.getMinutes();
        return hours + ':' + minutes;
    }
</script>
