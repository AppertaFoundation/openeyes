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
 * @var OphTrOperationchecklists_Questions[] $questions
 */
?>

<table class="cols-full">
    <colgroup>
        <col class="cols-5">
        <col class="cols-4">
        <col class="cols-3">
    </colgroup>
    <tbody>
    <?php
    foreach ($questions as $question) {
        $savedData = null;
        if (isset($response[$question->id])) {
            $savedData = $response[$question->id];
        }
        $questionAnswerAssignments = $question->questionAnswerAssignments;
        ?>
        <?php $elementClass = $question->is_hidden ?  ($question->isSubQuestion() ? 'class= "js-hide-question no-line"' : 'class= "js-hide-question"') : ($question->isSubQuestion() ? 'class="no-line"' : '') ?>
        <?php if ($question->type !== 'SECTION') :?>
        <tr id="<?= $question->id ?>" <?= $elementClass ?> style=<?= $question->is_hidden ?  ('display:none') : '' ?>>
            <?php
            echo \CHtml::hiddenField($name_stub . '[' . $question->id . '][mandatory]', $question->mandatory);
            if (isset($results)) {
                echo \CHtml::hiddenField($name_stub . '[' . $question->id . '][id]', @$results[$question->id]->id);
            }
            echo \CHtml::hiddenField($name_stub . '[' . $question->id . '][question_id]', $question->id);
            echo \CHtml::hiddenField($name_stub . '[' . $question->id . '][answer_id]', @$results[$question->id]->answer_id, array('id'=> 'result_answer_id' . $name_stub . $question->id));
            echo \CHtml::hiddenField($name_stub . '[' . $question->id . '][answer]', @$results[$question->id]->answer, array('id'=> 'result_answer' . $name_stub. $question->id));
            ?>
            <td>
                <?php echo $question->question; ?>
            </td>
            <td <?= !$question->is_comment_field_required ? 'style="text-align:left"' : '' ?>>
                <?php if ($question->type === 'RADIO') :
                    $radioElementId = $model_relation . $question->id . '_answer_id_';
                    foreach ($questionAnswerAssignments as $questionAnswerAssignment) :
                        $answers = $questionAnswerAssignment->answers;?>
                        <label class="inline highlight">
                            <?= CHtml::radioButton(
                                $name_stub . '[' . $question->id . '][answer_id]',
                                isset($results[$question->id]->answer_id) ?
                                    (@$results[$question->id]->answer_id === $answers->id ? 'checked' :'') :
                                    (@$savedData['answer_id'] === $answers->id ? 'checked' : ''),
                                array('value' => $answers->id, 'id' => $radioElementId . $answers->id)
                            ) ?>
                            <?= $answers->answer; ?>
                        </label>
                    <?php endforeach; ?>

                <?php elseif ($question->type === 'RADIO_TIME') :
                    $radioElementId = $model_relation . $question->id . '_answer_id_';
                    foreach ($questionAnswerAssignments as $questionAnswerAssignment) :
                        $answers = $questionAnswerAssignment->answers;
                        ?>
                        <label class="inline highlight">
                            <?= CHtml::radioButton(
                                $name_stub . '[' . $question->id . '][answer_id]',
                                isset($results[$question->id]->answer_id) ?
                                    (@$results[$question->id]->answer_id === $answers->id ? 'checked' :'') :
                                    (@$savedData['answer_id'] === $answers->id ? 'checked' : ''),
                                array('value' => $answers->id, 'id' => $radioElementId . $answers->id)
                            ) ?>
                            <?= $answers->answer; ?>
                        </label>
                    <?php endforeach; ?>
                    <label class="inline highlight">
                        <?= CHtml::timeField(
                            $name_stub . '[' . $question->id . '][answer]',
                            isset($results[$question->id]->answer) ?
                                $results[$question->id]->answer :
                                (isset($savedData['answer']) ?  $savedData['answer'] : ''),
                            array('placeholder' => 'Enter Time')
                        ) ?>
                    </label>

                <?php elseif ($question->type === 'TIME') : ?>
                    <label class="inline highlight">
                        <?= CHtml::timeField(
                            $name_stub . '[' . $question->id . '][answer]',
                            isset($results[$question->id]->answer) ?
                                $results[$question->id]->answer :
                                (isset($savedData['answer']) ? $savedData['answer'] : ''),
                            array('placeholder' => 'Enter Time')
                        ) ?>
                    </label>

                <?php elseif ($question->type === 'DATE') : ?>
                    <?= CHtml::textField(
                        $name_stub . '[' . $question->id . '][answer]',
                        isset($results[$question->id]->answer) ?
                            $results[$question->id]->answer :
                            (isset($savedData['answer']) ? $savedData['answer'] : ''),
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
                            $results[$question->id]->answer_id ?? '',
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
                            isset($results[$question->id]->answer) ?
                                $results[$question->id]->answer :
                                (isset($savedData['answer']) ? $savedData['answer'] : ''),
                            array('id' => $textElementId, 'autocomplete' => 'off')
                        ) ?>
                    </label>
                    <?php if ($question->id === '7') { ?>
                        <?php
                        $this->renderPartial(
                            'form_OphTrOperationchecklists_Contact',
                            array(
                                'name_stub' => $name_stub,
                                'question_id' => $question->id,
                                )
                        ); ?>

                    <?php } ?>

                <?php endif; ?>
            </td>
            <td>
                <?php if ($question->is_comment_field_required) : ?>
                    <?php $comment_button_id = $model_relation . $question->id . '_comment'; ?>
                    <div class="cols-full ">
                        <button id="<?= $comment_button_id.'_button' ?>"
                                type="button"
                                class="button js-add-comments"
                                style="<?php if (isset($results[$question->id]->comment) && @$results[$question->id]->comment != '') :
                                    ?>display: none;<?php
                                       endif; ?>"
                                data-comment-container="#<?= $comment_button_id . '_container'; ?>">
                            <i class="oe-i comments small-icon"></i>
                        </button>
                        <div class="flex-layout flex-left comment-group js-comment-container"
                              id="<?= $comment_button_id . '_container'; ?>"
                              style="<?php if (! (isset($results[$question->id]->comment) && @$results[$question->id]->comment != '')) :
                                    ?>display: none;<?php
                                     endif; ?>"
                              data-comment-button="#<?= $comment_button_id.'_button' ?>">
                            <?=\CHtml::textArea($name_stub . '[' . $question->id . '][comment]', @$results[$question->id]->comment, array(
                                'class' => 'autosize cols-full js-comment-field',
                                'rows' => 1,
                                'placeholder' => 'Comments',
                            )); ?>
                            <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
                        </div>
                    </div>

                <?php endif; ?>
            </td>
        </tr>
        <?php endif; ?>
        <?php if ($question->type === 'SECTION') { ?>
            <?php
            $relation = $result_model->getRelationForSection($question->section->section_name);
            $this->renderPartial(
                'form_' . $question->section->section_name,
                array(
                    'question' => $question,
                    'name_stub' => $name_stub,
                    'model' => CHtml::modelName($question->section->section_name),
                    'starting_index' => $starting_index,
                    'results' => $results ?? null,
                    'relation' => $relation,
                    'model_relation' => $model_relation
                )
            ); ?>

        <?php } ?>
    <?php } ?>
    </tbody>
</table>

<script>
    $(document).ready(function () {
        $('.js-date-element').each(function() {
            pickmeup('#' + this.getAttribute('id'), {
                format: 'd b Y',
                hide_on_select: true,
                default_date: true,
                max: new Date()
            });
        });
    });
</script>