Letter out: <br />

	<div class="row">
		<?php echo $form->labelEx($model,'from_address'); ?>
		<?php echo $form->textArea($model,'from_address',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'from_address'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'date'); ?>
		<?php echo $form->textField($model, 'date'); ?>
		<?php echo $form->error($model,'date'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'dear'); ?>
		<?php echo $form->textField($model, 'dear'); ?>
		<?php echo $form->error($model,'dear'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'re'); ?>
		<?php echo $form->textField($model, 're'); ?>
		<?php echo $form->error($model,'re'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'value'); ?>
		<?php echo $form->textArea($model,'value',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'value'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'to_address'); ?>
		<?php echo $form->textArea($model,'to_address',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'to_address'); ?>
	</div>
