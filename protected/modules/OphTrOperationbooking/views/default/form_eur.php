<?php
$form_id = 'eur-create';
$this->beginContent('//patient/event_container', array('no_face'=>true , 'form_id' => $form_id));
$clinical = $clinical = $this->checkAccess('OprnViewClinical');
$warnings = $this->patient->getWarnings($clinical);
$this->event_actions[] = EventAction::button('Cancel', 'cancel', array('level' => 'cancel'), array('form' => $form_id));
$this->event_actions[] = EventAction::button('Next', 'next', array('level' => 'next', 'disabled' => false), array('form' => $form_id));

$questions = EURQuestions::model()->findAll(array('order'=>'display_order'));
$element = EUREventResults::model();
$answerResults = $element->eurAnswerResults;
$name_stub = 'EUREventResult[eurAnswerResults]';
$procedure = Element_OphTrOperationbooking_Operation::model();
?>
<?php
    $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
            'id' => $form_id,
            'enableAjaxValidation' => false,
    ));
    ?>
<?=\CHtml::hiddenField('next')?>
<section class="element full procedures">
    <header class="element-header">
        <h3 class="element-title">Procedures</h3>
    </header>
    <div>
    <?php $form->widget('application.widgets.ProcedureSelection', array(
        'element' => $procedure,
        'durations' => true,
        'label' => '',
        'complexity' => $procedure->complexity
    )) ?>
</section>
<section class="element full eur" style="display:none;">
    <header class="element-header">
        <h3 class="element-title">Effective use of resources (EUR)</h3>
    </header>

    <div class="element-fields full-width">
        <div class="flex-layout flex-top col-gap data-group">
            <table class="cols-full last-left">
                <tbody>
                    <tr>
                        <td class="cols-1">Which Eye?</td>
                        <td class="cols-9">
                            <label for="eur_first_eye" class="inline highlight">
                                <input type="radio" data-eye="first" value="1" id="eur_first_eye" name="eye" required disabled>
                                <span>1st Eye</span>
                            </label>
                            <label for="eur_second_eye" class="inline highlight">
                                <input type="radio" data-eye="second" value="2" id="eur_second_eye" name="eye" required disabled>
                                <span>2nd Eye</span>
                            </label>
                        </td>
                        <td class="cols-2"></td>
                    </tr>
                    
                    <?php foreach ($questions as $q) {
                        $index = $q->id - 1;
                        $answers = $q->answers;
                        foreach ($answerResults as $anr_item) {
                            if ($anr_item->question_id == $q->id) {
                                $answerResult_item = $anr_item;
                            }
                        }
                        switch ($q->eye_num) {
                            case 1:
                                $question_style = 'first';
                                break;
                            case 2:
                                $question_style = 'second';
                                break;
                            case 3:
                                $question_style = 'first second';
                                break;
                            default:
                                break;
                        }
                        ?>


                    <tr class="<?=$question_style;?> questions" style="display:none">
                        <td class="cols-1">Statement <?=$q->id?></td>
                        <td class="cols-9"><?=$q->question?></td>
                        <td class="cols-2">
                        <?php  foreach ($answers as $answer_item) {?>
                            <label class="inline highlight cols-full">
                                <input class="eur_answer_res"
                                    id="EURAnswerResult_<?= $answer_item->id?>"
                                    value="<?= $answer_item->id ?>"
                                    data-value="<?= $answer_item->value?>"
                                    type="radio"
                                    name="<?= $name_stub .'['. $index .'][answer_id]'?>"
                                    data-question="<?=$q->id?>"
                                    required
                                    disabled
                                    <?= isset($answerResult_item->answer_id)&& @$answerResult_item->answer_id===$answer_item->id ?'checked':''?>
                                >
                                <span><?= $answer_item->answer ?></span>
                            </label>
                        <?php } ?>
                        <?php if (isset($answerResult_item)  && isset($answerResult_item->id)) { ?>
                            <?=\CHtml::hiddenField( $name_stub .'['. $index .'][id]', @$answerResult_item->id)?>
                        <?php } ?>

                        <?php if (isset($element)  && $element->id) { ?>
                            <?=\CHtml::hiddenField( $name_stub .'['. $index .'][element_id]', @$element->id)?>
                        <?php } ?>
                        <?=\CHtml::hiddenField( $name_stub .'['. $index .'][question_id]', $q->id)?>
                        </td>
                    </tr>
                    <?php }?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="element-fields full-width cols-10">
        <table class="cols-full last-left">
            <tr>
                <td class="cols-1">Result</td>
                <td class="cols-9">
                    <div class="flex-layout">
                        <div id="eur-res-ctn" class="flex-layout flex-left alert-box alert">
                            <div class="msg">Please finish the Questionnaire Above</div>
                            <input id="eur-res" type="hidden" name="eur_result" value="" disabled>
                        </div>
                    </div>
                </td>
                <td class="cols-2"></td>
            </tr>
        </table>
    </div>
