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

<script type="text/javascript">
    $(function(){
        var $textbox = $("#<?=CHtml::encode($model_name)?>_relationship_status");
        var $wrapper = $("#div_<?=CHtml::encode($model_name)?>_relationship_status");
         $('body').on('keydown','#<?=CHtml::encode($model_name)?>_relationship_status', function(e){
            if(($(this).val().length == "26") && (e.keyCode !== 8)){
                e.preventDefault();
                return false;
            }
        });
        var $radios = $("#div_<?=CHtml::encode($model_name)?>_signatory_person").find("input[type=radio]");
        $radios.change(function (e) {
            var selected = null;
            if($(this).prop("checked")) {
              selected = this.value;
              if(selected === "<?= \OEModule\OphTrConsent\models\Element_OphTrConsent_PatientSignature::SIGNATORY_PERSON_PARENT ?>") {
                $wrapper.show();
              }
              else {
                $textbox.val("");
                $wrapper.hide();
              }
            }
        });
        $radios.trigger("change");
    });
</script>
