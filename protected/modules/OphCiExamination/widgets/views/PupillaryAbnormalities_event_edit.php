<?php
/**
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php use \OEModule\OphCiExamination\models\PupillaryAbnormalityEntry; ?>
<script type="text/javascript" src="<?= $this->getJsPublishedPath('PupillaryAbnormalities.js') ?>"></script>
<?php
$model_name = CHtml::modelName($element);
$required_abnormality_ids = array_map(function ($required_abnormality) {
    return $required_abnormality->id;
}, $this->getRequiredAbnormalities());
?>

<div class="element-fields element-eyes" id="<?= $model_name ?>_form">
    <?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>
    <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) : ?>
        <div class="<?= $eye_side ?>-eye column <?= $page_side ?> side js-element-eye" data-side="<?= $eye_side ?>">
            <div class="active-form flex-layout" style="<?= !$element->hasEye($eye_side) ? "display: none;" : "" ?>">
                <div class="remove-side"><i class="oe-i remove-circle small"></i></div>
                <div class="cols-full">
                    <div class="cols-5 align-left" id="<?= $model_name ?>_no_abnormalities_wrapper_<?= $eye_side ?>"
                        <?= $this->isAbnormalitiesSet($element, $eye_side) ? ' style="display:none"' : '' ?>>
                        <input type="hidden"
                               name="<?= $model_name . '[' . $eye_side . '_no_pupillaryabnormalities]' ?>"
                               value="0">
                        <label class="inline highlight" for="<?= $model_name . '_' . $eye_side ?>_no_pupillaryabnormalities" id="<?= $model_name . '_' . $eye_side ?>_no_pa_label">
                            <?= \CHtml::checkBox($model_name . '[' . $eye_side . '_no_pupillaryabnormalities]', $element->{'no_pupillaryabnormalities_date_' . $eye_side} ? true : false); ?>
                            Confirm patient has no pupillary abnormalities
                        </label>
                    </div>
                    <table class="cols-full pa-entry-table">
                        <colgroup>
                            <col class="cols-3">
                            <col>
                            <col class="cols-4">
                        </colgroup>
                        <tbody>

                        <?php
                        $missing_req_abnormalities = $this->getMissingRequiredAbnormalities($eye_side);
                        $row_count = 0;
                        foreach ($missing_req_abnormalities as $entry) {
                            $this->render(
                                'PupillaryAbnormalityEntry_event_edit',
                                array(
                                    'entry' => $entry,
                                    'form' => $form,
                                    'side' => $eye_side,
                                    'model_name' => $model_name,
                                    'removable' => false,
                                    'abnormalities' => $element->getAbnormalityOptions(),
                                    'has_abnormality' => $entry->has_abnormality,
                                    'field_prefix' => $model_name . '[entries_' . $eye_side . '][' . $row_count . ']',
                                    'row_count' => $row_count,
                                    'eye_id' => $eye_side === "left" ? 1 : 2,
                                )
                            );
                            $row_count++;
                        }

                        foreach ($element->{'entries_' . $eye_side} as $i => $entry) {
                            $this->render(
                                'PupillaryAbnormalityEntry_event_edit',
                                array(
                                    'entry' => $entry,
                                    'form' => $form,
                                    'side' => $eye_side,
                                    'model_name' => $model_name,
                                    'removable' => !in_array($entry->abnormality_id, $required_abnormality_ids),
                                    'abnormalities' => $element->getAbnormalityOptions(),
                                    'field_prefix' => $model_name . '[entries_' . $eye_side . '][' . $row_count . ']',
                                    'row_count' => $row_count,
                                    'has_abnormality' => $entry->has_abnormality,
                                    'eye_id' => $eye_side === "left" ? 1 : 2,
                                )
                            );
                            $row_count++;
                        } ?>

                        </tbody>
                    </table>
                    <div class="flex-layout flex-right">
                        <div class="add-data-actions flex-item-bottom" id="history-abnormality-popup-<?= $eye_side ?>">
                            <button class="button hint green js-add-select-search"
                                    id="add-abnormality-btn-<?= $eye_side ?>"
                                    type="button"
                                <?= $element->{'no_pupillaryabnormalities_date_' . $eye_side} ? ' style="display: none;"' : ''?>>
                                <i class="oe-i plus pro-theme"></i>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
            <div class="inactive-form" style="<?= $element->hasEye($eye_side) ? "display: none;" : "" ?>">
                <div class="add-side">
                    <a href="#">
                        Add <?= $eye_side ?> side <span class="icon-add-side"></span>
                    </a>
                </div>
            </div>
        </div>
        <script>
            $(function () {
                let pupillary_abnormality_controller = $('.OEModule_OphCiExamination_models_PupillaryAbnormalities').data('controller');

                new OpenEyes.UI.AdderDialog({
                    openButton: $('#add-abnormality-btn-<?= $eye_side ?>'),
                    itemSets: [new OpenEyes.UI.AdderDialog.ItemSet(
                        <?= CJSON::encode(
                            array_map(function ($abnormality) {
                                return ['label' => $abnormality->name, 'id' => $abnormality->id];
                            }, $element->getAbnormalityOptions()))?>,
                        {'multiSelect': true, 'id': 'pupillary_abnormalities_list_<?= $eye_side ?>'}
                    )],
                    onReturn: function (adder_dialog, selected_items) {
                        let table_selector = '.<?= $eye_side ?>-eye .pa-entry-table';
                        pupillary_abnormality_controller.addEntry(table_selector, selected_items);
                        return true;
                    }
                });

                ['right', 'left'].forEach(function (side) {
                    pupillary_abnormality_controller.dedupeAbnormalitiesSelector(side);
                });
            });
        </script>
    <?php endforeach; ?>
</div>

<script type="text/template" id="<?= CHtml::modelName($element) . '_entry_template' ?>" style="display:none">
    <?php
    $empty_entry = new PupillaryAbnormalityEntry();
    $this->render(
        'PupillaryAbnormalityEntry_event_edit',
        array(
            'entry' => $empty_entry,
            'form' => $form,
            'side' => '{{side}}',
            'model_name' => $model_name,
            'removable' => true,
            'abnormalities' => $element->getAbnormalityOptions(),
            'field_prefix' => $model_name . '[entries_{{side}}][{{row_count}}]',
            'row_count' => '{{row_count}}',
            'eye_id' => '{{eye_id}}',
            'values' => array(
                'id' => '',
                'abnormality_id' => '{{abnormality_id}}',
                'abnormality_display' => '{{abnormality_display}}',
                'comments' => null,
                'has_abnormality' => (string)PupillaryAbnormalityEntry::$PRESENT,
            ),
        )
    );
    ?>
</script>
