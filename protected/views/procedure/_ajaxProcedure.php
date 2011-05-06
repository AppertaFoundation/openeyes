<?php
$link = CHtml::link('remove', '#', array('onClick' => "js:removeProcedure(this);"));
$display = $data['term'] . ' - ' . $data['short_format'] . ' ' . $link; ?>
<tr>
	<?php echo CHtml::hiddenField('Procedures[]', $data['id']); ?>
	<td><?php echo $display; ?></td>
	<td><?php echo $data['duration']; ?></td>
</tr>