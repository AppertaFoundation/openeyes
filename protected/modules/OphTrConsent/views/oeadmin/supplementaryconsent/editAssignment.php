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
    <h2>Edit Supplementary Consent Question Assignment</h2>
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
                $purifier = new CHtmlPurifier();
                $text_fileds = ['question_text', 'question_info', 'question_output',];
                foreach ($text_fileds as $field) { ?>
                    <tr>
                        <td><?= $q_assign->getAttributeLabel($field) ?></td>
                        <td>
                            <?= CHtml::activeTextField(
                                $q_assign,
                                $field,
                                [
                                    'autocomplete' => Yii::app()->params['html_autocomplete'],
                                    'class' => 'cols-full'
                                ]
                            ); ?>
                        </td>
                    </tr>
                <?php } ?>
                <?php if ($q_assign->question->question_type->name !== 'radio' && $q_assign->question->question_type->name !== 'dropdown') : ?>
                <tr>
                    <td>Minimum answer length/choices</td>
                    <td>
                        <?= \CHtml::activeTextField(
                            $q_assign,
                            'minimum_selected',
                            [
                                'class' => 'cols-full',
                                'value' => $q_assign->minimum_selected ?? '0',
                            ]
                        ); ?>
                    </td>
                </tr>
                <tr>
                    <td>Maximum answer length/choices</td>
                    <td>
                        <?= \CHtml::activeTextField(
                            $q_assign,
                            'maximum_selected',
                            [
                                'class' => 'cols-full'
                            ]
                        ); ?>
                    </td>
                </tr>
                <?php endif; ?>
                <?php if (isset($q_assign->id) && !$q_assign->question->question_type->text_based && count($q_assign->answers) > 0) : ?>
                <tr>
                    <td>
                        <?= $q_assign->getAttributeLabel('default_option_selection'); ?>
                    </td>
                    <td>
                        <?= CHtml::activeDropDownList(
                            $q_assign,
                            'default_option_selection',
                            array_map(static function ($answer) use ($purifier) {
                                return $purifier->purify($answer->display);
                            }, $q_assign->answers),
                        ); ?>
                    </td>
                </tr>
                <?php elseif ($q_assign->question->question_type->text_based) : ?>
                <tr>
                    <td>
                        <?= $q_assign->getAttributeLabel('default_option_text'); ?>
                    </td>
                    <td>
                        <?= CHtml::activeTextField(
                            $q_assign,
                            'default_option_text',
                        ); ?>
                    </td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td>Institution</td>
                    <td>
                        <?php
                        $ins = Institution::model()->findAll(array('select' => 'id, name', 'order' => 'id'));
                        $list = CHtml::listData($ins, 'id', 'name');
                        // Permit only those with an installation admin role to choose 'All institutions'
                        if ($this->checkAccess('admin')) {
                            echo CHtml::activeDropDownList(
                                $q_assign,
                                'institution_id',
                                $list,
                                array('empty' => 'All institutions')
                            );
                        } else {
                            echo CHtml::activeDropDownList(
                                $q_assign,
                                'institution_id',
                                $list
                            );
                        } ?>
                    </td>
                </tr>
                <tr>
                    <td>Site</td>
                    <td>
                        <?php
                        $sites = Site::model()->findAll(array('select' => 'id, name', 'order' => 'id'));
                        $list = CHtml::listData($sites, 'id', 'name');
                        echo CHtml::activeDropDownList(
                            $q_assign,
                            'site_id',
                            $list,
                            array('empty' => 'All sites')
                        ); ?>
                    </td>
                </tr>
                <tr>
                    <td>Subspecialty</td>
                    <td>
                        <?php
                        $subs = Subspecialty::model()->findAll(array('select' => 'id, name', 'order' => 'id'));
                        $list = CHtml::listData($subs, 'id', 'name');
                        echo CHtml::activeDropDownList(
                            $q_assign,
                            'subspecialty_id',
                            $list,
                            array('empty' => 'All subspecialties')
                        ); ?>
                    </td>
                </tr>
                <tr>
                    <td>Consent Form Type</td>
                    <td>
                        <?php
                        $cftype = OphTrConsent_Type_Type::model()->findAll(array('select' => 'id, name', 'order' => 'id'));
                        $list = CHtml::listData($cftype, 'id', 'name');
                        echo CHtml::activeDropDownList(
                            $q_assign,
                            'form_id',
                            $list,
                            array('empty' => 'All')
                        ); ?>
                    </td>
                </tr>
                <tr>
                    <td>Active</td>
                    <td>
                        <?=
                            \CHtml::activeRadioButtonList(
                                $q_assign,
                                'active',
                                [1 => 'Yes', 0 => 'No'],
                                ['separator' => ' ']
                            );?>
                    </td>
                </tr>
                <tr>
                    <td>Required</td>
                    <td>
                        <?=
                            \CHtml::activeRadioButtonList(
                                $q_assign,
                                'required',
                                [1 => 'Yes', 0 => 'No'],
                                ['separator' => ' ']
                            );?>
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
                'data-uri' => '/OphTrConsent/oeadmin/SupplementaryConsent/edit/' . $q_assign->question_id,
                'name' => 'cancel',
                'id' => 'et_cancel'
            ]
        ); ?>
    </form>
</div>
<?php if (isset($q_assign->id) && !$q_assign->question->question_type->text_based) : ?>
<br>
<h2>Local question answer options</h2>
<table class="standard cols-full">
    <thead>
        <tr>
            <th>Display order</th>
            <th>Name</th>
            <th>Text</th>
            <th>Print output</th>

        </tr>
    </thead>
    <tbody>
        <?php foreach ($q_assign->answers as $answer) { ?>
            <tr id="$key" class="clickable row divider" data-id="<?= $answer->id ?>" data-uri="OphTrConsent/oeadmin/supplementaryConsent/editAnswer/<?= $answer->id ?>">
                <td>
                    <?= $answer->display_order; ?>
                </td>
                <td>
                    <?= $purifier->purify($answer->name); ?>
                </td>
                <td>
                    <?= $purifier->purify($answer->display); ?>
                </td>
                <td>
                    <?= $purifier->purify($answer->answer_output); ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
    <?= \CHtml::button(
    'Add',
    [
        'class' => 'button large',
        'data-uri' => '/OphTrConsent/oeadmin/supplementaryConsent/editAnswer?question_assignment_id=' . $q_assign->id,
        'name' => 'Add new question type',
        'id' => 'et_add',
    ]
    ); ?>
<?php endif; ?>
