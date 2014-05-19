<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

$form = $this->beginWidget('FormLayout', array('layoutColumns' => array('label' => 3, 'field' => 9),));

?>
<fieldset class="field-row">

	<legend><strong><?= $medication->isNewRecord ? "Add" : "Edit" ?> medication</strong></legend>

	<input type="hidden" name="medication_id" id="medication_id" value="<?= $medication->id ?>"/>
	<input type="hidden" name="patient_id" id="medication_id" value="<?= $patient->id ?>"/>

	<div class="field-row row">
		<div class="<?= $form->columns('label');?>">
			<label for="drug_id">Medication:</label>
		</div>
		<div class="<?= $form->columns('field');?>">

			<input type="hidden" name="drug_id" value="<?= $medication->drug_id ?>"/>
			<div class="field-row" id="medication_drug_name" style="font-weight: bold;"><?= $medication->drug ? CHtml::encode($medication->drug->name) : "" ?></div>

			<div class="field-row">
				<?= CHtml::dropDownList('drug_select','', Drug::model()->listBySubspecialty($firm->getSubspecialtyID()), array('empty' => '- Select -'))?>
			</div>

			<div class="field-row">
				<div class="label"></div>
				<?php

				$this->widget('zii.widgets.jui.CJuiAutoComplete',
					array(
						'name' => 'drug_autocomplete',
						'source' => new CJavaScriptExpression(
							'function (req, res) { $.getJSON(' . CJSON::encode($this->createUrl('medication/finddrug')) . ', req, res); }'
						),
						'htmlOptions' => array('placeholder' => 'or search formulary'),
					)
				);

				?>
			</div>
		</div>
	</div>

	<?php $form->widget('application.widgets.DropDownList', array('element' => $medication, 'field' => 'route_id', 'data' => 'DrugRoute', 'htmlOptions' => array('name' => 'route_id', 'empty' => '- Select -'))); ?>

	<div id="medication_route_option">
		<?php if ($medication->route) $this->renderPartial('route_option', array('medication' => $medication, 'route' => $medication->route)); ?>
	</div>

	<?php $form->widget('application.widgets.DropDownList', array('element' => $medication, 'field' => 'frequency_id', 'data' => 'DrugFrequency', 'htmlOptions' =>array('name' => 'frequency_id', 'empty' => '- Select -'))); ?>

	<div class="field-row row">
		<div class="<?= $form->columns('label');?>">
			<label for="start_date">Date from:</label>
		</div>
		<div class="<?= $form->columns(3, true);?>">
			<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
				'name'=>'start_date',
				'id'=>'start_date',
				'options'=>array(
					'showAnim'=>'fold',
					'dateFormat'=>Helper::NHS_DATE_FORMAT_JS
				),
			))?>
		</div>
	</div>

	<div id="medication_form_errors" class="alert-box alert hide"></div>

	<div class="buttons">
		<button type="button" id="medication_save" class="secondary small">Save</button>
		<button type="button" id="medication_cancel" class="warning small">Cancel</button>
	</div>
</fieldset>
<?php

$this->endWidget();
