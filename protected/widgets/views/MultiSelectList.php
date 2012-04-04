<script type="text/javascript"> var multiSelectField = '<?php echo $field?>'; </script>
<input type="hidden" name="<?php echo get_class($element)?>[MultiSelectList_<?php echo $field?>]" />
<div id="<?php echo get_class($element); ?>" class="eventDetail"<?php if (isset($htmlOptions['div_style'])) {?> style="<?php echo $htmlOptions['div_style']?>"<?php }?>>
	<div class="label<?php if (isset($htmlOptions['layout'])) {?>-<?php echo $htmlOptions['layout']?><?php }?>"><?php echo @$htmlOptions['label']?>:</div>
	<div class="data">
		<select label="<?php echo $htmlOptions['label']?>" class="MultiSelectList" name="">
			<option value=""><?php echo $htmlOptions['empty']?></option>
			<?php foreach ($filtered_options as $value => $option) {?>
				<option value="<?php echo $value?>"><?php echo $option?></option>
			<?php }?>
		</select>
		<div style="background-color: #fff;">
			<ul style="list-style-type: none; padding-left: 10px;">
				<?php foreach ($selected_ids as $id) {?>
					<li>
						<?php echo $options[$id]?> (<a href="#" class="MultiSelectRemove <?php echo $id?>">remove</a>)
					</li>
					<input type="hidden" name="<?php echo $field?>[]" value="<?php echo $id?>" />
				<?php }?>
			</ul>
		</div>
	</div>
</div>
