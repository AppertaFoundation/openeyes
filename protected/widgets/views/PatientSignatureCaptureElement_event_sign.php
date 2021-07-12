<?php

    /** @var Element_PatientSignature $element */
    $modelName = CHtml::modelName($element);
    $module_id = Yii::app()->controller->module->id;
    $csrf_name = Yii::app()->request->csrfTokenName;
    $csrf_value = Yii::app()->request->csrfToken;
    $onSubmitJS = <<<JS
function(data) {
    $.post("/{$module_id}/default/signPatientSignatureElement?element_id={$element->id}&element_type_id={$element->getElementType()->id}&file_id="+data,
    {
      "{$csrf_name}" : "{$csrf_value}"
    }, function(response){
        var dlg;
        if(response === "1") {
            dlg = new OpenEyes.UI.Dialog({
              title: "Document signed",
              content : "The signature has been secured and saved. You will be redirected to the OpenEyes standby screen in <span id=\"countdown\">10</span> seconds."
            });
            dlg.open();
            setTimeout(function(){
              window.location.href = "/site/standby"
            }, 9000);
            var countdown = 10;
            setInterval(function(){
              $("#countdown").text(--countdown);  
            }, 1000);
        }
        else {
            dlg = new OpenEyes.UI.Dialog({
              title: "An error occured",
              content : "There has been an error while signing this document."
            });
            dlg.open();
        }
          
    });
}
JS;

?>

<canvas id="<?=$modelName?>_canvas" width="600" height="300" style="border: 4px solid black; margin-bottom: 5vw"></canvas>
<script type="text/javascript">
  (function(){
    var $canvas = $("#<?=$modelName?>_canvas");
    var $wrapper = $canvas.closest("div");
    $canvas.attr("width", $wrapper.width());
    $canvas.attr("height", $wrapper.width() / 4);
  })();
</script>
<?php
$this->widget('SignatureCapture', [
    'buttonText' => 'Capture signature',
    'submitURL'=> "/ProtectedFile/uploadBase64Image?name=signature.jpg",
    'onSubmit' => $onSubmitJS,
    'showMessage' => false,
    'embedded' => true,
    'embedded_canvas_selector' => "#".$modelName."_canvas"
]); ?>
