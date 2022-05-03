<?php

/**
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
<div class="cols-12">
    <h2>Edit Supplementary Consent Question</h2>
    <?= $this->renderPartial('//admin/_form_errors', array('errors' => $errors)) ?>
    <form method="POST">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>" />

        <table class="standard cols-full">

            <colgroup>
                <col class="cols-4">
                <col class="cols-8">
            </colgroup>

            <tbody>
                <?php
                $text_fileds = ['name', 'description',];
                foreach ($text_fileds as $field) { ?>
                    <tr>
                        <td><?= $suppleconsent->getAttributeLabel($field) ?></td>
                        <td>
                            <?= \CHtml::activeTextField(
                                $suppleconsent,
                                $field,
                                [
                                    'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
                                    'class' => 'cols-full'
                                ]
                            ); ?>
                        </td>
                    </tr>
                <?php } ?>

                <tr>
                    <td><?= $suppleconsent->getAttributeLabel("question_type") ?></td>
                    <td>
                        <?php
                        $qts = Ophtrconsent_SupplementaryConsentQuestionType::model()->findAll(array('select' => 'id, name', 'order' => 'id'));
                        $list = CHtml::listData($qts, 'id', 'name');
                        echo CHtml::DropDownList(
                            'Ophtrconsent_SupplementaryConsentQuestion[question_type_id]',
                            $suppleconsent->question_type_id,
                            $list,
                        );
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <?= \CHtml::submitButton(
            'Save',
            [
                'class' => 'button large',
                'name' => 'save',
                'id' => 'et_save'
            ]
        ); ?>
        <?= \CHtml::submitButton(
            'Cancel',
            [
                'class' => 'button large',
                'data-uri' => '/OphTrConsent/oeadmin/SupplementaryConsent/list/',
                'name' => 'cancel',
                'id' => 'et_cancel'
            ]
        ); ?>
    </form>

<?php if (isset($suppleconsent->id)) : ?>
    <br />
    <h2>Local question version settings</h2>
    <table class="standard cols-full">
        <thead>
            <tr>
                <th>Question text</th>
                <th>Info text</th>
                <th>Default answer</th>
                <th>Required</th>
                <th>Override level</th>
                <th>Applies only to form</th>
                <th>Active</th>
            </tr>
        </thead>
        <?php
            $is_text = $suppleconsent->question_type->text_based;
            $purifier = new CHtmlPurifier();
        ?>
        <?php foreach ($suppleconsent->question_assignment as $questionAsgn) { ?>
            <tr id="$key" class="clickable row divider" data-id="<?= $questionAsgn->id ?>" data-uri="OphTrConsent/oeadmin/supplementaryConsent/editAssignment/<?= $questionAsgn->id ?>">
                <td>
                    <?= $purifier->purify($questionAsgn->question_text); ?>
                </td>
                <td>
                    <?= $purifier->purify($questionAsgn->question_info); ?>
                </td>
                <td>
                    <?php if ($is_text) : ?>
                        <?= $purifier->purify($questionAsgn->default_option_text); ?>
                    <?php elseif ($questionAsgn->default_option_selection !== null) : ?>
                        <?= $purifier->purify($questionAsgn->answers[$questionAsgn->default_option_selection]->display); ?>
                    <?php endif; ?>
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
                <td>
                    <?= $questionAsgn->active ? "Yes" : "No"; ?>
                </td>
            </tr>
        <?php } ?>
    </table>

    <?= \CHtml::button(
        'Add',
        [
            'class' => 'button large',
            'data-uri' => '/OphTrConsent/oeadmin/supplementaryConsent/editAssignment?question_id=' . $suppleconsent->id,
            'name' => 'Add new question type',
            'id' => 'et_add',
        ]
    ); ?>
<?php endif; ?>
</div>
