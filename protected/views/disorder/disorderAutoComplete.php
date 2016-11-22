<?php
$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
    'name' => "{$class}[$name]",
    'id' => "{$class}_{$name}_0",
    'value' => '',
    'source' => "js:function(request, response) {
                $.ajax({
                    'url': '" . Yii::app()->createUrl('/disorder/autocomplete') . "',
                    'type':'GET',
                    'data':{'term': request.term, 'code': " . CJavaScript::encode($code) . "},
                    'success':function(data) {
                        data = $.parseJSON(data);
                        response(data);
                    }
                });
                }",
    'options' => array(
    'minLength' => '3',
    'select' => "js:function(event, ui) {
                    $('#" . $class . '_' . $name . "_0').val('');
                    $('#enteredDiagnosisText').html(ui.item.value+' $clear_diagnosis ');
                    $('#enteredDiagnosisText').show();
                    $('input[id=savedDiagnosisText]').val(ui.item.value);
                    $('input[id=savedDiagnosis]').val(ui.item.id);
                    $('#" . $class . '_' . $name . "').focus();
                    return false;
                }",
    ),
    'htmlOptions' => array(
        'placeholder' => $placeholder,
    ),
));
?>
<input type="hidden" name="<?php echo $class ?>[<?php echo $name ?>]" id="savedDiagnosis" value="<?=$value?>"/>