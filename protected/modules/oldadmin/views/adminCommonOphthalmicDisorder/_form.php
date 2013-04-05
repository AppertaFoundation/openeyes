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
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'common-ophthalmic-disorder-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model, 'disorder_id'); ?>
<?php
if (isset($model->disorder)) {
	$term = $model->disorder->term;
} else {
	$term = '';
}

$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
    'name'=>'term',
    'value' => $term,
    'sourceUrl'=>array('//disorder/disorders'),
    'options'=>array(
        'minLength'=>'4',
    ),
    'htmlOptions'=>array(
        'style'=>'height:20px;',
        'value' => 'foo'
    ),
));
?>		<?php echo $form->error($model,'disorder_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'subspecialty_id'); ?>
		<?php echo $form->dropDownList($model,'subspecialty_id',$model->getSubspecialtyOptions()); ?>
		<?php echo $form->error($model,'subspecialty_id'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
