<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
Yii::app()->assetManager->registerScriptFile('js/risks.js');
?>
<section class="box patient-info associated-data js-toggle-container">
	<header class="box-header">
		<h3 class="box-title">
			<span class="icon-patient-clinician-hd_flag"></span>
			Risks
		</h3>
		<a href="#" class="toggle-trigger toggle-hide js-toggle">
			<span class="icon-showhide">
				Show/hide this section
			</span>
		</a>
	</header>
	<div class="js-toggle-body">

		<p class="risk-status-unknown" <?php if (!(empty($this->patient->riskAssignments)) || $this->patient->no_risks_date) {
            echo 'style="display: none;"';
        } ?>>Patient risk status is unknown</p>

		<p class="risk-status-none" <?php if (!$this->patient->no_risks_date) {
            echo 'style="display: none;"';
        } ?>>Patient has no known risks</p>

		<table class="plain patient-data" id="currentRisks" <?php if (empty($this->patient->riskAssignments)) {
            echo 'style="display: none;"';
        } ?>>
			<thead>
			<tr>
				<th>Risks</th>
				<th>Comments</th>
				<?php if ($this->checkAccess('OprnEditAllergy')) { ?>
					<th>Actions</th><?php } ?>
			</tr>
			</thead>
			<tbody>
			<?php foreach ($this->patient->riskAssignments as $ra) { ?>
				<tr data-assignment-id="<?= $ra->id ?>" data-risk-id="<?= $ra->risk->id ?>"
					data-risk-name="<?= $ra->risk->name ?>">
					<td><?= CHtml::encode($ra->name) ?></td>
					<td><?= CHtml::encode($ra->comments) ?></td>
					<?php if ($this->checkAccess('OprnEditAllergy')) { ?>
						<td>
							<a href="#" rel="<?php echo $ra->id ?>" class="small removeRisk">
								Remove
							</a>
						</td>
					<?php } ?>
				</tr>
			<?php } ?>
			</tbody>
		</table>
		<?php

        if ($this->checkAccess('OprnEditAllergy')) { ?>
			<div class="box-actions">
				<button id="btn-add_risk" class="secondary small">
					Edit
				</button>
			</div>

			<div id="add_risk" style="display: none;">
				<?php
                $form = $this->beginWidget('FormLayout', array(
                    'id' => 'add-risk',
                    'enableAjaxValidation' => false,
                    'htmlOptions' => array('class' => 'form add-data'),
                    'action' => array('patient/addRisk'),
                    'layoutColumns' => array(
                        'label' => 3,
                        'field' => 9,
                    ),
                )) ?>

				<div
					class="risks_confirm_no field-row row" <?php if ($this->patient->hasRiskStatus() && !$this->patient->no_risks_date) {
                    echo 'style="display: none;"';
                } ?>>
					<div class="risks">
						<div class="<?php echo $form->columns('label'); ?>">
							<label for="no_risks">Confirm patient has no risks:</label>
						</div>
						<div class="<?php echo $form->columns('field'); ?>">
							<?php echo CHtml::checkBox('no_risks', $this->patient->no_risks_date ? true : false); ?>
						</div>
					</div>
				</div>

				<input type="hidden" name="edit_risk_id" id="edit_risk_id" value=""/>
				<input type="hidden" name="patient_id" value="<?php echo $this->patient->id ?>"/>

				<div class="row field-row risk_field" <?php if ($this->patient->no_risks_date) {
                    echo 'style="display: none;"';
                } ?>>
					<div class="<?php echo $form->columns('label'); ?>">
						<label for="risk_id">Add risk:</label>
					</div>
					<div class="<?php echo $form->columns('field'); ?>">
						<?php echo CHtml::dropDownList('risk_id', null,
                            CHtml::listData($this->riskList(), 'id', 'name'), array('empty' => '-- Select --')) ?>
					</div>
				</div>
				<div id="risk_other" class="row field-row hidden">
					<div class="<?php echo $form->columns('label'); ?>">
						<label for="risk_id">Other risk:</label>
					</div>
					<div class="<?php echo $form->columns('field'); ?>">
						<?= CHtml::textField('other', '',
                            array('autocomplete' => Yii::app()->params['html_autocomplete'])); ?>
					</div>
				</div>
				<div class="field-row row risk_field" <?php if ($this->patient->no_risks_date) {
                    echo 'style="display: none;"';
                } ?>>
					<div class="<?php echo $form->columns('label'); ?>">
						<label for="comments">Comments:</label>
					</div>
					<div class="<?php echo $form->columns('field'); ?>">
						<?php echo CHtml::textField('comments', '',
                            array('autocomplete' => Yii::app()->params['html_autocomplete'])) ?>
					</div>
				</div>

				<div class="buttons">
					<img src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif') ?>"
						 class="add_risk_loader" style="display: none;"/>
					<button class="secondary small btn_save_risk" type="submit">Save</button>
					<button class="warning small btn_cancel_risk" type="submit">Cancel</button>
				</div>

				<?php $this->endWidget() ?>
			</div>
		<?php } ?>
	</div>
</section>

<?php if (BaseController::checkAccess('OprnEditAllergy')) { ?>

	<!-- Confirm deletion dialog -->
	<div id="confirm_remove_risk_dialog" title="Confirm remove risk" style="display: none;">
		<div id="delete_risk">
			<div class="alert-box alert with-icon">
				<strong>WARNING: This will remove the risk from the patient record.</strong>
			</div>
			<p>
				<strong>Are you sure you want to proceed?</strong>
			</p>

			<div class="buttons">
				<input type="hidden" id="remove_risk_id" value=""/>
				<button type="submit" class="warning small btn_remove_risk">Remove risk</button>
				<button type="submit" class="secondary small btn_cancel_remove_risk">Cancel</button>
				<img class="loader" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif') ?>"
					 alt="loading..." style="display: none;"/>
			</div>
		</div>
	</div>
<?php } ?>
