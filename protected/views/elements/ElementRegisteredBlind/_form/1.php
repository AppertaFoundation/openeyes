<?php
$selectOptions = $model->getSelectOptions(); ?>
Registered Blind:
<br />
<div class="row">
	<label for="ElementRegisteredBlind_value">Registered:</label>
	<?php echo CHtml::activeDropDownList($model, 'status', $selectOptions); ?>
</div>