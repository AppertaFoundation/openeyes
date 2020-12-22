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
 * @var bool $isCollapsable
 */
?>
<?php if (isset($isCollapsable) && $isCollapsable) { ?>
    <header class="subgroup-header">
        <h3><?= $element->getElementTypeName() . ($setIdName ?? '') ?></h3>
        <div class="viewstate-icon">
            <i class="oe-i small js-element-subgroup-viewstate-btn collapse" data-subgroup="subgroup-<?= strtolower(preg_replace('/[\/\s-]+/', '', $element->getElementTypeName()) . ($setId ?? null)); ?>"></i>
        </div>
    </header>
<?php } ?>

<div class="element-data full-width" id="subgroup-<?= strtolower(preg_replace('/[\/\s-]+/', '', $element->getElementTypeName()) . ($setId ?? null)); ?>" <?= (isset($isCollapsable) && $isCollapsable) ? 'style= "display: none"' : ''?>>
    <div>
        <table class="cols-12">
            <colgroup>
                <col class="cols-6">
                <col class="cols-2">
                <col class="cols-2">
                <col class="cols-2">
            </colgroup>
            <thead>
            <tr>
                <th>Checklist Questions</th>
                <th class="center">(Ward Practitioner)</th>
                <th class="center">(Reception Practitioner)</th>
                <th class="center">(Theatre Practitioner)</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($questions as $question) {
                $isCommentRowRequired = false;
                $elementResults = $element::model()
                    ->with("checklistResults")
                    ->find(array(
                            "condition" => "element_id = :element_id AND checklistResults.question_id = :question_id",
                            "params" => array(':element_id' => $element->id, ':question_id' => $question->id),
                        ));

                $checklistResults = $elementResults->checklistResults;
                if (isset($removeLast) && $removeLast) {
                    array_pop($checklistResults);
                }
                ?>
                <tr id="<?= $question->id ?>" <?= $question->isSubQuestion() ? 'class="no-line"' : '' ?>>
                    <td>
                        <div class="data-label"><?= $question->question; ?></div>
                    </td>
                    <?php foreach ($checklistResults as $checklistResult) {
                        $answer = null;
                        if ($question->type === 'RADIO') {
                            $answer = $checklistResult->checklistAnswers->answer ?? null;
                        } elseif ($question->type === 'DATE') {
                            $answer = $checklistResult->answer;
                        } elseif ($question->type === 'DROPDOWN') {
                            $answer = $checklistResult->checklistAnswers->answer ?? null;
                        } elseif ($question->type === 'TEXT') {
                            $answer = $checklistResult->answer;
                        } elseif ($question->type === 'TIME') {
                            $answer = $checklistResult->answer;
                        } elseif ($question->type === 'RADIO_TIME') {
                            $radio = $checklistResult->checklistAnswers->answer ?? null;
                            $time = $checklistResult->answer;
                            if (isset($radio, $time)) {
                                $answer = $radio . ' / ' . $time;
                            } elseif (isset($radio) && !isset($time)) {
                                $answer = $radio;
                            } elseif (!isset($radio) && isset($time)) {
                                $answer = $time;
                            }
                        }
                        if ($checklistResult->comment) {
                            $isCommentRowRequired = true;
                        }?>
                        <td class="center">
                            <?php if ($question->requires_answer) { ?>
                                <span><?= $answer ?? 'Unknown'; ?></span>
                            <?php } ?>
                        </td>
                    <?php } ?>
                    <td></td>
                    <td></td>
                </tr>
                <?php if ($isCommentRowRequired) { ?>
                    <tr class="no-line">
                        <td>
                            <br>
                            <div style="font-style: italic;">Comments</div>
                        </td>
                        <?php foreach ($checklistResults as $checklistResult) { ?>
                            <td class="center">
                                <br>
                                <span><?php echo $checklistResult->comment ?></span>
                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>