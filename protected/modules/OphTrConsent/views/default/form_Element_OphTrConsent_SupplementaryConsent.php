<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php
$purifier = new CHtmlPurifier();
$my_questions = [];
$data_questions = [];
if ($element->id) { // add questions we have data for.
    foreach ($element->element_question as $ele_q) {
        if (!empty($ele_q->question)) {
            array_push($data_questions, $ele_q->question);
        }
    }
}

$form_id = @$_GET['type_id'] ?? '1';
$new_q = Ophtrconsent_SupplementaryConsentQuestion::model()->findAllMyQuestionsAsgn(
    $this->event->episode->firm->institution_id,
    $this->event->site_id,
    $this->event->episode->firm->getSubspecialtyID(),
    $form_id);
?>
<div class="element-fields cols-10">
    <?php if (!$new_q) { ?>
    <div class="alert-box info">There are no active supplementary consent questions.</div>
    <?php } else { ?>
    <table class="cols-full last-left">
        <colgroup>
            <col class="cols-6">
        </colgroup>
        <tbody>
            <?php

            $my_questions = array_merge($data_questions, $new_q);
            // remove duplicate questions
            $my_questions = array_map("unserialize", array_unique(array_map("serialize", $my_questions)));

            foreach ($my_questions as $questassgn) { ?>
                <tr>
                    <td>
                        <?php
                        echo $purifier->purify($questassgn->question_text);

                        if (isset($questassgn->question_info)) {
                            echo '</br><span style="color:#666">' . $purifier->purify($questassgn->question_info) . '</span>';
                        }; ?>
                    </td>
                    <td>
                        <?php
                        $my_answer_selections = [$questassgn->default_option_selection=>$questassgn->default_option_selection];
                        $my_answer_text = '';
                        $my_answer_list = Ophtrconsent_SupplementaryConsentQuestionAssignment::model()->getQuestionAnswerList($questassgn->id);

                        if ($element->id) { // only applies update action
                            if ($questassgn->question->question_type->text_based) {
                                $my_answer_text = Ophtrconsent_SupplementaryConsentQuestionAssignment::model()->getAnswerElementSelectionText($element->id, $questassgn->id);
                            } else {
                                $my_answer_selections = Ophtrconsent_SupplementaryConsentQuestionAssignment::model()->getAnswerElementSelectionList($element->id, $questassgn->id);
                            }
                        }
                        if(isset($_POST['Element_OphTrConsent_SupplementaryConsent']['element_question']["'" . $questassgn->id . "'"]["'" . $questassgn->question->question_type->name . "'"])){
                            if ($questassgn->question->question_type->text_based) {
                                $my_answer_text = $_POST['Element_OphTrConsent_SupplementaryConsent']['element_question']["'" . $questassgn->id . "'"]["'" . $questassgn->question->question_type->name . "'"];
                            } else {
                                $my_answer_selections = [$_POST['Element_OphTrConsent_SupplementaryConsent']['element_question']["'" . $questassgn->id . "'"]["'" . $questassgn->question->question_type->name . "'"]];
                            }
                        }

                        switch ($questassgn->question->question_type->name) {
                            case 'radio':
                                echo "<fieldset>";
                                echo CHtml::radioButtonList(
                                    'Element_OphTrConsent_SupplementaryConsent[element_question][' . $questassgn->id . '][' . $questassgn->question->question_type->name . ']',
                                    reset($my_answer_selections), //we can only pass in 1 value for radioButtonList
                                    $my_answer_list,
                                    array(
                                        'nowrapper' => true,
                                        'template' => '<label class="highlight inline ' . ($element->hasErrors('element_question[' . $questassgn->id . ']') ? ' highlighted-error error"' : '"') . '>{input} {label}</label>',
                                        'separator' => ''
                                    )
                                );
                                echo "</fieldset>";
                                break;
                            case 'check':
                                echo CHtml::checkBoxList(
                                    'Element_OphTrConsent_SupplementaryConsent[element_question][' . $questassgn->id . '][' . $questassgn->question->question_type->name . ']',
                                    $my_answer_selections, //checkBoxList is happy with multiple values
                                    $my_answer_list,
                                    array(
                                        'nowrapper' => true,
                                        'template' => '<label class="highlight inline '.($element->hasErrors('element_question[' . $questassgn->id . ']') ? ' highlighted-error error"' : '"').'>{input} {label}</label>',
                                        'separator' => ''
                                    )
                                );
                                break;
                            case 'dropdown':
                                echo CHtml::dropDownList(
                                    'Element_OphTrConsent_SupplementaryConsent[element_question][' . $questassgn->id . '][' . $questassgn->question->question_type->name . ']',
                                    reset($my_answer_selections),  //we can only pass in 1 value for dropDownList
                                    $my_answer_list,
                                    array('nowrapper' => true, 'class' => 'cols-full' . ($element->hasErrors('element_question[' . $questassgn->id . ']') ? ' highlighted-error error' : ''))
                                );
                                break;
                            case 'text':
                                echo CHtml::textField(
                                    'Element_OphTrConsent_SupplementaryConsent[element_question][' . $questassgn->id . '][' . $questassgn->question->question_type->name . ']',
                                    $purifier->purify($my_answer_text),
                                    array('nowrapper' => true, 'class' => 'cols-full' . ($element->hasErrors('element_question[' .$questassgn->id . ']') ? ' highlighted-error error' : ''), 'placeholder' => $purifier->purify($questassgn->default_option_text))
                                );
                                break;
                            case 'textarea':
                                echo CHtml::textArea(
                                    'Element_OphTrConsent_SupplementaryConsent[element_question][' . $questassgn->id . '][' . $questassgn->question->question_type->name . ']',
                                    $purifier->purify($my_answer_text),
                                    array('nowrapper' => true, 'class' => 'cols-full autosize' . ($element->hasErrors('element_question[' . $questassgn->id . ']') ? ' highlighted-error error' : ''), 'placeholder' => $purifier->purify($questassgn->default_option_text))
                                );
                                break;
                        }
                        ?>
                    </td>
                </tr>
            <?php }?>
        </tbody>
    </table>
    <?php } ?>
</div>

