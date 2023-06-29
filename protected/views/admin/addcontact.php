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
<div class="cols-9">

    <div class="row divider">
        <h2>Add contact</h2>
    </div>
    <?php echo $this->renderPartial('_form_errors', array('errors' => $errors))?>
    <?php
    $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => 'adminform',
        'enableAjaxValidation' => false,
        'focus' => '#contactname',
        'layoutColumns' => array(
            'label' => 2,
            'field' => 5,
        ),
    ))?>

    <table class="standard">
        <colgroup>
            <col class="cols-2">
            <col class="cols-4">
        </colgroup>
        <tbody>
        <?php foreach (
        ['title', 'first_name', 'last_name',
                           'nick_name', 'primary_phone', 'mobile_phone', 'fax', 'email', 'qualifications' , 'national_code'] as $field
) : ?>
            <tr>
                <td><?= $contact->getAttributeLabel($field); ?></td>
                <td>
                    <?= CHtml::activeTextField($contact, $field, [
                        'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
                        'class' => 'cols-full'
                    ]); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td><?= $contact->getAttributeLabel('contact_label_id'); ?></td>
            <td>
                <?= CHtml::activeDropDownList(
                    $contact,
                    'contact_label_id',
                    CHtml::listData(ContactLabel::model()->active()->findAll(['order' => 'name']), 'id', 'name'),
                    ['class' => 'cols-full']
                ); ?>
            </td>
        </tr>
        <tr>
            <td><?= $contact->getAttributeLabel('active'); ?></td>
            <td>
                <?= CHtml::activeCheckBox(
                    $contact,
                    'active'
                ); ?>
            </td>
        </tr>
        </tbody>
        <tfoot>
        <tr class="pagination-container">
            <td colspan="3">
                <?= CHtml::submitButton('Save', [
                    'class' => 'button large',
                ]) ?>
                <?= CHtml::link('Cancel', '/admin/contacts', [
                    'class' => 'button large',
                ]) ?>
            </td>
        </tr>
        </tfoot>
    </table>

        <?php $this->endWidget()?>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#Contact_title').focus();
    });
</script>
