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
if (empty($element->entries) && empty($element->no_family_history_date)) { ?>
    <div class="nil-recorded">
        Patient family history is unknown
    </div>
<?php } else {
    Yii::app()->clientScript->registerScriptFile($this->getJsPublishedPath('FamilyHistory.js'), CClientScript::POS_BEGIN);
    $model_name = CHtml::modelName($element);
    ?>
    <?php $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => 'edit-family-history',
        'enableAjaxValidation' => false,
        'action' => array('changeEvent/save'),
        'layoutColumns' => array(
        ),
    ))?>
    <div class="nil-recorded"
         style="<?= empty($element->no_family_history_date) &&  !empty($element->entries) ? 'display: none;':'' ?>"
    >
        Patient has no family history
    </div>

    <table id="<?=$model_name ?>_patient_mode_table" class="plain patient-data" <?php if (empty($element->entries)) { echo 'style="display: none;"'; }?>>
        <thead>
        <tr>
            <th>Relative</th>
            <th>Side</th>
            <th>Condition</th>
            <th>Comments</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($element->entries as $i => $entry) {
            $this->render(
                'FamilyHistory_Entry_event_edit',
                array(
                    'entry' => $entry,
                    'model_name' => CHtml::modelName($element),
                    'editable' => false,
                    'row_count' => $i,
                    'field_prefix' => $model_name . '[entries][' . $i . ']',
                )
            );
        }
        ?>
        </tbody>
    </table>
    <?php if ($this->canEdit()) { ?>
    <div class="box-actions">
        <button id="btn-edit-family-history" class="secondary small">
            Edit Family History
        </button>
    </div>

    <div id="family-history-form" style="display: none;">
        <legend><strong>Add family history</strong></legend>

        <input type="hidden" name="patient_id" value="<?php echo $this->patient->id?>" />
        <input type="hidden" name="element_type_id" value="<?php echo $element->getElementType()->id ?>" />

        <?php $this->render('FamilyHistory_form',
            array(
                'element' => $element,
                'model_name' => $model_name,
            )
        );?>

        <div class="buttons">
            <img src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" class="add_family_history_loader" style="display: none;" />
            <button type="submit" class="secondary small" id="btn-save-family-history">
                Save
            </button>
            <button class="warning small" id="btn-cancel-family-history">
                Cancel
            </button>
        </div>
    </div>
    <script type="text/template" id="<?= CHtml::modelName($element).'_entry_template' ?>" class="hidden">
        <?php
        $empty_entry = new \OEModule\OphCiExamination\models\FamilyHistory_Entry();
        $this->render(
            'FamilyHistory_Entry_event_edit',
            array(
                'entry' => $empty_entry,
                'form' => $form,
                'model_name' => CHtml::modelName($element),
                'editable' => true,
                'row_count' => '{{row_count}}',
                'values' => array(
                    'id' => '',
                    'relative_id' => '{{relative_id}}',
                    'relative_display' => '{{relative_display}}',
                    'other_relative' => '{{other_relative}}',
                    'side_id' => '{{side_id}}',
                    'side_display' => '{{side_display}}',
                    'condition_id' => '{{condition_id}}',
                    'condition_display' => '{{condition_display}}',
                    'other_condition' => '{{other_condition}}',
                    'comments' => '{{comments}}',
                )
            )
        );
        ?>
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            new OpenEyes.OphCiExamination.FamilyHistoryPatientController();
        });
    </script>
    <?php }
    $this->endWidget();
}?>
