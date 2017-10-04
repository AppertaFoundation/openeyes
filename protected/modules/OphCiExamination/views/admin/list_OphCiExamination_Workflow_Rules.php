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
<?php $this->renderPartial('//base/_messages')?>
<div class="hidden" id="add-new-form" style="margin-bottom: 10px">
	<?php
    $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
            'id' => 'clinical-create',
            'enableAjaxValidation' => false,
            'action' => Yii::app()->createURL($this->module->getName().'/admin/addEmailRecipient'),
    ));

    $this->endWidget();
    ?>
</div>
<div class="box admin">
	<h2><?php echo $title ?></h2>
	<form id="admin_workflowrules">
		<table class="grid">
			<thead>
				<tr>
					<th><input type="checkbox" name="selectall" id="selectall" /></th>
					<th>Subspecialty</th>
					<th>Firm</th>
					<th>Episode status</th>
					<th>Workflow</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($model_list as $i => $model) {
    ?>
					<tr class="clickable" data-id="<?php echo $model->id?>" data-uri="OphCiExamination/admin/editWorkflowRule/<?php echo $model->id?>">
						<td><input type="checkbox" name="workflowrules[]" value="<?php echo $model->id?>" /></td>
						<td>
							<?php echo $model->subspecialty ? $model->subspecialty->name : 'All'?>
						</td>
						<td>
							<?php echo $model->firm ? $model->firm->name : 'All'?>
						</td>
						<td>
							<?php echo $model->episode_status ? $model->episode_status->name : 'All'?>
						</td>
						<td>
							<?php echo $model->workflow->name?>
						</td>
					</tr>
				<?php 
}?>
			</tbody>
			<tfoot class="pagination-container">
				<tr>
					<td colspan="5">
						<?php echo EventAction::button('Add', 'add', null, array('class' => 'small', 'data-uri' => '/OphCiExamination/admin/addWorkflowRule'))->toHtml()?>
						<?php echo EventAction::button('Delete', 'delete', null, array('class' => 'small', 'data-uri' => '/OphCiExamination/admin/deleteWorkflowRules', 'data-object' => 'workflowrules'))->toHtml()?>
					</td>
				</tr>
			</tfoot>
		</table>
	</form>
</div>
