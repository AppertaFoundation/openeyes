<?php if (isset($errors) && !empty($errors)) {?>
	<div class="alert-box alert with-icon">
		<p>Please fix the following input errors:</p>
		<ul>
			<?php foreach ($errors as $field => $errs) {?>
				<?php foreach ($errs as $err) {?>
					<li>
						<?php echo $err?>
					</li>
				<?php }?>
			<?php }?>
		</ul>
	</div>
<?php }?>
