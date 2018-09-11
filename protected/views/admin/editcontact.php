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
?>
    <main class="oe-full-main admin-main">
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
        <div class="cols-6">
            <table class="standard">
                <colgroup>
                    <col class="cols-2">
                    <col class="cols-4">
                </colgroup>
                <tbody>
                <?php foreach (['title', 'first_name', 'last_name', 'nick_name', 'primary_phone', 'qualifications'] as $field) : ?>
                    <tr>
                        <td><?= $contact->getAttributeLabel($field); ?></td>
                        <td>
                            <?= CHtml::activeTextField($contact, $field, [
                                'autocomplete' => Yii::app()->params['html_autocomplete'],
                                'class' => 'cols-full'
                            ]); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td><?= $contact->getAttributeLabel('contact_label_id'); ?></td>
                    <td>
                        <?= CHtml::activeDropDownList($contact, 'contact_label_id',
                            CHtml::listData(ContactLabel::model()->active()->findAll(['order' => 'name']), 'id', 'name'), [
                                'class' => 'cols-full'
                            ]
                        ); ?>
                    </td>
                </tr>
                </tbody>
                <tfoot>
                <tr class="pagination-container">
                    <td colspan="3">
                        <?= CHtml::htmlButton('Save', [
                                'class' => 'button large',
                                'type' => 'submit'
                        ]) . ' ' .
                         CHtml::link('Cancel', '/admin/contacts', [
                                'class' => 'button large',
                        ]);
                        ?>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
        <?php $this->endWidget() ?>
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
                foreach ($contact->locations as $i => $location) { ?>
                    <tr class="clickable" data-id="<?php echo $location->id ?>"
                        data-uri="admin/contactLocation?location_id=<?php echo $location->id ?>">
                        <td><?php echo $location->site_id ? 'Site' : 'Institution' ?></td>
                        <td><?php echo $location->site_id ? $location->site->name : $location->institution->name ?>
                            &nbsp;
                        </td>
                        <td><button type="button" class="removeLocation hint red" rel="<?php echo $location->id ?>">Remove</button></td>
                    </tr>
                <?php } ?>
                </tbody>
                <tfoot class="pagination-container">
                <tr>
                    <td colspan="3">
                        <?= CHtml::link('Add', '/admin/addContactLocation?contact_id=' . $contact->id, [
                                'class' => 'button large',
                        ]);?>
                    </td>
                </tr>
                </tfoot>
            </table>
        </form>

    </main>
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
</script>
