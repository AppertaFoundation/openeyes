<?php
// Initialise any rendering variables here.
?>
<label class="inline highlight">
    <?php echo CHtml::activeCheckBox($model, "[$id]operation") . $model->getDisplayTitle() ?>
</label>