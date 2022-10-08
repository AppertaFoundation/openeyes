<?php
$purifier = new CHtmlPurifier();
foreach ($element->element_question as $key => $question) { ?>
    <div class="group">
        <h4>
            <?php
            if (!empty($question->question->question_text)) {
                echo nl2br($purifier->purify($question->question->question_text));
                if (isset($question->question->question_info)) {
                    echo '</br>' . nl2br($purifier->purify($question->question->question_info));
                }
            } else {
                if (!empty($question->question->question->name)) {
                    echo nl2br($purifier->purify($question->question->name));
                    if (isset($question->question->description)) {
                        echo '</br>' . nl2br($purifier->purify($question->question->description));
                    }
                }
            }
            ?>
        </h4>
        <div class="indent">
            <?php
            switch ($question->question->question->question_type->name) {
                case 'dropdown':
                    if (!empty($question->element_answers)) {
                        foreach ($question->element_answers as $subkey => $answer) {
                            if ($subkey > 0) {
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
                case 'check':
                    if (!empty($question->element_answers)) {
                        $element_answers = array_map(function ($element_answer) {
                            return $element_answer->answer_id;
                        }, $question->element_answers);
                        foreach ($question->question->answers as $answer) {
                            $checked = "";
                            if (in_array($answer->id, $element_answers)) {
                                $checked = "checked";
                            }
                            echo '<span class="checkbox ' . $checked . '"></span>' . nl2br($purifier->purify($answer->display));
                        }
                    } else {
                        echo "no answers";
                    }
                    break;
                case 'radio':
                    if (!empty($question->element_answers)) {
                        $element_answers = array_map(function ($element_answer) {
                            return $element_answer->answer_id;
                        }, $question->element_answers);
                        foreach ($question->question->answers as $answer) {
                            $checked = "";
                            if (in_array($answer->id, $element_answers)) {
                                $checked = "checked";
                            }
                            echo '<span class="checkbox ' . $checked . '"></span>' . nl2br($purifier->purify($answer->display));
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
        </div>
    </div>
<?php } ?>
<p>If you do not wish to take part in the above, your care will not be compromised in any way.</p>