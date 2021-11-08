<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php if (!$suppleConsent) : ?>
    <div class="row divider">
        <div class="alert-box issue"><b>No results found</b></div>
    </div>
<?php endif; ?>

<div class="row divider cols-full">
    <form id="procedures_search" method="post">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>" />
        <table class="cols-full">
            <colgroup class="cols-full">
                <col class="cols-10">
                <col class="cols-1">
                <col class="cols-1">
            </colgroup>
            <tbody>
                <tr class="col-gap">
                    <td>
                        <?= \CHtml::textField(
                            'search[query]',
                            $search['query'],
                            [
                                'class' => 'cols-full',
                                'placeholder' => "Question name/description/printed text, Answer name/description/printed text or Institution/Site/Subspecialty/Form name"
                            ]
                        ); ?>
                    </td>
                    <td>
                        <?= \CHtml::dropDownList(
                            'search[active]',
                            $search['active'],
                            [
                                1 => 'Only Active',
                                0 => 'Exclude Active',
                            ],
                            ['empty' => 'All']
                        ); ?>
                    </td>
                    <td>
                        <button class="blue hint" type="submit" id="et_search">Search
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>


<form id="admin_procedures" method="post">
    <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>" />

    <table class="standard cols-full">
        <colgroup class='cols-full'>
            <col class="cols-1">
            <col class="cols-2">
            <col class="cols-1">
            <col class="cols-9">
        </colgroup>
        <thead>
            <tr>
                <th><?= Ophtrconsent_SupplementaryConsentQuestion::model()->getAttributeLabel('name') ?></th>
                <th><?= Ophtrconsent_SupplementaryConsentQuestion::model()->getAttributeLabel('description') ?></th>
                <th><?= Ophtrconsent_SupplementaryConsentQuestion::model()->getAttributeLabel('question_type') ?></th>
                <th><?= Ophtrconsent_SupplementaryConsentQuestion::model()->getAttributeLabel('question_assignment') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $purifier = new CHtmlPurifier();
            foreach ($suppleConsent as $key => $question) { ?>
                <tr class="clickable divider" data-id="<?= $question->id ?>" data-uri="OphTrConsent/oeadmin/supplementaryConsent/edit/<?= $question->id ?>">

                    <td><?= $purifier->purify($question->name) ?></td>
                    <td><?= $purifier->purify($question->description) ?></td>
                    <td><?= $question->question_type->name; ?></td>

                    <td class='cols-full'>
                        <?php if (!empty($question->question_assignment)) { ?>
                            <table class='standard cols-full'>
                                <thead class='cols-full'>
                                    <tr>
                                        <th>Question text</th>
                                        <th>Required</th>
                                        <th>Override level</th>
                                        <th>Applies only to form</th>

                                    </tr>
                                    </thead>
                                    <?php
                                    if ($search['active'] !== '') {
                                        $question_assignments = Ophtrconsent_SupplementaryConsentQuestionAssignment::model()->findAll('question_id=? AND active=?', [$question->id, (int)$search['active']]);
                                    } else {
                                        $question_assignments = $question->question_assignment;
                                    }
                                    ?>
                                    <?php foreach ($question_assignments as $questionAsgn) { ?>
                                        <tr>
                                            <td>
                                                <?= $purifier->purify($questionAsgn->question_text); ?>
                                            </td>
                                            <td>
                                                <?= $questionAsgn->required ? "Yes" : "No"; ?>
                                            </td>
                                            <td>
                                                <?= $questionAsgn->getSettingLevel(); ?>
                                            </td>
                                            <td>
                                                <?= $questionAsgn->getFormLevelName() ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                            </table>

                        <?php } else { ?>
                            No current wording assigned - Question cannot be used.
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>

        <tfoot class="pagination-container">
            <tr>
                <td colspan="4">
                    <?= \CHtml::button(
                        'Add',
                        [
                            'class' => 'button large',
                            'data-uri' => '/OphTrConsent/oeadmin/supplementaryConsent/edit',
                            'name' => 'Add new question type',
                            'id' => 'et_add',
                        ]
                    ); ?>
                </td>
                <td colspan="9">
                    <?php $this->widget(
                        'LinkPager',
                        ['pages' => $pagination]
                    ); ?>
                </td>
            </tr>
        </tfoot>
    </table>
</form>
