<?php
if($flash_messages = Yii::app()->user->getFlashes()) {
	ksort($flash_messages);
	foreach($flash_messages as $flash_key => $flash_message) {
		$parts = explode('.', $flash_key);
		$class = isset($parts[1]) ? $parts[0] : 'info';
		$id = isset($parts[1]) ? $parts[1] : $parts[0];
		?>
<div id="flash-<?php echo $id; ?>"
	class="alertBox flash-<?php echo $class; ?>">
	<?php echo $flash_message; ?>
</div>
<?php } 
} ?>
