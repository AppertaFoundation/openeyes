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
    <h2>Edit Supplementary Consent Question Answer</h2>
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
                $text_fileds = ['display_order','name', 'display', 'answer_output',];
                foreach ($text_fileds as $field) { ?>
                    <tr>
                        <td><?= $q_assign->getAttributeLabel($field) ?></td>
                        <td>
                            <?= \CHtml::activeTextField(
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
                'data-uri' => '/OphTrConsent/oeadmin/SupplementaryConsent/editAssignment/' . $q_assign->question_assignment_id,
                'name' => 'cancel',
                'id' => 'et_cancel'
            ]
        ); ?>
    </form>
</div>

<script type="text/javascript">
    $('#SupplementaryConsent_optionResponse').change(function() {
            var selectedValue = parseInt(jQuery(this).val());
            var inputContainer = document.getElementById("optionCheckBox");
            for (var i = 0; i < selectedValue; i++) {
                const j = i + 1;
                $('#optionCheckBox').append(' <label for="fname">Option' + j + ':</label>');
                $('#optionCheckBox').append(' <input type="text" value=""  name="SupplementaryConsent[option' +
                    j + ']">');
            }
        }
    );
</script>
