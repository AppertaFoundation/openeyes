<div id="<?php echo get_class($element); ?>" class="eventDetail"<?php if (isset($htmlOptions['div_style'])) {?> style="<?php echo $htmlOptions['div_style']?>"<?php }?>>
	<div class="label<?php if (isset($htmlOptions['layout'])) {?>-<?php echo $htmlOptions['layout']?><?php }?>"><?php echo @$htmlOptions['label']?>:</div>
	<div class="data">
		<?php foreach ($fields as $field) {?>
			<input type="hidden" name="<?php echo get_class($element)?>[<?php echo $field?>]" value="0" />
		<?php }?>
		<?php echo CHtml::activeDropDownList($element,'id',$options,array_merge($htmlOptions,array('class'=>'MultiSelectList')))?>
		<div style="background-color: #fff;">
			<ul style="list-style-type: none; padding-left: 10px;">
			</ul>
		</div>
	</div>
</div>
