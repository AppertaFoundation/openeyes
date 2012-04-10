<div id="div_<?php echo get_class($element); ?>" class="eventDetail">
	<?php foreach ($fields as $i => $field) {?>
		<div class="labelrow<?php if ($i==0) {?>-first<?php }?>"><?php echo $element->getAttributeLabel($field); ?>:</div>
		<div class="datarow<?php if ($i==0) {?>-first<?php }?>">
			<?php echo CHtml::activeDropDownList($element,$field,$datas[$i],$htmlOptions[$i])?>
		</div>
	<?php }?>
</div>
