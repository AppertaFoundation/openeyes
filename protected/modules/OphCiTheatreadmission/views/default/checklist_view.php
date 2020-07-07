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
<?php } else { ?>
    <header class="subgroup-header">
        <h3>
            <?= $element->getElementTypeName() . ($setIdName ?? '') ?>
        </h3>
    </header>
<?php } ?>
<div class="element-data full-width" id="subgroup-<?= strtolower(preg_replace('/[\/\s-]+/', '', $element->getElementTypeName()) . ($setId ?? null)); ?>" <?= (isset($isCollapsable) && $isCollapsable) ? 'style= "display: none"' : ''?>>
    <div>
        <table class="cols-12">
            <colgroup>
                <col class="cols-2">
                <col class="cols-2">
                <col class="cols-2">
                <col class="cols-2">
                <col class="cols-2">
                <col class="cols-2">
            </colgroup>
            <thead>
                <tr>
                    <th colspan="3">Checklist Questions</th>
                    <th colspan="3">Responses</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($checklistResults as $checklistResult) {
                $question = $checklistResult->question;
                $answer = null;
                if ($question->type === 'RADIO') {
                    $answer = $checklistResult->checklistAnswers;
                } elseif ($question->type === 'DATETIME') {
                    $answer = $checklistResult;
                } elseif ($question->type === 'DROPDOWN') {
                    $answer = $checklistResult->checklistAnswers;
                } elseif ($question->type === 'TEXT') {
                    $answer = $checklistResult;
                }
                ?>
                <tr <?= $question->isSubQuestion() ? 'class="no-line"' : '' ?>>
                    <td colspan="3">
                        <div class="data-label"><?= $question->question; ?></div>
                        <?php if (isset($checklistResult->comment)) { ?>
                            <div class="data-group"><br/>
                                <div class="cols-4 column large-push-1"
                                     style="font-style: italic;">Comments</div>
                                <div class="cols-6 column large-push-1 end"><?php echo $checklistResult->comment ?></div>
                            </div>
                        <?php } ?>
                    </td>
                    <?php if ($question->type === 'SECTION') {
                        // If the type of the question is SECTION, then need to render the
                        // respective view.
                        $relation = $resultModel->getRelationForSection($question->section->section_name);
                        $this->renderPartial(
                            'view_' . $question->section->section_name,
                            array(
                                'element' => $checklistResult->{$relation},
                            )
                        );
                    } else { ?>
                        <td colspan="3">
                            <span><?= isset($answer->answer) ? $answer->answer : 'Unknown'; ?></span>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>