<div id="layout">
	<br />
	<p class="to"><?php echo nl2br(CHtml::encode($data->to_address)); ?></p>

	<p class="date"><?php echo CHtml::encode($data->date); ?></p>

	<p class="dear"><?php echo CHtml::encode($data->dear); ?></p>

	<p class="re"><?php echo CHtml::encode($data->re); ?></p>

	<p class="text"><?php echo nl2br(CHtml::encode($data->value)); ?>

	<p class="from">Yours sincerely
	<br />
	<br />
	<br />
	<br />
	<?php echo nl2br(CHtml::encode($data->from_address)) ?></p>

	<p class="cc"><?php echo nl2br(CHtml::encode($data->cc)); ?></p>
</div>
