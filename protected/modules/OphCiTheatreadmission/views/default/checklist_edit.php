<?php
/**
 * (C) Copyright Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * @var BaseEventTypeElement $element
 * @var OphcitheatreadmissionChecklistQuestions[] $questions
 */
?>

<table class="cols-full">
    <colgroup>
        <col class="cols-2">
        <col class="cols-2">
        <col class="cols-2">
        <col class="cols-2">
        <col class="cols-2">
        <col class="cols-2">
    </colgroup>
    <tbody>
    <?php
    foreach ($questions as $question) {
        $savedData = null;
        $questionAnswerAssignments = $question->questionAnswerAssignments;
        // Check if the current question needs to be pre-populate from the already saved data.
        $duplicationQuestionId = OphcitheatreadmissionChecklistQuestions::model()->duplicationQuestion[$question->id] ?? null;
        if (isset($duplicationQuestionId)) {
            $response = $question->getSavedResponse($this->event->id, $duplicationQuestionId);
        }
        // if there is a response then, set the responses for that answer to read-only
        $savedData = $response['answer'] ?? null;
        unset($response);
        ?>
        <?php $elementClass = $question->is_hidden ?  ($question->isSubQuestion() ? 'js-hide-question no-line' : 'js-hide-question') : ($question->isSubQuestion() ? 'no-line' : '') ?>
        <tr class="<?= $elementClass ?>" <?= $question->is_hidden ?  ('style="display:none"') : '' ?>>
            <?php
            echo \CHtml::hiddenField($name_stub . '[' . $question->id . '][mandatory]', $question->mandatory);
            if (isset($results)) {
                echo \CHtml::hiddenField($name_stub . '[' . $question->id . '][id]', @$results[($question->id)-$starting_index]->id);
            }
            echo \CHtml::hiddenField($name_stub . '[' . $question->id . '][question_id]', $question->id);
            echo \CHtml::hiddenField($name_stub . '[' . $question->id . '][answer_id]', @$results[($question->id)-$starting_index]->answer_id, array('id'=> 'result_answer_id' . $name_stub . $question->id));
            echo \CHtml::hiddenField($name_stub . '[' . $question->id . '][answer]', @$results[($question->id)-$starting_index]->answer, array('id'=> 'result_answer' . $name_stub. $question->id));
            ?>

            <td colspan="3">
                <?php echo $question->question; ?>
            </td>
            <?php if ($question->is_comment_field_required) : ?>
                <td <?= $question->type === 'SECTION' ? 'colspan="4" style="text-align:left"' : 'colspan="2"'?>>
                    <?php
                    echo CHtml::textArea(
                        $name_stub . '[' . $question->id . '][comment]',
                        isset($results[($question->id)-$starting_index]->comment) ? $results[($question->id)-$starting_index]->comment : '',
                        array(
                            'rows' => '2',
                            'class' => 'js-input-comments',
                            'nowrapper' => true,
                            'placeholder' => 'Comments'
                        )
                    );
                    ?>
                </td>
            <?php endif; ?>
            <?php if ($question->type !== 'SECTION') :?>
                <td colspan=4>
                    <?php if ($question->type === 'RADIO') :
                        $radioElementId = $model_relation . $question->id . '_answer_id_';
                        foreach ($questionAnswerAssignments as $questionAnswerAssignment) :
                            $answers = $questionAnswerAssignment->answers;?>
                            <label class="inline highlight">
                                <?= CHtml::radioButton(
                                    $name_stub . '[' . $question->id . '][answer_id]',
                                    isset($results[($question->id)-$starting_index]->answer_id) && @$results[($question->id)-$starting_index]->answer_id === $answers->id ? 'checked' : '',
                                    array('value' => $answers->id, 'id' => $radioElementId . $answers->id)
                                ) ?>
                                <?= $answers->answer; ?>
                            </label>
                        <?php endforeach; ?>

                    <?php elseif ($question->type === 'DATETIME') : ?>
                        <?= CHtml::textField(
                            $name_stub . '[' . $question->id . '][answer]',
                            isset($savedData) ?  $savedData : (isset($results[($question->id)-1]->answer) ? $results[($question->id)-$starting_index]->answer : ''),
                            array('placeholder' => 'dd MMM yyyy', 'class' => !isset($savedData) ? 'js-date-element' : '' , 'readonly' => isset($savedData))
                        ) ?>

                    <?php elseif ($question->type === 'DROPDOWN') :
                        $answers = [];
                        foreach ($questionAnswerAssignments as $questionAnswerAssignment) :
                            $answers[] = $questionAnswerAssignment->answers;
                        endforeach;
                        ?>
                        <label class="inline highlight">
                            <?=\CHtml::dropDownList(
                                $name_stub . '[' . $question->id . '][answer_id]',
                                $results[($question->id)-$starting_index]->answer_id ?? '',
                                CHtml::listData(
                                    $answers,
                                    'id',
                                    'answer'
                                ),
                                ['empty' => 'None']
                            ) ?>
                        </label>
                    <?php elseif ($question->type === 'TEXT') :
                        $textElementId = $model_relation . $question->id . '_answer_1';
                        ?>
                        <label class="inline highlight">
                            <?= CHtml::textField(
                                $name_stub . '[' . $question->id . '][answer]',
                                isset($results[($question->id)-1]->answer) ? $results[($question->id)-$starting_index]->answer : '',
                                array('id' => $textElementId)
                            ) ?>
                        </label>
                    <?php endif; ?>
                </td>
            <?php endif; ?>

            <?php if ($question->type === 'SECTION') { ?>
                <?php
                $relation = $result_model->getRelationForSection($question->section->section_name);
                $this->renderPartial(
                    'form_' . $question->section->section_name,
                    array(
                        'header' => $question->question,
                        'name_stub' => $name_stub . '[' . $question->id . ']' . '[' . $relation . ']',
                        'model' => CHtml::modelName($question->section->section_name),
                        'results' => $results ?? null,
                    )
                ); ?>

            <?php } ?>
        </tr>
    <?php } ?>
    </tbody>
</table>

<script>
    $(document).ready(function () {
        $('.js-date-element').each(function() {
            pickmeup('#'+this.getAttribute('id'), {
                format: 'd b Y 00:00:00',
                hide_on_select: true,
                default_date: false,
                max: new Date()
            });

            this.addEventListener('pickmeup-fill', function (e) {
                let d = new Date();
                let curr_hour = d.getHours();
                let curr_min = d.getMinutes();
                let currentTime = curr_hour + ":" + curr_min;

                let date = pickmeup(this).get_date(true);
                let currentDateTime = date.replace('00:00:00', currentTime);

                $(this).val(currentDateTime);
            });
        });
    });
</script>