<?php
/** @var SignatureCapture $this */
$uid = uniqid();
?>

<?php if(!$this->embedded): ?>
<button id="signature_open_<?=$uid?>" type="button" class="button small success"><?=$buttonText?></button>
<canvas class="canvasCopy" id="canvasCopy_<?=$uid?>"></canvas>
    <?php if ($element) :?>
        <button data-event-id="<?=$element->event_id?>"
                data-element-id="<?=$element->id?>"
                data-element-type-id="<?=$element->elementType->id?>"
                type="button"
                class="button small success js-print-out-sign-form">Print out</button>

        <?php
        $client_script = Yii::app()->clientScript;
        $q = http_build_query([
            'event_id' => $element->event_id,
            'element_id' => $element->id,
            'element_type_id' => $element->elementType->id,
        ]);
        $client_script->registerScript('printSignForm', '
                $(document).ready(function() {
                    $(".js-print-out-sign-form").on("click", function(e) {
                        e.preventDefault();
                        disableButtons();
                        $frame = $("<iframe>", {
                            style: "display: none", 
                            src: "/" + moduleName + "/default/printQRSignature?' . $q . '"});
                        $frame.appendTo($("body"));
                        $frame.get(0).contentWindow.print();
                        setTimeout(enableButtons, 2000);
                        
                    });
                });
            ', CClientScript::POS_END);?>
    <?php endif; ?>
<?php endif; ?>
<?php if($this->showMessage): ?>
<div id="signature_message"></div>
<?php endif; ?>
<script type="text/javascript">
      if(typeof window.sc_<?=$uid?> === "undefined") {
        window.sc_<?=$uid?> = new OpenEyes.UI.SignatureCapture({
          requirePIN: <?php echo $this->pinSecured ? "true" : "false" ?>,
          unique_identifier: "<?=$this->uniqueCode?>",
          cryptKey: "<?=$this->key?>",
          submitURL: "<?=$this->submitURL?>",
          messageContainer: <?= $this->showMessage ? '$("#signature_message")' : '$([])' ?>,
          csrf: {
            name: "<?=Yii::app()->request->csrfTokenName?>",
            token: "<?=Yii::app()->request->csrfToken?>"
          },
          onSubmit: <?=$this->onSubmit?>,
          openButtonSelector: "#signature_open_<?=$uid?>",
          widgetid: "<?=$uid?>",
          embedded: <?=$this->embedded ? "true" : "false" ?>,
          embedded_canvas_selector: "<?=$this->embedded_canvas_selector?>"
        });
      }
</script>