<?php
    $model_name = CHtml::modelName($element);
    /** @var \OEModule\OphCoCvi\models\Element_OphCoCvi_PatientSignature $element */
?>
<div class="row field-row">
    <div class="large-2 column">
        <label><?php echo $element->getAttributeLabel("consented_for")?>:</label>
    </div>
    <div class="large-10 column end">
        <?php echo $form->radioBoolean($element, "consented_to_gp"); ?>
        <?php echo $form->radioBoolean($element, "consented_to_la"); ?>
        <?php echo $form->radioBoolean($element, "consented_to_rcop"); ?>
    </div>
</div>

