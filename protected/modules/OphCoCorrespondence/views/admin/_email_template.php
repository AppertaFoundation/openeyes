<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

Yii::app()->clientScript->registerScriptFile("{$this->assetPath}/js/InitMethod.js", \CClientScript::POS_HEAD);
Yii::app()->clientScript->registerScriptFile(
    "{$this->assetPath}/js/OpenEyes.OphCoCorrespondence.LetterMacro.js",
    \CClientScript::POS_HEAD
);
/**
 * @var $form BaseEventTypeCActiveForm
 * @var $macro LetterMacro
 * @var $none_option String
 * @var $template EmailTemplate
 * @var $errors array
 */
?>

<h2><?= $title; ?> Email Template</h2>
<?php echo $this->renderPartial('_form_errors', array('errors' => $errors)) ?>
<?php
$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
    'id' => 'adminform',
    'enableAjaxValidation' => false,
    'layoutColumns' => array(
        'label' => 2,
        'field' => 1,
    ),
));
?>
<div class="cols-10">
    <table class="standard">
        <colgroup>
            <col class="cols-2">
            <col class="cols-4">
            <col class="cols-2">
            <col class="cols-2">
        </colgroup>
        <tbody>
            <tr>
                <td>Institution</td>
                <td>
                    <?= Institution::model()->getCurrent()->name ?>
                </td>
            </tr>
            <tr>
                <td>Site</td>
                <td>
                    <?= CHtml::activeDropDownList(
                        $template,
                        'site_id',
                        CHtml::listData(Institution::model()->getCurrent()->sites, 'id', 'name'),
                        array('empty' => 'None', 'class' => 'cols-full')
                    ) ?>
                </td>
            </tr>
            <tr>
                <td>Recipient Type</td>
                <td>
                    <?php $contactTypes = Document::getContactTypes();
                    $contactTypes['INTERNALREFERRAL'] = 'Internal Referral';
                    ?>
                    <?= CHtml::activeDropDownList(
                        $template,
                        'recipient_type',
                        $contactTypes,
                        array('empty' => 'Select', 'class' => 'cols-full')
                    ) ?>
                </td>
            </tr>
            <tr>
                <td>Title</td>
                <td>
                    <?= $form->textField(
                        $template,
                        'title',
                        array(
                            'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
                            'class' => 'cols-full',
                            'nowrapper' => true,
                        )
                    ) ?>
                </td>
            </tr>
            <tr>
                <td>Subject</td>
                <td>
                    <?= $form->textField(
                        $template,
                        'subject',
                        array(
                            'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
                            'class' => 'cols-full',
                            'nowrapper' => true,
                        )
                    ) ?>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <?= $template->getAttributeLabel('body') ?><br/><br/>
                    <?= \CHtml::activeTextField($template, 'body', ['class' => 'cols-full']); ?>
                </td>
            </tr>
            <tr>
                <td>Add shortcode</td>
                <td colspan="2">
                    <?= \CHtml::dropDownList(
                        'shortcode',
                        '',
                        CHtml::listData(
                            PatientShortcode::model()->findAll(['order' => 'description asc']),
                            'code',
                            'description'
                        ),
                        array('empty' => '- Select -', 'class' => 'cols-full', 'nowrapper' => true,)
                    ) ?>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">
                    <?= CHtml::submitButton(
                        'Save',
                        [
                            'class' => 'button large',
                            'name' => 'save',
                            'id' => 'et_save'
                        ]
                    ) ?>
                    <?= CHtml::submitButton(
                        'Cancel',
                        [
                            'class' => 'button large',
                            'data-uri' => '/OphCoCorrespondence/admin/emailTemplates',
                            'name' => 'cancel',
                            'id' => 'et_cancel'
                        ]
                    ) ?>
                </td>
            </tr>
        </tfoot>
    </table>
</div>
<?php $this->endWidget() ?>

<script type="text/javascript">
    $(document).ready(function () {
        new OpenEyes.OphCoCorrespondence.InitMethodController();

        var emailTemplateController = new OpenEyes.OphCoCorrespondence.LetterMacroController(
            "EmailTemplate_body",
            <?= json_encode(\Yii::app()->params['tinymce_default_options'])?>
        );
        emailTemplateController.connectDropdown($("select#shortcode"));

        getEmailBody(emailTemplateController);
    });

    function getEmailBody(emailTemplateController) {
        $('select#EmailTemplate_recipient_type').on('change', function () {
            let recipient_type = $('select#EmailTemplate_recipient_type').val();
            $.ajax({
                url: baseUrl + '/OphCoCorrespondence/admin/getEmailBody/',
                data: {recipient_type: recipient_type},
                type: 'GET',
                success: function (response) {
                    if (emailTemplateController.getContent() !== '') {
                        emailTemplateController.setContent('');
                    }
                    emailTemplateController.addAtCursor(response);
                }
            });
        });
    }
</script>
