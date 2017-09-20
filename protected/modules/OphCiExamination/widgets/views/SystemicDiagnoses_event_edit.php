<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>

<script type="text/javascript" src="<?=$this->getJsPublishedPath('SystemicDiagnoses.js')?>"></script>
<?php
$model_name = CHtml::modelName($element);
?>

<div class="element-fields" id="OphCiExamination_SystemicDiagnoses">

        <?php
        $this->widget('application.widgets.DiagnosisSelection', array(
            'nowrapper' => true,
            'field' => 'disorder_id',
            'options' => CommonSystemicDisorder::getList($this->controller->firm),
            'restrict' => 'systemic',
            'default' => false,
            'loader' => '#find_systemic_diagnosis_loader',
            'callback' => 'OpenEyes.OphCiExamination.SystemicDiagnosesSelectDiagnosis',
            'allowClear' => true,
            'layoutColumns' => array(
                'label' => 2,
                'field' => 4,
            ),
        ))?>
    <div class="field-row row">
        <div class="large-2 column"><label for="<?= $model_name ?>_diagnosis_side">Side:</label></div>
        <div class="large-3 column end">
            <label class="inline"><input type="radio" name="<?= $model_name ?>_diagnosis_side" class="<?= $model_name ?>_diagnosis_side" value="" checked="checked" /> None </label>
            <?php foreach (Eye::model()->findAll(array('order' => 'display_order')) as $eye) {?>
                <label class="inline"><input type="radio" name="<?= $model_name ?>_diagnosis_side" class="<?= $model_name ?>_diagnosis_side" value="<?php echo $eye->id?>" /> <?php echo $eye->name ?></label>
            <?php }?>
        </div>
    </div>
    <div class="row">
        <div class="large-8 column">
            <?php $this->render('application.views.patient._fuzzy_date', array('class' => $model_name . '_diagnosis_fuzzy_date')) ?>
        </div>
        <div class="large-4 column end">
            <button class="button small primary add-diagnosis" id="<?= $model_name ?>_add_diagnosis">Add</button>
        </div>
    </div>

    <input type="hidden" name="<?= $model_name ?>[present]" value="1" />
    <table id="<?= $model_name ?>_diagnoses_table">
        <thead>
        <tr>
            <th>Diagnosis</th>
            <th>Side</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($element->diagnoses as $diagnosis) {
            $this->render(
                'SystemicDiagnoses_Diagnosis_event_edit',
                array(
                    'diagnosis' => $diagnosis,
                    'form' => $form,
                    'model_name' => CHtml::modelName($element),
                )
            );
        }
        ?>
        </tbody>
    </table>
</div>

<script type="text/template" id="<?= CHtml::modelName($element).'_diagnosis_template' ?>" class="hidden">
    <?php
    $empty_diagnosis = new \OEModule\OphCiExamination\models\SystemicDiagnoses_Diagnosis();
    $this->render(
        'SystemicDiagnoses_Diagnosis_event_edit',
        array(
            'diagnosis' => $empty_diagnosis,
            'form' => $form,
            'model_name' => CHtml::modelName($element),
            'values' => array(
                'id' => '',
                'disorder_id' => '{{disorder_id}}',
                'disorder_display' => '{{disorder_display}}',
                'side_id' => '{{side_id}}',
                'side_display' => '{{side_display}}',
                'date' => '{{date}}',
                'date_display' => '{{date_display}}'
            )
        )
    );
    ?>
</script>
