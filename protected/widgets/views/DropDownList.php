<?php if (@$htmlOptions['nowrapper']) {?>
	<?php echo CHtml::activeDropDownList($element,$field,$data,$htmlOptions)?>
<?php }else{?>
	<div id="div_<?php echo get_class($element)?>_<?php echo $field?>" class="eventDetail"<?php if (@$hidden) {?> style="display: none;"<?php }?>>
		<div class="label"><?php echo $element->getAttributeLabel($field)?>:</div>
		<div class="data">
			<?php echo CHtml::activeDropDownList($element,$field,$data,$htmlOptions)?>
		</div>
	</div>
<?php }?>
