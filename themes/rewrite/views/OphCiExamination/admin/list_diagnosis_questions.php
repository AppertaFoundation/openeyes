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

<div class="box admin">

	<header class="box-header">
		<h2 class="box-title"><?php echo $title ? $title : "Examination Admin" ?></h2>
		<div class="box-actions">
			<a class="button small" href="<?php echo Yii::app()->createUrl('OphCiExamination/admin/create' . $model_class); ?>?disorder_id=<?php echo $disorder_id ?>">Add New</a>
		</div>
	</header>

	<div class="row field-row">
		<div class="large-2 column">
			<label for="question_disorder">Select disorder:</label>
		</div>
		<div class="large-6 column end">
			<?php
			echo CHtml::dropDownList('disorder_id', $disorder_id, CHtml::listData(Element_OphCiExamination_InjectionManagementComplex::model()->getAllDisorders(),'id','term'), array('empty'=>'- Please select -', 'id' => 'question_disorder'));
			?>
		</div>
	</div>


	<?php
	if (!$disorder_id) {
	?>
		<div class="alert-box">
			<strong>Please select a disorder to view the questions</strong>
		</div>
	<?php
	} elseif (count($model_list)) {
	?>
		<table class="grid">
			<thead>
				<tr>
					<th>Name</th>
					<th>Enabled</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ($model_list as $i => $model) {?>
					<tr data-attr-id="<?php echo $model->id?>" data-attr-name="Question">
						<td><a href="<?php echo Yii::app()->createUrl($this->module->getName() . '/admin/update' . get_class($model), array('id'=> $model->id)) ?>"><?php echo $model->question ?></a></td>
						<td>
							<input type="checkbox" class="model_enabled" <?php if ($model->enabled) { echo "checked"; }?> />
						</td>
					</tr>
				<?php }?>
			</tbody>
		</table>
	<?php } else { ?>
		<div class="alert-box">
			<strong>No questions set for this disorder</strong>
		</div>
	<?php } ?>
</div>