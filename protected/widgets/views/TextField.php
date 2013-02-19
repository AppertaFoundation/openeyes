<?php if (@$htmlOptions['nowrapper']) {?>
	<?php echo CHtml::textField($name, $value, $htmlOptions)?>
<?php }else{?>
	<div id="div_<?php echo get_class($element)?>_<?php echo $field?>" class="eventDetail" <?php if (@$htmlOptions['hide']) {?> style="display: none;"<?php }?>>
		<div class="label"><?php echo empty($htmlOptions['label']) ? CHtml::encode($element->getAttributeLabel($field)) : $htmlOptions['label']?>:</div>
		<div class="data">
			<?php echo CHtml::textField($name, $value, $htmlOptions)?>
			<?php echo @$htmlOptions['append_text']?>
		</div>
	</div>
<?php }?>
