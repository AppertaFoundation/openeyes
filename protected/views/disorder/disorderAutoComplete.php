<?php $form_id = isset($form) ? $form->getId() : null; ?>

<script type="text/javascript">
  $(document).ready(function() {
    $('#enteredDiagnosisText').on('click', '.clear-diagnosis-widget', function (e) {
      e.preventDefault();
      $('.multiDiagnosis[value="' + $(this).data('diagnosisId') +'"').remove();
      $('input[id=savedDiagnosis]').val('');
      $(this).parent().hide();
        <?= (isset($callback) && strlen($callback)) ? $callback . '();' : '' ?>
    });
  });
  var source = function(request, response) {
    $.ajax({
      'url': '<?=Yii::app()->createUrl('/disorder/autocomplete')?>',
      'type':'GET',
      'data':{'term': request.term, 'code': "<?=$code?>"},
      'success':function(data) {
        response(data);
      }
    });
  };

    <?php
    if (is_array($value)) :
        ?>
  var select = function(event, ui) {
        <?= (isset($callback) && strlen($callback)) ? $callback . '(event, ui);' : ''?>
    var $clear = $('<?=$clear_diagnosis?>'),
      $new= $('<span></span>');

    $clear.data('diagnosisId', ui.item.id);
    $('#<?=$class . '_' . str_replace('.', '', $name)?>_0').val('');
    $new.text(ui.item.value).append($clear);
    $('#enteredDiagnosisText').append($new.append('<br>'));
    $('#enteredDiagnosisText').show();
    $(event.target).parent().append('<input type="hidden" name="<?=$class ?>[<?=$name ?>][]" class="multiDiagnosis" value="' + ui.item.id + '"' +
        <?php echo ($form_id ? " form='{$form_id}'" : '');?>
        '>');
    $('#<?=$class?>_<?=$name?>').focus();
    $('#<?php echo $class?>_<?php echo $name?> option:first').attr('selected', 'selected');
    return false;
  };
    <?php else : ?>
  var select = function(event, ui) {
        <?= isset($callback) ? $callback . '(event, ui);' : ''?>
    $('#<?=$class . '_' . str_replace('.', '', $name)?>_0').val('');
    $('#enteredDiagnosisText').html(ui.item.value + ' <?=$clear_diagnosis?> ');
    $('#enteredDiagnosisText').show();
    $('input[id=savedDiagnosisText]').val(ui.item.value);
    $('input[id=savedDiagnosis]').val(ui.item.id);
    $('#<?=$class . '_' . str_replace('.', '', $name)?>_0').focus();
    $('#<?php echo $class?>_<?php echo $name?> option:first').attr('selected', 'selected');
    return false;
  };
    <?php endif;?>
</script>
<?php
$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
    'name' => "{$class}[$name]",
    'id' => $class . '_' . str_replace('.', '', $name) . '_0',
    'value' => '',
    'source' => "js:source",
    'options' => array(
      'minLength' => '3',
      'select' => "js:select",
    ),
    'htmlOptions' => array(
        'placeholder' => $placeholder,
    ),
));
if (is_array($value)) :
    foreach ($value as $disorder) :
        ?>
  <input type="hidden" name="<?=$class ?>[<?=$name ?>][]" class="multiDiagnosis" value="<?=$disorder->id?>" <?php echo ($form_id ? "form='{$form_id}'" : '');?>>
        <?php
    endforeach;
    else :
        ?>
  <input type="hidden" name="<?=$class ?>[<?=$name ?>]" id="savedDiagnosis" value="<?=$value?>" <?php echo ($form_id ? "form='{$form_id}'" : '');?>>
    <?php endif; ?>
