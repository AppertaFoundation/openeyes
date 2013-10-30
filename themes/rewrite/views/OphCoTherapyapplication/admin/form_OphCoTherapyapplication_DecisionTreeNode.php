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
<div class="element">
	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row question eventDetail">
		<div class="label"><?php echo $form->labelEx($model,'question'); ?></div>
		<div class="data"><?php echo $form->textField($model,'question',array('size'=>40,'maxlength'=>256, 'nowrapper' => true)); ?></div>
		<?php echo $form->error($model,'question'); ?>
	</div>

	<div class="row outcomeAdmin eventDetail">
		<div class="label"><?php echo $form->labelEx($model,'outcome_id'); ?></div>
		<div class="data"><?php echo $form->dropdownlist($model,'outcome_id',CHtml::listData(OphCoTherapyapplication_DecisionTreeOutcome::model()->findAll(),'id','name'),array('empty'=>'- Please select -', 'nowrapper' => true)); ?></div>
		<?php echo $form->error($model,'outcome_id'); ?>
	</div>

	<div class="row default_function  eventDetail">
		<div class="label"><?php echo $form->labelEx($model,'default_function'); ?></div>
		<div class="data">
		<?php
		$func_list = array();
		foreach ($model->getDefaultFunctions() as $func) {
			$func_list[$func] = $func;
		}

		echo $form->dropdownlist($model, 'default_function', $func_list, array('empty' => '- Please select -', 'nowrapper' => true)); ?>
		</div>
		<?php echo $form->error($model,'default_function'); ?>
	</div>

	<div class="row default_value eventDetail">
		<div class="label"><?php echo $form->labelEx($model,'default_value'); ?></div>
		<div class="data">
		<?php
			if ($model->response_type && $model->response_type->datatype == 'bool') {
				$this->renderPartial('template_OphCoTherapyapplication_DecisionTreeNode_default_value_bool',
						array('name' => get_class($model) . '[default_value]',
								'id' => get_class($model) . '_default_value',
								'val'=> $model->default_value,

						));
			} else {
				$this->renderPartial('template_OphCoTherapyapplication_DecisionTreeNode_default_value_default',
						array('name' => get_class($model) . '[default_value]',
						'id' => get_class($model) . '_default_value',
						'val'=> $model->default_value,
				));
			}
		?>
		</div>
		<?php echo $form->error($model,'default_value'); ?>
	</div>

	<div class="row response_type eventDetail">
		<div class="label"><?php echo $form->labelEx($model,'response_type'); ?></div>

		<div class="data">
		<?php
		$html_options = array(
				'options' => array(),
				'empty'=>'- Please select -',
				'nowrapper' => true
		);
		foreach (OphCoTherapyapplication_DecisionTreeNode_ResponseType::model()->findAll() as $rt) {
			$html_options['options'][(string) $rt->id] = array('data-datatype' => $rt->datatype);
		}

		echo $form->dropdownlist($model,'response_type_id',CHtml::listData(OphCoTherapyapplication_DecisionTreeNode_ResponseType::model()->findAll(),'id','label'),$html_options); ?>
		</div>
		<?php echo $form->error($model,'response_type'); ?>
	</div>
</div>

<script id="template_default_value_default" type="text/html">
	<?php
		$this->renderPartial('template_OphCoTherapyapplication_DecisionTreeNode_default_value_default',
						array('name' => '{{name}}',
						'id' => '{{id}}',
						'val'=> null
				));
	?>
</script>
<script id="template_default_value_bool" type="text/html">
	<?php
		$this->renderPartial('template_OphCoTherapyapplication_DecisionTreeNode_default_value_bool',
					array('name' => '{{name}}',
					'id' => '{{id}}',
					'val'=> null,
			));
	?>
</script>
