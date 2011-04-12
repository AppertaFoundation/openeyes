<?php
$retinopathyOptions = $model->getSelectOptions(NSCGrade::RETINOPATHY);
$maculopathyOptions = $model->getSelectOptions(NSCGrade::MACULOPATHY); ?>
NSC Grade:
<br />
<div class="row">
	<label for="ElementNSCGrade_value">Retinopathy:</label>
	<?php echo CHtml::activeDropDownList($model, 'retinopathy_grade_id', $retinopathyOptions); ?>
</div>
<div class="row">
	<label for="ElementNSCGrade_value">Maculopathy:</label>
	<?php echo CHtml::activeDropDownList($model, 'maculopathy_grade_id', $maculopathyOptions); ?>
</div>