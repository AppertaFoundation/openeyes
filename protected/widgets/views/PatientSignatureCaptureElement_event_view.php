<?php
    /** @var Element_PatientSignature $element */
    $modelName = CHtml::modelName($element);
    $tokenName = Yii::app()->request->csrfTokenName;
    $token = Yii::app()->request->csrfToken;
    $module=Yii::app()->controller->module->id;
$onSubmitJS = <<<JS
function(data) {
    $.get("/{$module}/default/signPatientSignatureElement?element_id={$element->id}&element_type_id={$element->getElementType()->id}&file_id="+data,
      function(result) {
        if(result === "0") {
          window.formHasChanged = false;  
          new OpenEyes.UI.Dialog({
              content: "We're sorry, something went wrong when attempting to save this signature. Please reload the page and try again."
            }).open();
        }
        else {
            window.formHasChanged = false;
            setTimeout(function () {
                window.location.reload();
            }, 300);  
        }
    });
     
}
JS;
?>

<div class="element-data">
    <?php if ($element->required_checkbox_visible) : ?>
        <div class="row data-row">
            <div class="large-3 column">
                <div class="data-label"><?php echo $element->getAttributeLabel('signatory_required')?>:</div>
            </div>
            <div class="large-9 column">
                <div class="data-value"><?php echo ($element->signatory_required ? "Yes" : "No") ?></div>
            </div>
        </div>
    <?php endif; ?>
    <?php if ($element->signatory_required) : ?>
        <div class="row data-row">
            <div class="large-3 column">
                <div class="data-label"><?php echo $element->getAttributeLabel('signature_date')?>:</div>
            </div>
            <div class="large-9 column">
                <div class="data-value" id="<?=$modelName?>_signature_date_text">
                    <?php
                    if ($element->isSigned()) {
                        $time = strtotime($element->signature_date);
                        if ($time) {
                            echo date("j M Y, H:i", $time);
                        }
                    } else {
                        echo "-";
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php if (count($element->getSignatoryPersonOptions()) > 1) : ?>
        <div class="row data-row">
            <div class="large-3 column">
                <div class="data-label"><?php echo $element->getAttributeLabel('signatory_person')?>:</div>
            </div>
            <div class="large-9 column">
                <div class="data-value"><?php echo $element->getSignedBy()?></div>
            </div>
        </div>
        <?php endif; ?>
        <?php if ($element->signatory_name != "") : ?>
            <div class="row data-row">
                <div class="large-3 column">
                    <div class="data-label"><?php echo $element->getAttributeLabel('signatory_name')?>:</div>
                </div>
                <div class="large-9 column">
                    <div class="data-value"><?php echo CHtml::encode($element->signatory_name)?></div>
                </div>
            </div>
        <?php endif; ?>
        <div class="row data-row">
            <div class="large-3 column">
                <div class="data-label"><?=$element->getAttributeLabel('protected_file_id')?>:</div>
            </div>
            <div class="large-9 column">
                <div class="data-value">
                    <img id="<?=$modelName?>_signature_image" src="<?=$element->protected_file_id ? "/ProtectedFile/view?id=".$element->protected_file_id."&name=signature.jpg" : ""?>" style="<?php if (!$element->protected_file_id) {
                        echo "display:none;";
                             }?> max-height: 170px;" alt="Signature"/>
                    <?php if ($element->can_be_signed_in_view_mode && is_null($element->protected_file_id)) : ?>
                        <div id="<?=$modelName?>_no-signature-note">
                        <p>No signature has been captured yet.</p>
                        <?php $this->widget('SignatureCapture', [
                            'buttonText' => 'Capture signature now',
                            'submitURL'=> "/ProtectedFile/uploadBase64Image?name=signature.jpg",
                            'onSubmit' => $onSubmitJS,
                            'showMessage' => false,
                            'element' => $element
                        ]); ?>
                        <?php if (!$element->isNewRecord) : ?>
                        <button id="<?=$modelName?>_requestSignature"
                                type="button"
                                data-element_type_id="<?=$element->elementType->id?>"
                                data-event_id="<?=$element->event_id?>"
                                class="button small success">Request signature</button>
                        <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="large-3 column">&nbsp;</div>
        </div>
        <?php foreach ($element->getAdditionalFields() as $field) : ?>
            <div class="row data-row">
                <div class="large-3 column">
                    <div class="data-label"><?php echo $element->getAttributeLabel($field)?>:</div>
                </div>
                <div class="large-9 column">
                    <div class="data-value"><?php echo CHtml::encode($element->$field)?></div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php foreach ($this->additional_view_mode_templates as $template) {
            $this->render($template, [
                "element" => $element
            ]);
        }?>
    <?php endif; ?>
</div>
<script type="text/javascript">
    $(function(){
      $('#<?=$modelName?>_requestSignature').on('click', function() {
        $.post('/<?=$module?>/default/registerSignRequest/', {
          'event_id': $(this).data('event_id'),
          'element_type_id': $(this).data('element_type_id'),
          YII_CSRF_TOKEN: YII_CSRF_TOKEN
        }, function( data ) {
          var dlg = new OpenEyes.UI.Dialog({
            content: "The request has been sent. Waiting for the signature to be completed...",
            title: "Waiting for signature"
          });
          dlg.open();
          setInterval(function(){
            $.get("/<?=$module?>/default/pollSignatureRequest?request_id=" + data,
            function (response) {
              if(response !== "0") {
                window.location.reload();
              }
            });
          }, 2000);
        });
      });
    });
</script>