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

<?php
$form = $this->beginWidget('CActiveForm', array(
	'id' => 'site-and-firm-form',
	'action' => Yii::app()->createUrl('/site/changesiteandfirm'),
));
?>
	<?php echo CHtml::hiddenField('returnUrl',$this->returnUrl)?>

	<?php if ($this->subspecialty) {?>
		<?php echo CHtml::hiddenField('subspecialty_id',$this->subspecialty->id)?>
		<p>
			To add an event to this episode you must switch to a <?php echo $this->subspecialty->name?> firm.
		</p>
	<?php }?>

	<?php if ($this->support_services) {?>
		<?php echo CHtml::hiddenField('support_services',1)?>
		<p>
			To add an event to this episode you must switch to a support services firm.
		</p>
	<?php }?>

	<?php if ($this->patient) {?>
		<?php echo CHtml::hiddenField('patient_id',$this->patient->id)?>
	<?php }?>

	<?php
	if ($errors = $form->errorSummary($model)) {
		echo '<div>' . $errors . '</div>';
	}
	?>

	<div class="field-row row">
		<div class="large-3 column text-right">
			<?php echo $form->labelEx($model, 'site_id'); ?>
		</div>
		<div class="large-9 column">
			<?php echo $form->dropDownList($model,'site_id', $sites); ?>
		</div>
	</div>

	<div class="field-row row">
		<div class="large-3 column text-right">
			<?php echo $form->labelEx($model, 'firm_id'); ?>
		</div>
		<div class="large-9 column">
			<?php echo $form->dropDownList($model,'firm_id', $firms); ?>
		</div>
	</div>

	<div class="field-row row">
		<div class="large-9 large-offset-3 column">
			<?php echo CHtml::submitButton('Confirm', array('class' => 'secondary small')); ?>
		</div>
	</div>

<?php $this->endWidget(); ?>