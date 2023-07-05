<?php $form_id = isset($form) ? $form->getId() : null; ?>

<script type="text/javascript" src="<?= Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.widgets.js') . '/AutoCompleteSearch.js', true, -1); ?>"></script>

<?php

$this->widget('application.widgets.AutoCompleteSearch',
    [
        'field_name' => $class . '_' . $name,
        'htmlOptions' =>
            [
                'placeholder' => $placeholder,
            ],
        'layoutColumns' => ['field' => $autocompleteFieldColumns ?? 4]
    ]);

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


<script type="text/javascript">
    $(document).ready(function() {
        OpenEyes.UI.AutoCompleteSearch.init({
            input: $('#<?= $class . '_' . $name ?>'),
            url: '/disorder/autocomplete',
            params: {
                'code': function () {return "<?= $code ?>"},
            },
            maxHeight: '200px',
            onSelect: function() {
                let response = OpenEyes.UI.AutoCompleteSearch.getResponse();
                let input = OpenEyes.UI.AutoCompleteSearch.getInput();

                select(input[0], response);
            }
        });

        $('#enteredDiagnosisText').on('click', '.clear-diagnosis-widget', function (e) {
            e.preventDefault();
            $('.multiDiagnosis[value="' + $(this).data('diagnosisId') +'"').remove();
            $('input[id=savedDiagnosis]').val('');
            $(this).parent().hide();
            <?= (isset($callback) && strlen($callback)) ? $callback . '();' : '' ?>
        });

    });

    <?php
    if (is_array($value)) :
        ?>
    var select = function(event, ui) {
        <?= (isset($callback) && strlen($callback)) ? $callback . '(event, ui);' : ''?>
        var $clear = $('<?=$clear_diagnosis?>'),
            $new= $('<span></span>');

        $clear.data('diagnosisId', ui.id);
        $('#<?=$class . '_' . str_replace('.', '', $name)?>').val('');
        $new.text(ui.value).append($clear);
        $('#enteredDiagnosisText').append($new.append('<br>'));
        $('#enteredDiagnosisText').show();
        $(event).parent().append('<input type="hidden" name="<?=$class ?>[<?=$name ?>][]" class="multiDiagnosis" value="' + ui.id + '"' +
            <?php echo ($form_id ? " form='{$form_id}'" : '');?>
            '>');
        $('#<?=$class?>_<?=$name?>').focus();
        $('#<?php echo $class?>_<?php echo $name?> option:first').attr('selected', 'selected');
        return false;
    };
    <?php else : ?>
    var select = function(event, ui) {
        <?= isset($callback) ? $callback . '(event, ui);' : ''?>
        $('#<?=$class . '_' . str_replace('.', '', $name)?>').val('');
        $('#enteredDiagnosisText').html(ui.value + ' <?=$clear_diagnosis?> ');
        $('#enteredDiagnosisText').show();
        $('input[id=savedDiagnosisText]').val(ui.value);
        $('input[id=savedDiagnosis]').val(ui.id);
        $('#<?=$class . '_' . str_replace('.', '', $name)?>').focus();
        $('#<?php echo $class?>_<?php echo $name?> option:first').attr('selected', 'selected');
        return false;
    };
    <?php endif;?>
</script>