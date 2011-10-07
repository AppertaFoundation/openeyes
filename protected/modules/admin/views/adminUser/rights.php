<?php
/*
_____________________________________________________________________________
(C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
(C) OpenEyes Foundation, 2011
This file is part of OpenEyes.
OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
_____________________________________________________________________________
http://www.openeyes.org.uk   info@openeyes.org.uk
--
*/

$this->breadcrumbs=array(
	'Users'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List User', 'url'=>array('index')),
	array('label'=>'Create User', 'url'=>array('create')),
	array('label'=>'View User', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage User', 'url'=>array('admin')),
	array('label'=>'User Rights', 'url'=>array('view', 'id'=>$model->id)),
);
?>

<h1>User Rights for <?php echo $model->last_name; ?></h1>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'user-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

<?php

	foreach ($rights as $service) {
?>
	<div class="row">
------------------
		<?php echo CHtml::label($service['name'], $service['label']); ?>
		<?php echo CHtml::checkBox($service['label'], $service['checked']); ?>

<?php
		foreach ($service['firms'] as $firm) {
?>
			<?php echo CHtml::label($firm['name'], $firm['label']); ?>
			<?php echo CHtml::checkBox($firm['label'], $firm['checked']); ?>
<?php
		}
?>
	</div>
<?php
	}
?>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Update Rights'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
