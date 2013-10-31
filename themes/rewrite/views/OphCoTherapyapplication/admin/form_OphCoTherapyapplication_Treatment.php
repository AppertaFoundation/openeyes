<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>


<h3><?php echo $model->isNewRecord ? 'Create' : 'Edit'; ?> Treatment</h3>

<?php echo $form->errorSummary($model); ?>

<?php echo $form->dropdownlist($model, 'drug_id', CHtml::listData($model->getTreatmentDrugs(), 'id', 'name'), array('empty' => '- Please select -', 'class' => 'clearfix'))?>

<?php echo $form->dropdownlist($model, 'decisiontree_id', CHtml::listData(OphCoTherapyapplication_DecisionTree::model()->findAll(),'id','name'),array('empty'=>'- Please select -'))?>

<?php echo $form->radioBoolean($model, 'contraindications_required', array(1 => 'Yes', 0 => 'No'), array('separator' => '&nbsp;')); ?>

<?php echo $form->textField($model, 'template_code') ?>
<span class="info">The template code is used to determine what form is attached to application email. Leave blank for the default behaviour.</span>

<hr />

<?php echo $form->textfield($model, 'intervention_name') ?>

<?php echo $form->textField($model, 'dose_and_frequency') ?>

<?php echo $form->textField($model, 'administration_route') ?>

<div id="div_OphCoTherapyapplication_Treatment_cost">
	<div class="label">Cost:</div>
	<div class="data" style="display: inline-block">
	<?php echo $form->textField($model, 'cost',  array('nowrapper' => true))?> per <?php echo $form->dropDownList($model, 'cost_type_id', CHtml::listData(OphCoTherapyapplication_Treatment_CostType::model()->findAll(), 'id', 'name'), array('nowrapper' => true)) ?>
	</div>
</div>

<div id="div_OphCoTherapyapplication_Treatment_monitoring">
	<div class="label">Monitoring Frequency:</div>
	<div class="data" style="display: inline-block">
	Every <?php echo $form->textField($model, 'monitoring_frequency',  array('nowrapper' => true))?> <?php echo $form->dropDownList($model, 'monitoring_frequency_period_id', CHtml::listData(Period::model()->findAll(), 'id', 'name'), array('nowrapper' => true)) ?>
	</div>
</div>

<?php echo $form->textArea($model, 'duration', array('rows' => 4, 'cols' => 40)) ?>
<?php echo $form->textArea($model, 'toxicity', array('rows' => 6, 'cols' => 60)) ?>
