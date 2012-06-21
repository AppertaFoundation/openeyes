<input type="hidden" name="<?php echo get_class($element)?>[MultiSelectList_<?php echo $field?>]" />
<div id="div_<?php echo get_class($element)?>_<?php echo @$htmlOptions['label']?>" class="eventDetail"<?php if ($hidden) {?> style="display: none;"<?php }?>>
	<div class="label"><?php echo @$htmlOptions['label']?>:</div>
	<div class="data">
		<select label="<?php echo $htmlOptions['label']?>" class="MultiSelectList" name="">
			<option value=""><?php echo $htmlOptions['empty']?></option>
			<?php foreach ($filtered_options as $value => $option) {?>
				<option value="<?php echo $value?>"><?php echo $option?></option>
			<?php }?>
		</select>
		<div class="MultiSelectList">
			<ul class="MultiSelectList">
				<?php foreach ($selected_ids as $id) {
					if (isset($options[$id])) {?>
						<li>
							<?php echo $options[$id]?> (<a href="#" class="MultiSelectRemove <?php echo $id?>">remove</a>)
						</li>
						<input type="hidden" name="<?php echo $field?>[]" value="<?php echo $id?>" />
					<?php }?>
				<?php }?>
			</ul>
		</div>
	</div>
</div>
