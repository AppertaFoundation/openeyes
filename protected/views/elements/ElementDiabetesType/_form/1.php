<?php
$selectOptions = $model->getSelectOptions(); ?>
Diabetes Type:
<br />
<div class="row">
	<label for="ElementDiabetesType_value">Type of Diabetes:</label>
	<?php echo CHtml::activeDropDownList($model, 'type', $selectOptions); ?>
</div>