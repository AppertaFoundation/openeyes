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

/**
 * @var $form BaseEventTypeCActiveForm
 * @var $macro LetterMacro
 * @var $none_option String
 * @var $senderEmailAddresses SenderEmailAddresses
 * @var $errors array
 */
?>

<h2><?= $title; ?> Email Address</h2>
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

<div class="cols-6">
    <table class="standard">
        <colgroup>
            <col class="cols-2">
            <col class="cols-4">
        </colgroup>
        <tbody>
            <tr>
                <td>Host</td>
                <td>
                    <?php echo $form->textField(
                        $senderEmailAddresses,
                        'host',
                        array(
                            'autocomplete' => Yii::app()->params['html_autocomplete'],
                            'class' => 'cols-full',
                            'nowrapper' => true,
                        )
                    ) ?>
                </td>
            </tr>
            <tr>
                <td>Username</td>
                <td>
                    <?php echo $form->textField(
                        $senderEmailAddresses,
                        'username',
                        array(
                            'autocomplete' => Yii::app()->params['html_autocomplete'],
                            'class' => 'cols-full',
                            'nowrapper' => true,
                        )
                    ) ?>
                </td>
            </tr>
            <tr>
                <td>Password</td>
                <td>
                    <?= CHtml::activePasswordField(
                        $senderEmailAddresses,
                        'password',
                        array(
                            'autocomplete' => Yii::app()->params['html_autocomplete'],
                            'class' => 'cols-full',
                            'nowrapper' => true,
                        )
                    ) ?>
                </td>
            </tr>
            <tr>
                <td>Reply-To Address</td>
                <td>
                    <?= $form->textField(
                        $senderEmailAddresses,
                        'reply_to_address',
                        array(
                            'autocomplete' => Yii::app()->params['html_autocomplete'],
                            'class' => 'cols-full',
                            'nowrapper' => true,
                        )
                    ) ?>
                </td>
            </tr>
            <tr>
                <td>Port</td>
                <td>
                    <?php echo $form->textField(
                        $senderEmailAddresses,
                        'port',
                        array(
                            'autocomplete' => Yii::app()->params['html_autocomplete'],
                            'class' => 'cols-full',
                            'nowrapper' => true,
                        )
                    ) ?>
                </td>
            </tr>
            <tr>
                <td>Security</td>
                <td>
                    <?= CHtml::activeDropDownList(
                        $senderEmailAddresses,
                        'security',
                        array('ssl' => 'SSL', 'tls' => 'TLS'),
                        array('empty' => 'None', 'class' => 'cols-full')
                    ) ?>
                </td>
            </tr>
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
                        $senderEmailAddresses,
                        'site_id',
                        CHtml::listData(Institution::model()->getCurrent()->sites, 'id', 'name'),
                        array('empty' => 'None', 'class' => 'cols-full')
                    ) ?>
                </td>
            </tr>
            <tr>
                <td>Domain</td>
                <td>
                    <?php echo $form->textField(
                        $senderEmailAddresses,
                        'domain',
                        array(
                            'autocomplete' => Yii::app()->params['html_autocomplete'],
                            'class' => 'cols-full',
                            'nowrapper' => true,
                        )
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
                            'data-uri' => '/OphCoCorrespondence/admin/senderEmailAddresses',
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