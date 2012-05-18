<?php if (!@$htmlOptions['nowrapper']) {?>
	<div id="div_<?php echo $id?>" class="eventDetail">
		<div class="data">
<?php }?>
		<select id="<?php echo $id?>"<?php if (@$htmlOptions['class']) {?> class="<?php echo $htmlOptions['class']?>"<?php }?><?php if (@$htmlOptions['disabled']) {?> disabled="disabled"<?php }?><?php if (@$htmlOptions['title']) {?> title="<?php echo $htmlOptions['title']?>"<?php }?>>
			<?php if (isset($htmlOptions['empty'])) {?>
				<option value=""><?php echo $htmlOptions['empty']?></option>
			<?php }?>
			<?php foreach ($options as $id => $option) {?>
				<option value="<?php echo $id?>"<?php if ($id == $selected_value) {?> selected="selected"<?php }?>><?php echo $option?></option>
			<?php }?>
		</select>
		<?php if (!@$htmlOptions['nowrapper']) {?>
	</div>
</div>
<?php }?>
