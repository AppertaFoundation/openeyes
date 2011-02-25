HPC:

<br />

<?php echo $form->errorSummary($model); ?>

<div class="row">
	<?php echo $form->textArea($model,'description',array('rows'=>6, 'cols'=>50)); ?>
	<?php echo $form->error($model,'description'); ?>
</div>
