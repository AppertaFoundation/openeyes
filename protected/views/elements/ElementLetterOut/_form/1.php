<h2 class="element_letterout">Letter out</h2>

<div class="row_element_letterout">
	<h3 class="element_letterout"><?php echo $form->labelEx($model,'from_address'); ?></h3 class="element_letterout">
	<span class="element_letterout_left">...
	</span>
	<span class="element_letterout_right">
		<?php echo $form->textArea($model,'from_address',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'from_address'); ?>
	</span>
</div>

<div class="row_element_letterout">
	<h3 class="element_letterout"><?php echo $form->labelEx($model,'date'); ?></h3 class="element_letterout">
	<span class="element_letterout_left">...
	</span>
	<span class="element_letterout_right">
		<?php echo $form->textField($model, 'date'); ?>
		<?php echo $form->error($model,'date'); ?>
	</span>
</div>
	
<div class="row_element_letterout">
	<h3 class="element_letterout"><?php echo $form->labelEx($model,'dear'); ?></h3 class="element_letterout">
	<span class="element_letterout_left">...
	</span>
	<span class="element_letterout_right">
		<?php echo $form->textField($model, 'dear'); ?>
		<?php echo $form->error($model,'dear'); ?>
	</span>
</div>

<div class="row_element_letterout">
	<h3 class="element_letterout"><?php echo $form->labelEx($model,'re'); ?></h3 class="element_letterout">
	<span class="element_letterout_left">...
	</span>
	<span class="element_letterout_right">
		<?php echo $form->textField($model, 're'); ?>
		<?php echo $form->error($model,'re'); ?>
	</span>
</div>

<div class="row_element_letterout">
	<h3 class="element_letterout"><?php echo $form->labelEx($model,'value'); ?></h3 class="element_letterout">
	<span class="element_letterout_left">...
	</span>
	<span class="element_letterout_right">
		<?php echo $form->textArea($model,'value',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'value'); ?>
	</span>
</div>

<div class="row_element_letterout">
	<h3 class="element_letterout"><?php echo $form->labelEx($model,'to_address'); ?></h3 class="element_letterout">
	<span class="element_letterout_left">...
	</span>
	<span class="element_letterout_right">
		<?php echo $form->textArea($model,'to_address',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'to_address'); ?>
	</span>
</div>

