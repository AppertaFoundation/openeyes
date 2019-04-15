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
<table class="standard">
    <colgroup>
        <col class="cols-3">
    </colgroup>
    <tbody>
    <tr>
        <td class="title">
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
        <td class="title">
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
        <td class="title">
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
        <td class="title">
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
        <td class="title">
            <?= "Address Line One" ?>
        </td>
        <td>
            <?php echo \CHtml::textField(
                'field',
                "",
                ['data-label' => 'address1',
                    'class' => 'cols-full js-contact-field',
                    'placeholder' => "Address Line One\""
                ]
            ); ?>
        </td>
    </tr>
    <tr>
        <td class="title">
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
        <td class="title">
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
        <td class="title">
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
        <td class="title">
            Contact Type
        </td>
        <td>
            <?= \CHtml::dropDownList(
                'contact_label_id',
                '',
                CHtml::listData(
                    ContactLabel::model()->findAll(),
                    'id',
                    'name'
                ),
                ['empty' => 'None', 'class' => 'cols-full js-contact-field', 'data-label' => 'contact_label_id']
            ); ?>
        </td>
    </tr>
    <tr>
        <td class="title">
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
        <td colspan="2" class="align-right">
            <?= \CHtml::submitButton('Submit', array('class' => 'green hint js-add-new-contact')); ?>
        </td>
    </tr>
    </tbody>
</table>

<?php $this->endWidget(); ?>

<script>
    $(document).ready(function () {
        $('.js-add-new-contact').on('click', function (event) {
            event.preventDefault();
            let data = {};
            $('.js-contact-field').each(function () {
                data[$(this).data('label')] = $(this).val();
                //  console.log(data);
            })

            console.log(JSON.stringify(data));

            // do ajax to save contact and new address
            $.ajax({
                'type': 'POST',
                'data': "data=" + JSON.stringify(data) + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
                'url': baseUrl + '/OphCiExamination/contact/saveNewContact',
                'success': function (resp) {

                    resp = JSON.parse(resp);
                    data = {};
                    data.id = resp.id;
                    data.label = resp.contact_label;
                    data.full_name = resp.name;
                    data.email = resp.email;
                    data.phone = resp.phone;
                    data.address = resp.address;
                    let templateText = $('#contact-entry-template').text();
                    row = Mustache.render(templateText, data);
                    $('#contact-assignment-table').append(row);

                    $('.oe-popup-wrap').remove();


                }
            });
        });
    });
</script>

