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
                'new_modal_first_name',
                "",
                [
                    'data-label' => 'First Name',
                    'data-name' => 'first_name',
                    'required' => 'required',
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
                'new_modal_last_name',
                "",
                [
                    'data-label' => 'Last Name',
                    'data-name' => 'last_name',
                    'required' => 'required',
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
                'new_modal_email',
                "",
                [
                    'data-label' => 'Email',
                    'data-name' => 'email',
                    'required' => 'required',
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
                'new_modal_primary_phone',
                "",
                [
                    'data-label' => 'Primary Phone',
                    'data-name' => 'phone_number',
                    'required' => 'required',
                    'class' => 'cols-full js-contact-field',
                    'placeholder' => "Phone Number"
                ]
            ); ?>
        </td>
    </tr>
    <tr>
        <td>
            <?= "Mobile Number" ?>
        </td>
        <td>
            <?php echo \CHtml::textField(
                'new_modal_mobile_phone',
                "",
                [
                    'data-label' => 'Mobile Phone',
                    'data-name' => 'mobile_number',
                    'required' => '',
                    'class' => 'cols-full js-contact-field',
                    'placeholder' => "Mobile Number"
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
                'new_modal_address1',
                "",
                [
                    'data-label' => 'Address Line One',
                    'data-name' => 'address_line1',
                    'required' => 'required',
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
                'new_modal_address2',
                "",
                [
                    'data-label' => 'Address Line Two',
                    'data-name' => 'address_line2',
                    'required' => '',
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
                'new_modal_city',
                "",
                [
                    'data-label' => 'City',
                    'data-name' => 'city',
                    'required' => 'required',
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
                'new_modal_country_id',
                '',
                CHtml::listData(Country::model()->findAll(), 'id', 'name', 'code'),
                [
                    'data-label' => 'Country',
                    'data-name' => 'country_id',
                    'required' => 'required',
                    'empty' => 'None',
                    'options' => array(Address::model()->getDefaultCountryId() => array('selected' => true)),
                    'class' => 'cols-full js-contact-field',
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
                'new_modal_postcode',
                "",
                [
                    'data-label' => 'Postcode',
                    'data-name' => 'postcode',
                    'required' => 'required',
                    'class' => 'cols-full js-contact-field',
                    'placeholder' => "Postcode"
                ]
            ); ?>
        </td>
    </tr>
    <tr>
        <td>
            Relationship
        </td>
        <td>
            <?= $selected_relationship ?? '-' ?>
        </td>
    </tr>
    <tr>
        <td>
            Contact method
        </td>
        <td>
            <?= $selected_contact_method ?? '-' ?>
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

