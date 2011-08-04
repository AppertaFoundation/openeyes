<?php

Yii::app()->clientScript->registerCssFile(
	'/css/elements/ElementLetterOut/1.css',
	'screen, projection'
);

Yii::app()->clientScript->registerCssFile(
        '/css/elements/ElementLetterOut/1_print.css',
        'print'
);

?>
<div id="ElementLetterOut_layout">
	<br />
	<p class="ElementLetterOut_to"><?php echo nl2br(CHtml::encode($data->to_address)); ?></p>

	<p class="ElementLetterOut_date"><?php echo CHtml::encode($data->date); ?></p>

	<p class="ElementLetterOut_dear"><?php echo CHtml::encode($data->dear); ?></p>

	<p class="ElementLetterOut_re"><?php echo CHtml::encode($data->re); ?></p>

	<p class="ElementLetterOut_text"><?php echo nl2br(CHtml::encode($data->value)); ?></p>

	<p><?php echo nl2br(CHtml::encode($data->from_address)) ?></p>

	<p class="ElementLetterOut_cc"><?php echo nl2br(CHtml::encode($data->cc)); ?></p>
</div>
