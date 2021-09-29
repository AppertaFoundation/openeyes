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
// $userRadio = Ophtrconsent_SupplementaryConsentQuestionAssignment::model()
// ->findByAttributes(array('id' =>$element->user_submitted_radio ));
// $userRadioQuestion = Ophtrconsent_SupplementaryConsentQuestionAssignment::model()->findByAttributes(array('id'=>$userRadio->question_id));
?>
<div class="element-fields full-width">
    <div class="cols-10">
        <table class="cols-full last-left">
            <colgroup>
                <col class="cols-6">
            </colgroup>
            <tbody>
                <?php
                $purifier = new CHtmlPurifier();
                foreach ($element->element_question as $key => $question) {?>
                    <tr>
                        <td>
                            <?php
                            if (!empty($question->question->question_text)) {
                                echo nl2br($purifier->purify($question->question->question_text));
                                if (isset($question->question->question_info)) {
                                    echo '</br><span style="color:#666">' . nl2br($purifier->purify($question->question->question_info)) . '</span>';
                                }
                            } else {
                                if (!empty($question->question->question->name)) {
                                    echo nl2br($purifier->purify($question->question->name));
                                    if (isset($question->question->description)) {
                                        echo '</br><span style="color:#666">' . nl2br($purifier->purify($question->question->description))  . '</span>';
                                    }
                                }
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            switch ($question->question->question->question_type->name) {
                                case 'dropdown':
                                case 'check':
                                case 'radio':
                                    if (!empty($question->element_answers)) {
                                        foreach ($question->element_answers as $subkey => $answer) {
                                            if ($subkey>0) {
                                                echo ', ';
                                            }
                                            echo '<span class="highlighter" style="display: inline-block">';
                                            if (!empty($answer->answer)) {
                                                if (!empty($answer->answer->display)) {
                                                    echo nl2br($purifier->purify($answer->answer->display));
                                                } elseif (!empty($answer->answer->name)) {
                                                    echo nl2br($purifier->purify($answer->answer->name));
                                                }
                                            } else {
                                                echo "empty answer";
                                            }
                                            echo '</span>';
                                        }
                                    } else {
                                        echo "no answers";
                                    }
                                    break;
                                case 'text':
                                case 'textarea':
                                    if (!empty($question->element_answers)) {
                                        foreach ($question->element_answers as $subkey => $answer) {
                                            if ($subkey > 0) {
                                                echo '<br>';
                                            }
                                            echo '<span class="highlighter" style="display: inline-block">';
                                            if (!empty($answer->answer_text)) {
                                                echo nl2br($purifier->purify($answer->answer_text));
                                            } else {
                                                echo "No text";
                                            }
                                            echo '</span>';
                                        }
                                    } else {
                                        echo "no answers";
                                    }
                                    break;
                            } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>