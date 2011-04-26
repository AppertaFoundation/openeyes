<?php
$display = $data['term'] . ' - ' . $data['short_format']; ?>
<tr>
	<?php echo CHtml::hiddenField('Procedures[]', $data['id']); ?>
	<td><?php echo $display; ?></td>
	<td><?php echo $data['duration']; ?></td>
</tr>