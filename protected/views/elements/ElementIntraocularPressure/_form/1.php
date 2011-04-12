<?php
$selectOptions = $model->getSelectOptions(); ?>
Intraocular Pressure:
<br />
<div class="row">
	<label for="ElementIntraocularPressure_value">Right Eye:</label>
	<?php echo CHtml::activeDropDownList($model, 'right_iop', $selectOptions); ?>
</div>
<div class="row">
	<label for="ElementIntraocularPressure_value">Left Eye:</label>
	<?php echo CHtml::activeDropDownList($model, 'left_iop', $selectOptions); ?>
</div>