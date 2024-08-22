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
        <h2>Edit contact</h2>
    </div>

    <?php echo $this->renderPartial('_form_errors', array('errors' => $errors)) ?>
    <?php
    $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => 'adminform',
        'enableAjaxValidation' => false,
        'focus' => '#contactname',
        'layoutColumns' => array(
            'label' => 2,
            'field' => 5,
        ),
    )); ?>

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

    <?php $this->endWidget() ?>

    <div class="row divider">
        <h2>Addresses</h2>
    </div>

    <form id="admin_contact_addresses">
        <table class="standard">
            <thead>
            <tr>
                <th>Email</th>
                <th>Address Line One</th>
                <th>Address Line Two</th>
                <th>City</th>
                <th>Postcode</th>
                <th>County</th>
                <th>Country</th>
                <th>Date Start</th>
                <th>Date End</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($contact->addresses as $index => $address) { ?>
                <tr class="clickable" data-id="<?php echo $address->id ?>"
                    data-uri="Admin/address/edit?id=<?php echo $address->id ?>&contact_id=<?= $contact->id?>">
                    <td><?= $contact->email ?></td>
                    <td><?= $address->address1 ?></td>
                    <td><?= $address->address2 ?></td>
                    <td><?= $address->city ?></td>
                    <td><?= $address->postcode ?></td>
                    <td><?= $address->county ?></td>
                    <td><?= $address->country->name ?></td>
                    <td><?= $address->date_start ?></td>
                    <td><?= $address->date_end ?></td>
                    <td>
                        <button type="button" class="removeAddress hint red" rel="<?php echo $address->id ?>">
                            Remove
                        </button>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot class="pagination-container">
            <tr class="js-address-add-container" style="display:<?= $contact->addresses ? "none" : ""?>">
                <td colspan="9">
                    <?= CHtml::link(
                        'Add',
                        '/Admin/address/add?contact_id=' . $contact->id,
                        ['class' => 'button large']
                    ); ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>

    <div class="row divider">
        <h2>Locations</h2>
    </div>

    <form id="admin_contact_locations">
        <table class="standard">
            <thead>
            <tr>
                <th>Type</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($contact->locations as $i => $address) { ?>
                <tr class="clickable" data-id="<?php echo $address->id ?>"
                    data-uri="admin/contactLocation?location_id=<?php echo $address->id ?>">
                    <td><?php echo $address->site_id ? 'Site' : 'Institution' ?></td>
                    <td><?php echo $address->site_id ? $address->site->name : $address->institution->name ?></td>
                    <td>
                        <button type="button" class="removeLocation hint red" rel="<?php echo $address->id ?>">
                            Remove
                        </button>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot class="pagination-container">
            <tr>
                <td colspan="3">
                    <?= CHtml::link(
                        'Add',
                        '/admin/addContactLocation?contact_id=' . $contact->id,
                        ['class' => 'button large']
                    ); ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>

<script type="text/javascript">
    $('.removeLocation').click(function (e) {
        e.preventDefault();

        var location_id = $(this).attr('rel');

        var row = $(this).parent().parent();

        $.ajax({
            'type': 'POST',
            'data': 'location_id=' + location_id + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
            'url': baseUrl + '/admin/removeLocation',
            'success': function (resp) {
                if (resp == "0") {
                    new OpenEyes.UI.Dialog.Alert({
                        content: "This contact location is currently associated with one or more patients and so cannot be removed.\n\nYou can click on the location row to view the patients involved."
                    }).open();
                } else if (resp == "-1") {
                    new OpenEyes.UI.Dialog.Alert({
                        content: "There was an unexpected error trying to remove the location, please try again or contact support for assistance."
                    }).open();
                } else {
                    row.remove();
                }
            }
        });
    });

    $('.removeAddress').click(function (e) {
        e.preventDefault();

        var address_id = $(this).attr('rel');

        var row = $(this).parent().parent();

        $.ajax({
            'type': 'POST',
            'data': 'address_id=' + address_id + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
            'url': baseUrl + '/Admin/address/delete',
            'success': function (resp) {
                if (resp == "0") {
                    new OpenEyes.UI.Dialog.Alert({
                        content: "This contact location is currently associated with one or more patients and so cannot be removed.\n\nYou can click on the location row to view the patients involved."
                    }).open();
                } else if (resp == "-1") {
                    new OpenEyes.UI.Dialog.Alert({
                        content: "There was an unexpected error trying to remove the location, please try again or contact support for assistance."
                    }).open();
                } else {
                    row.remove();
                    $('.js-address-add-container').show();
                }
            }
        });
    });
</script>
