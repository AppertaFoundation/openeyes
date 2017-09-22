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
use OEModule\OphCiExamination\models\OphCiExamination_PostOpComplications;

?>
<?php

$operationNoteList = $element->getOperationNoteList();
$operation_note_id = \Yii::app()->request->getParam('OphCiExamination_postop_complication_operation_note_id', (is_array($operationNoteList) ? key($operationNoteList) : null));

$firm = \Firm::model()->findByPk(\Yii::app()->session['selected_firm_id']);
$subspecialty_id = $this->firm->serviceSubspecialtyAssignment ? $this->firm->serviceSubspecialtyAssignment->subspecialty_id : null;

$right_eye = OphCiExamination_PostOpComplications::model()->getPostOpComplicationsList($element->id, $operation_note_id, $subspecialty_id, \Eye::RIGHT);

$right_eye_data = \CHtml::listData($right_eye, 'id', 'name');

$left_eye = OphCiExamination_PostOpComplications::model()->getPostOpComplicationsList($element->id, $operation_note_id, $subspecialty_id, \Eye::LEFT);
$left_eye_data = \CHtml::listData($left_eye, 'id', 'name');

$defaultURL = '/'.Yii::app()->getModule('OphCiExamination')->id.'/'.Yii::app()->getModule('OphCiExamination')->defaultController;

$left_values = $element->getRecordedComplications(\Eye::LEFT, $operation_note_id);
$right_values = $element->getRecordedComplications(\Eye::RIGHT, $operation_note_id);

?>

<?php if ($operationNoteList): ?>

<div class="row field-row" id="div_Element_OphTrOperationnote_ProcedureList_id">

    <div class="large-4 column">
        <label for="Element_OphTrOperationnote_ProcedureList_id" class="right">Operation:</label>
    </div>
    <div class="large-5 column end">
	<?php echo CHtml::dropDownList('OphCiExamination_postop_complication_operation_note_id', $operation_note_id,
            $operationNoteList,
            array('id' => 'OphCiExamination_postop_complication_operation_note_id-select', 'name' => 'OphCiExamination_postop_complication_operation_note_id')
        );?>
    </div>
</div>

<div class="element-fields element-eyes row">
	<?php
            echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField'));
        ?>
	<div class="element-eye right-eye column left side" data-side="right">
		<div class="active-form">
			<a href="#" class="icon-remove-side remove-side">Remove side</a>
			<?php
                                echo $form->dropDownList(
                                    OphCiExamination_PostOpComplications::model(),
                                    'name', $right_eye_data,
                                    array(
                                        'empty' => array('-1' => '-- Select --'),
                                        'id' => 'right-complication-select',
                                    ),
                                    false,
                                    array('label' => 4, 'field' => 6)
                                );

                                 $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                    'name' => 'right_complication_autocomplete_id',
                    'id' => 'right_complication_autocomplete_id',
                    'source' => "js:function(request, response) {
                                                        $.getJSON('".$defaultURL."/getPostOpComplicationAutocopleteList', {
                                                                term : request.term,
                                                                eye_id: '".\Eye::RIGHT."',
                                                                element_id: '".$this->id."',
                                                                operation_note_id: '".$operation_note_id."',
                                                                ajax: 'ajax',
                                                        }, response);
                                                                       
                                                    }",
                    'options' => array(
                        'select' => "js:function(event, ui) {
										addPostOpComplicationTr(ui.item.label, 'right-complication-list', ui.item.value, 0  );
										return false;
									}",
                    ),
                    'htmlOptions' => array(
                        'placeholder' => 'search for complications',
                    ),
                ));

                                ?>

                         <hr>
		</div>

                <div class="active-form">

                    <h5 class="right-recorded-complication-text recorded <?php echo $right_values ? '' : 'hide'?>">Recorded Complications</h5>
                    <h5 class="right-no-recorded-complication-text no-recorded <?php echo $right_values ? 'hide' : ''?>">No Recorded Complications</h5>

                    <?php echo $form->hiddenInput($element, 'id', false); ?>

                    <table id="right-complication-list" class="recorded-postop-complications" data-sideletter="R">
                    <?php
                        foreach ($right_values as $key => $value): ?>
                            <tr>
                                <td class=postop-complication-name><?php echo $value['name']; ?></td>
                                <td class='right'>
                                        <?php echo \CHtml::hiddenField("complication_items[R][$key]", $value['id'], array('id' => "complication_items_R_$key")); ?>
                                        <a class="postop-complication-remove-btn" href="javascript:void(0)">Remove</a>
                                </td></tr>

                    <?php endforeach; ?>
                    </table>

		</div>

		<div class="inactive-form">
			<div class="add-side">
				<a href="#">
					Add right side <span class="icon-add-side"></span>
				</a>
			</div>
		</div>
	</div>
	<div class="element-eye left-eye column right side" data-side="left">
		<div class="active-form">
			<a href="#" class="icon-remove-side remove-side">Remove side</a>
			<?php echo $form->dropDownList(OphCiExamination_PostOpComplications::model(), 'name', $left_eye_data,
                                    array(
                                        'empty' => array('-1' => '-- Select --'),
                                        'id' => 'left-complication-select',
                                    ),
                                    false, array('label' => 4, 'field' => 6));

                        $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                    'name' => 'left_complication_autocomplete_id',
                    'id' => 'left_complication_autocomplete_id',
                    'source' => "js:function(request, response) {
                                                        $.getJSON('".$defaultURL."/getPostOpComplicationAutocopleteList', {
                                                                term : request.term,
                                                                eye_id: '".\Eye::LEFT."',
                                                                element_id: '".$this->id."',
                                                                operation_note_id: '".$operation_note_id."',
                                                                ajax: 'ajax',
                                                        }, response);

                                                }",
                    'options' => array(
                        'select' => "js:function(event, ui) {
                                                                                console.log(ui);
										addPostOpComplicationTr(ui.item.label, 'left-complication-list', ui.item.value, 0  );
										return false;
									}",
                    ),
                    'htmlOptions' => array(
                        'placeholder' => 'search for complications',
                    ),
                ));

                        ?>
                        <hr>
                </div>

                <div class="active-form">

                    <h5 class="left-recorded-complication-text recorded <?php echo $left_values ? '' : 'hide'?>">Recorded Complications</h5>
                    <h5 class="left-no-recorded-complication-text no-recorded <?php echo $left_values ? 'hide' : ''?>">No Recorded Complications</h5>


                    <table id="left-complication-list" class="recorded-postop-complications" data-sideletter="L">
                    <?php
                        foreach ($left_values as $key => $value): ?>
                            <tr>
                                <td class=postop-complication-name><?php echo $value['name']; ?></td>
                                <td class='right'>
                                        <?php echo \CHtml::hiddenField("complication_items[L][$key]", $value['id'], array('id' => "complication_items_L_$key")); ?>
                                        <a class="postop-complication-remove-btn" href="javascript:void(0)">Remove</a>
                                </td>
                            </tr>

                    <?php endforeach; ?>
                    </table>
		</div>
		<div class="inactive-form">
			<div class="add-side">
				<a href="#">
					Add left side <span class="icon-add-side"></span>
				</a>
			</div>
		</div>

	</div>
</div>

<?php else: ?>

<div class="row field-row" id="div_Element_OphTrOperationnote_ProcedureList_id">
    <div class="large-12 column text-center">
        There are no recorded operations for this patient
    </div>
</div>

<?php endif;