</section>
<?php $this->endWidget();?>
<?php $this->endContent();?>
<script>
    $('button[id^="et_next"]').prop('disabled', true);
    function updateResult(eye_side){
        var last_answer = $('tr.active.' + eye_side).last().find('input:checked')
        var res_msg_ctn = $('#eur-res-ctn');
        var res_msg = res_msg_ctn.find('.msg');
        var res = last_answer.data('value');
        var msg = '';
        if(res == 0){
            res_msg_ctn.removeClass('alert');
            res_msg_ctn.removeClass('success');
            res_msg_ctn.addClass('warning');
            res_msg_ctn.find('#eur-res').prop('disabled', false);
            msg = 'The patient is not suitable for Cataract Surgery.'
        } else if(res == 1){
            res_msg_ctn.removeClass('alert');
            res_msg_ctn.removeClass('warning');
            res_msg_ctn.addClass('success');
            res_msg_ctn.find('#eur-res').prop('disabled', false);
            msg = 'The patient is suitable for Cataract Surgery.'
        } else {
            res_msg_ctn.removeClass('warning');
            res_msg_ctn.removeClass('success');
            res_msg_ctn.addClass('alert');
            res_msg_ctn.find('#eur-res').prop('disabled', true);
            msg = 'Please finish the Questionnaire Above.'
        }
        $(res_msg).text(msg);
        $('#eur-res').val(res);
    }
    $('input[name="eye"]').off('change').on('change', function(){
        var eye_side = $(this).data('eye');
        var questions = $('tr.questions');
        var cur_questions = $('tr.' + eye_side);
        if(questions[0] && questions[0].style.display === 'none'){
            $(questions[0]).show();
            $(questions[0]).addClass('active')
            $(questions[0]).find('input').prop('disabled', false);
        } else {
            $(questions).not(questions[0]).hide();
            $(questions).not(questions[0]).removeClass('active');
            $(questions).not(questions[0]).find('input').prop('disabled', true);
            $(questions).find('input').prop('checked', false);
        }
        updateResult(eye_side);
        var answers = cur_questions.find('input');
        answers.off('change').on('change', function(e){
            var next = e.target.closest('.' + eye_side).nextElementSibling;
            if(!$(this).prop('checked')){
                $(next).hide();
                $(next).removeClass('active');
                $(next).find('input').prop('checked', false);
                $(next).find('input').prop('disabled', true);
                $(next).find('input').trigger('change');
                return
            }

            if($(this).data('value') == '0'){
                if(next && next.classList.contains(eye_side)
                ){
                    $(next).show();
                    $(next).addClass('active');
                    $(next).find('input').prop('disabled', false);
                }
            } else {
                if(next && next.classList.contains(eye_side)
                ){
                    $(next).hide();
                    $(next).removeClass('active');
                    $(next).find('input').prop('checked', false);
                    $(next).find('input').prop('disabled', true);
                    $(next).find('input').trigger('change');
                }
            }
            updateResult(eye_side);
        });
    })
    async function callbackAddProcedure(procedure_id, selected_proc){
        $.ajax({
            'type': 'GET',
            'url': baseUrl + '/OphTrOperationbooking/Default/CheckProcedureEUR?procedure_id=' + procedure_id,
            'success': function(resp){
                if(resp){
                    $('section.eur').show();
                    $('section.eur input[name="eye"]').prop('disabled', false);
                    $('section.eur input[name="eye"]').prop('checked', false);
                }
            }
        });
        if($('tbody.body').html().trim()){
            $('button[id^="et_next"]').prop('disabled', false);
        }
    }
    function callbackRemoveProcedure(procedure_id, selected_proc){
        if(procedure_id == 42){
            var questions = $('tr.questions');
            $('section.eur').hide();
            $('section.eur input').prop('disabled', true);
            $('section.eur input').prop('checked', false);
            questions.hide();
            updateResult(null);
        }
        if(!$('tbody.body').html().trim()){
            $('button[id^="et_next"]').prop('disabled', true);
        }

    }
</script>