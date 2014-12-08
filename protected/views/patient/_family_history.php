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

Yii::app()->assetManager->registerScriptFile('js/family_history.js');
?>

<section class="box patient-info associated-data js-toggle-container">
	<header class="box-header">
		<h3 class="box-title">
			<span class="icon-patient-clinician-hd_flag"></span>
			Family History
		</h3>
		<a href="#" class="toggle-trigger toggle-hide js-toggle">
			<span class="icon-showhide">
				Show/hide this section
			</span>
		</a>
	</header>
	<div class="js-toggle-body">

		<p class="family-history-status-none" <?php if (!$this->patient->no_family_history_date) { echo 'style="display: none;"'; }?>>Patient has no known family history</p>

		<p class="family-history-status-unknown"  <?php if (!empty($this->patient->familyHistory) || $this->patient->no_family_history_date) { echo 'style="display: none;"'; }?>>Patient family history is unknown</p>

		<table id="currentFamilyHistory" class="plain patient-data" <?php if (empty($this->patient->familyHistory)) { echo 'style="display: none;"'; }?>>
			<thead>
			<tr>
				<th>Relative</th>
				<th>Side</th>
				<th>Condition</th>
				<th>Comments</th>
				<?php if ($this->checkAccess('OprnEditFamilyHistory')) { ?><th>Actions</th><?php } ?>
			</tr>
			</thead>
			<tbody>
			<?php foreach ($this->patient->familyHistory as $history) {?>
				<tr>
					<td class="relative" data-relativeId="<?= $history->relative_id ?>"><?php echo $history->getRelativeName(); ?></td>
					<td class="side"><?php echo $history->side->name?></td>
					<td class="condition" data-conditionId="<?= $history->condition_id ?>"><?php echo $history->getConditionName(); ?></td>
					<td class="comments"><?php echo CHtml::encode($history->comments)?></td>
					<?php if ($this->checkAccess('OprnEditFamilyHistory')): ?>
						<td>
							<a href="#" class="editFamilyHistory" rel="<?php echo $history->id?>">Edit</a>&nbsp;&nbsp;
							<a href="#" class="removeFamilyHistory" rel="<?php echo $history->id?>">Remove</a>
						</td>
					<?php endif ?>
				</tr>
			<?php }?>
			</tbody>
		</table>


		<?php if ($this->checkAccess('OprnEditFamilyHistory')) { ?>
			<div class="box-actions">
				<button id="btn-add_family_history" class="secondary small">
					Add Family History
				</button>
			</div>
			<div id="add_family_history" style="display: none;">

				<?php
				$form = $this->beginWidget('FormLayout', array(
					'id'=>'add-family_history',
					'enableAjaxValidation'=>false,
					'htmlOptions' => array('class'=>'sliding form add-data'),
					'action'=>array('patient/addFamilyHistory'),
					'layoutColumns'=>array(
						'label' => 3,
						'field' => 9
					),
				))?>

					<fieldset class="field-row">

						<legend><strong>Add family history</strong></legend>

						<input type="hidden" name="edit_family_history_id" id="edit_family_history_id" value="" />
						<input type="hidden" name="patient_id" value="<?php echo $this->patient->id?>" />

						<div class="family-history-confirm-no field-row row" <?php if (!empty($this->patient->familyHistory)) { echo 'style="display: none;"'; }?>>
						<div class="familyHistory">
							<div class="<?php echo $form->columns('label');?>">
								<label for="no_family_history">Confirm patient has no family history:</label>
							</div>
							<div class="<?php echo $form->columns('field');?>">
								<?php echo CHtml::checkBox('no_family_history', $this->patient->no_family_history_date ? true : false); ?>
							</div>
						</div>
						</div>

						<div class="family_history_field field-row" <?php if ($this->patient->no_family_history_date) { echo 'style="display: none;"'; }?>>

						<div class="field-row row">
							<div class="<?php echo $form->columns('label');?>">
								<label for="relative_id">Relative:</label>
							</div>
							<div class="<?php echo $form->columns('field');?>">
								<?php
								$relatives = FamilyHistoryRelative::model()->findAll(array('order'=>'display_order'));
								$relatives_opts = array(
										'options' => array(),
										'empty'=>'- select -',
								);
								foreach ($relatives as $rel) {
									$relatives_opts['options'][$rel->id] = array('data-other' => $rel->is_other ? '1' : '0');
								}
								echo CHtml::dropDownList('relative_id','',CHtml::listData($relatives,'id','name'),$relatives_opts)
								?>
							</div>
						</div>

						<div class="field-row row hidden" id="family-history-o-rel-wrapper">
							<div class="<?php echo $form->columns('label');?>">
								<label for="comments">Other Relative:</label>
							</div>
							<div class="<?php echo $form->columns('field');?>">
								<?php echo CHtml::textField('other_relative','')?>
							</div>
						</div>

						<div class="field-row row">
							<div class="<?php echo $form->columns('label');?>">
								<label for="side_id">Side:</label>
							</div>
							<div class="<?php echo $form->columns('field');?>">
								<?php echo CHtml::dropDownList('side_id','',CHtml::listData(FamilyHistorySide::model()->findAll(array('order'=>'display_order')),'id','name'))?>
							</div>
						</div>

						<div class="field-row row">
							<div class="<?php echo $form->columns('label');?>">
								<label for="condition_id">Condition:</label>
							</div>
							<div class="<?php echo $form->columns('field');?>">
								<?php
								$conditions = FamilyHistoryCondition::model()->findAll(array('order'=>'display_order'));
								$conditions_opts = array(
										'options' => array(),
										'empty'=>'- select -',
								);
								foreach ($conditions as $con) {
									$conditions_opts['options'][$con->id] = array('data-other' => $con->is_other ? '1' : '0');
								}
								echo CHtml::dropDownList('condition_id','',CHtml::listData($conditions,'id','name'),$conditions_opts);
								?>
							</div>
						</div>

						<div class="field-row row hidden" id="family-history-o-con-wrapper">
							<div class="<?php echo $form->columns('label');?>">
								<label for="comments">Other Condition:</label>
							</div>
							<div class="<?php echo $form->columns('field');?>">
								<?php echo CHtml::textField('other_condition','')?>
							</div>
						</div>

						<div class="field-row row">
							<div class="<?php echo $form->columns('label');?>">
								<label for="comments">Comments:</label>
							</div>
							<div class="<?php echo $form->columns('field');?>">
								<?php echo CHtml::textField('comments','',array('autocomplete'=>Yii::app()->params['html_autocomplete']))?>
							</div>
						</div>

						</div>

						<div class="buttons">
							<img src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" class="add_family_history_loader" style="display: none;" />
							<button type="submit" class="secondary small btn_save_family_history">
								Save
							</button>
							<button class="warning small btn_cancel_family_history">
								Cancel
							</button>
						</div>

					</fieldset>
				<?php $this->endWidget()?>
			</div>
		<?php }?>
	</div>
</section>

<!-- Confirm deletion dialog -->
<div id="confirm_remove_family_history_dialog" title="Confirm remove family history" style="display: none;">
	<div id="delete_family_history">
		<div class="alert-box alert with-icon">
			<strong>WARNING: This will remove the family history from the patient record.</strong>
		</div>
		<p>
			<strong>Are you sure you want to proceed?</strong>
		</p>
		<div class="buttons">
			<input type="hidden" id="family_history_id" value="" />
			<button type="submit" class="warning small btn_remove_family_history">Remove family history</button>
			<button type="submit" class="secondary small btn_cancel_remove_family_history">Cancel</button>
			<img class="loader" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
		</div>
	</div>
</div>