<?php
/**
 * OpenEyes.
 *
 *
 * Copyright OpenEyes Foundation, 2017
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'js-add-new-contact-form',
));
?>
<div class="alert-box error with-icon js-contact-error-box" style="display:none">
    <p>Please fix the following input errors:</p>
    <ul class="js-contact-errors">
    </ul>
</div>
<table>
    <colgroup>
        <col class="cols-3">
    </colgroup>
    <tbody>
    <tr>
        <td>
            <?= "First Name" ?>
        </td>
        <td>
            <?php echo \CHtml::textField(
                'field',
                "",
                ['data-label' => 'first_name',
                    'class' => 'cols-full js-contact-field',
                    'placeholder' => "First Name"
                ]
            ); ?>
        </td>
    </tr>
    <tr>
        <td>
            <?= "Last Name" ?>
        </td>
        <td>
            <?php echo \CHtml::textField(
                'field',
                "",
                ['data-label' => 'last_name',
                    'class' => 'cols-full js-contact-field',
                    'placeholder' => "Last Name"
                ]
            ); ?>
        </td>
    </tr>
    <tr>
        <td>
            <?= "Email" ?>
        </td>
        <td>
            <?php echo \CHtml::textField(
                'field',
                "",
                ['data-label' => 'email',
                    'class' => 'cols-full js-contact-field',
                    'placeholder' => "Email"
                ]
            ); ?>
        </td>
    </tr>
    <tr>
        <td>
            <?= "Phone Number" ?>
        </td>
        <td>
            <?php echo \CHtml::textField(
                'field',
                "",
                [
                    'data-label' => 'primary_phone',
                    'class' => 'cols-full js-contact-field',
                    'placeholder' => "Phone Number"
                ]
            ); ?>
        </td>
    </tr>
    <tr>
        <td>
            <?= "Address Line One" ?>
        </td>
        <td>
            <?php echo \CHtml::textField(
                'field',
                "",
                ['data-label' => 'address1',
                    'class' => 'cols-full js-contact-field',
                    'placeholder' => "Address Line One"
                ]
            ); ?>
        </td>
    </tr>
    <tr>
        <td>
            <?= "Address Line Two" ?>
        </td>
        <td>
            <?php echo \CHtml::textField(
                'field',
                "",
                ['data-label' => 'address2',
                    'class' => 'cols-full js-contact-field',
                    'placeholder' => "Address Line Two"
                ]
            ); ?>
        </td>
    </tr>

    <tr>
        <td>
            <?= "City" ?>
        </td>
        <td>
            <?php echo \CHtml::textField(
                'field',
                "",
                ['data-label' => 'city',
                    'class' => 'cols-full js-contact-field',
                    'placeholder' => "City"
                ]
            ); ?>
        </td>
    </tr>
    <tr>
        <td>
            Country
        </td>
        <td>
            <?= \CHtml::dropDownList(
                'country_id',
                '',
                CHtml::listData(
                    Country::model()->findAll(),
                    'id',
                    'name',
                    'code
                        '
                ),
                [
                    'empty' => 'None',
                    'options' => array(Address::model()->getDefaultCountryId() => array('selected' => true)),
                    'class' => 'cols-full js-contact-field', 'data-label' => 'country'
                ]
            ); ?>
        </td>
    </tr>
    <tr>
        <td>
            <?= "Postcode" ?>
        </td>
        <td>
            <?php echo \CHtml::textField(
                'field',
                "",
                ['data-label' => 'postcode',
                    'class' => 'cols-full js-contact-field',
                    'placeholder' => "Postcode"
                ]
            ); ?>
        </td>
    </tr>
    <tr>
        <td>
            Contact Type
        </td>
        <td>
            <?= \CHtml::dropDownList(
                'contact_label_id',
                '',
                CHtml::listData(
                    ContactLabel::model()->findAll(
                        [
                            'select' => 't.name,t.id',
                            'group' => 't.name',
                            'distinct' => true
                        ]
                    ),
                    'id',
                    'name'
                ),
                [
                    'empty' => 'None',
                    'class' => 'cols-full js-contact-field',
                    'data-label' => 'contact_label_id',
                    'options' => array($selected_contact_type => array('selected' => true)),
                ]
            ); ?>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="align-right">
            <?= \CHtml::submitButton('Submit', array('class' => 'green hint js-add-new-contact')); ?>
        </td>
    </tr>
    </tbody>
</table>

<?php $this->endWidget(); ?>
<script>
    $('#contact_label_id').trigger('change');
</script>