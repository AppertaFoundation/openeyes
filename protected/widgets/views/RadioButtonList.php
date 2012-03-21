<div id="<?php echo $field?>" class="eventDetail">
	<div class="label"><?php echo CHtml::encode($element->getAttributeLabel($field)); ?>:</div>
	<div class="data">
		<?php foreach ($data as $id => $value) {?>
			<span class="group">
				<?php echo CHtml::radioButton($name, $element->$field == $id)?>
				<label for="<?php echo get_class($element)?>_<?php echo $field?>_<?php echo $id?>"><?php echo $value?></label>
			</span>
		<?php }?>
	</div>
</div>
