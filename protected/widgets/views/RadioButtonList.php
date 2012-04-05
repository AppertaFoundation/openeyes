<div id="<?php echo $field?>" class="eventDetail"<?php if ($hidden) {?> style="display: none;"<?php }?>>
	<div class="label"><?php echo CHtml::encode($element->getAttributeLabel($field)); ?>:</div>
	<div class="data">
		<?php $i=0; ?>
		<?php foreach ($data as $id => $value) {?>
			<span class="group">
				<?php echo CHtml::radioButton($name, $element->$field == $id,array('value' => $id))?>
				<label for="<?php echo get_class($element)?>_<?php echo $field?>_<?php echo $id?>"><?php echo $value?></label>
			</span>
			<?php
			if ($maxwidth) {
				$i++;
				if ($i >= $maxwidth) {
					echo "<br />";
					$i=0;
				}
			}
			?>
		<?php }?>
	</div>
</div>
