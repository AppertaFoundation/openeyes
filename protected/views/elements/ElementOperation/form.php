<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

if ($this->nopost) {
	$selected_procedures = $model->procedures;
} else {
	$selected_procedures = array();

	if (isset($_POST['Procedures']) && is_array($_POST['Procedures'])) {
		foreach ($_POST['Procedures'] as $proc_id) {
			$selected_procedures[] = Procedure::model()->findByPk($proc_id);
		}
	}
}

if (!isset($_POST['ElementOperation']['decision_date'])) {
	if ($model->decision_date) {
		$_POST['ElementOperation']['decision_date'] = $model->decision_date;
	} else {
		$_POST['ElementOperation']['decision_date'] = date('j M Y',time());
	}
}
?>
					<script type="text/javascript" src="/js/element_operation.js"></script>
					<h4>Operation details</h4>

					<?php echo $form->radioButtons($model, 'eye_id', 'eye');?>

					<?php $this->widget('application.components.ProcedureSelection',array(
						'model' => $model,
						'subsections' => $subsections,
						'procedures' => $procedures,
						'newRecord' => $newRecord,
						'selected_procedures' => $selected_procedures
					));
					?>

					<?php echo $form->radioBoolean($model, 'consultant_required')?>
					<?php echo $form->radioButtons($model, 'anaesthetic_type_id', 'anaesthetic_type');?>
					<?php echo $form->radioBoolean($model, 'overnight_stay')?>

					<div id="site" class="eventDetail">
						<div class="label"><?php echo $form->label(ElementOperation::model(),'site_id'); ?></div>
						<div class="data">
							<?php 
							if (!$model->site_id) {
								$active_site_id = Yii::app()->request->cookies['site_id']->value;	
							} else {
								$active_site_id = $model->site_id;
							}
							echo CHtml::dropDownList('ElementOperation[site_id]', $active_site_id, Site::model()->getList());
							?>
						</div>
					</div>

					<?php echo $form->radioButtons($model, 'priority_id', 'priority')?>

					<div id="decisionDate" class="eventDetail">
						<div class="label"><?php echo CHtml::encode($model->getAttributeLabel('decision_date'))?>:</div>
						<div class="data">
							<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
								'name'=>'ElementOperation[decision_date]',
								'id'=>'ElementOperation_decision_date_0',
								// additional javascript options for the date picker plugin
								'options'=>array(
									'showAnim'=>'fold',
									'dateFormat'=>Helper::NHS_DATE_FORMAT_JS,
									'maxDate'=>'today'
								),
								'value' => $_POST['ElementOperation']['decision_date'],
								'htmlOptions'=>array('style'=>'width: 110px;')
							)); ?>
						</div>
					</div>

					<div id="addComments" class="eventDetail">
						<div class="label"><?php echo CHtml::encode($model->getAttributeLabel('comments'))?>:</div>
						<div class="data">
							<textarea rows="4" cols="50" name="ElementOperation[comments]" id="ElementOperation_comments"><?php echo strip_tags($model->comments)?></textarea>
						</div>
					</div>
