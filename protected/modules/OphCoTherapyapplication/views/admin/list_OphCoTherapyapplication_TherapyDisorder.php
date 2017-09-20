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
?>
<div class="box admin">
	<h2><?php echo $title ?></h2>
	<?php $this->renderPartial('//base/_messages')?>
	<form id="admin_diagnoses">
		<table class="grid">
			<thead>
				<tr>
					<th><input type="checkbox" name="selectall" id="selectall" /></th>
					<th>Name</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($model_list as $i => $model) {?>
					<tr class="clickable" data-id="<?php echo $model->id?>">
						<td><input type="checkbox" name="diagnoses[]" value="<?php echo $model->id?>" /></td>
						<td>
							<?php if (!$parent_id) {?>
								<a href="<?php echo Yii::app()->createUrl($this->module->getName().'/admin/viewDiagnoses', array('parent_id' => $model->id))?>">
							<?php }?>
							<?php echo $model->disorder->term?>
							<?php if (!$parent_id) {?>
								</a>
							<?php }?>
						</td>
					</tr>
				<?php }?>
			</tbody>
			<tfoot class="pagination-container">
				<tr>
					<td colspan="2">
						<?php echo EventAction::button('Add', 'add2', null, array('class' => 'small'))->toHtml()?>
						<?php echo EventAction::button('Delete', 'delete', null, array('class' => 'small', 'data-uri' => '/OphCoTherapyapplication/admin/deleteDiagnoses', 'data-object' => 'diagnoses'))->toHtml()?>
					</td>
				</tr>
			</tfoot>
		</table>
	</form>
</div>
<div class="hidden" id="add-new-form" style="margin-bottom: 10px">
	<?php
    $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
            'id' => 'clinical-create',
            'enableAjaxValidation' => false,
            'action' => Yii::app()->createURL($this->module->getName().'/admin/addDiagnosis'),
    ));

    if ($parent_id) {
        echo CHtml::hiddenField('parent_id', $parent_id);
    }

    $form->widget('application.widgets.DiagnosisSelection', array(
            'field' => 'new_disorder_id',
            'layout' => 'minimal',
            'default' => false,
            'callback' => 'OphCoTherapyapplication_AddDiagnosis',
            'placeholder' => 'type the first few characters to search',
    ));

    echo CHtml::hiddenField('disorder_id', '', array('id' => 'disorder_id'));

    $this->endWidget();
    ?>
</div>
