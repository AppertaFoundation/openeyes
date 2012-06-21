<?php if (@$htmlOptions['nowrapper']) {?>
	<?php echo CHtml::activeDropDownList($element,$field,$data,$htmlOptions)?>
<?php }else{?>
	<div id="div_<?php echo get_class($element)?>_<?php echo $field?>" class="eventDetail"<?php if (@$hidden) {?> style="display: none;"<?php }?>>
		<?php if (@$htmlOptions['layout'] == 'vertical') {?>
			<div class="label"></div>
			<div class="DropDownLabelVertical">
				<?php echo $element->getAttributeLabel($field)?>
			</div>
			<div class="label"></div>
		<?php }else{?>
			<?php if (!@$htmlOptions['nolabel']){?><div class="label"><?php echo $element->getAttributeLabel($field)?>:</div><?php }?>
		<?php }?>
		<div class="data">
			<?php if (@$htmlOptions['divided']) {?>
				<select name="<?php echo get_class($element)?>[<?php echo $field?>]" id="<?php echo get_class($element)?>_<?php echo $field?>">
					<?php if (isset($htmlOptions['empty'])) {?>
						<option value=""><?php echo $htmlOptions['empty']?></option>
					<?php }?>
					<?php foreach ($data as $i => $optgroup) {?>
						<?php if ($i>0) {?>
							<optgroup label="--------">
						<?php }?>
							<?php foreach ($optgroup as $id => $option) {?>
								<option value="<?php echo $id?>"<?php if ($id == $value) {?> selected="selected"<?php }?>><?php echo $option?></option>
							<?php }?>
						<?php if ($i>0) {?>
							</optgroup>
						<?php }?>
					<?php }?>
				</select>
			<?php }else{?>
				<?php echo CHtml::activeDropDownList($element,$field,$data,$htmlOptions)?>
			<?php }?>
		</div>
	</div>
<?php }?>
