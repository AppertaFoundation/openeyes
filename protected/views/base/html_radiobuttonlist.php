<div id="<?php echo $field?>" class="eventDetail">
	<div class="label"><?php echo CHtml::encode($element->getAttributeLabel($field)); ?>:</div>
	<div class="data">
		<?php foreach ($data as $id => $value) {?>
			<span class="group">
				<input id="<?php echo get_class($element)?>_<?php echo $field?>_<?php echo $id?>" <?php if ($element->$field == $id){?>checked="checked" <?php }?>value="<?php echo $id?>" type="radio" name="<?php echo $name?>" />
				<label for="<?php echo get_class($element)?>_<?php echo $field?>_<?php echo $id?>"><?php echo $value?></label>
			</span>
		<?php }?>
	</div>
</div>
