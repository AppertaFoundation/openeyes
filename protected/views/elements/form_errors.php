<?php if (isset($errors) && !empty($errors)) {?>
	<div id="clinical-create_es_" class="alertBox">
		<p>Please fix the following input errors:</p>
		<?php foreach ($errors as $field => $errs) {?>
			<ul>
				<li>
					<?php echo $field.': '.$errs[0]?>
				</li>
			</ul>
		<?php }?>
	</div>
<?php }?>
