Referred from screening:<br />

<div class="view">
	<b><?php echo CHtml::encode($data->getAttributeLabel('referred')); ?>:</b>
	<?php
		if ($data->referred) {
			echo "Yes";
		} else {
			echo "No";
		}
	?>
	<br />

</div>
