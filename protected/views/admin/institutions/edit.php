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

<div class="cols-7">

    <?php echo $this->renderPartial('_form_errors', array('errors' => $errors)) ?>
    <?php
    $form = $this->beginWidget(
        'BaseEventTypeCActiveForm',
        [
            'id' => 'adminform',
            'enableAjaxValidation' => false,
            'focus' => '#username',
            'layoutColumns' => array(
                'label' => 2,
                'field' => 5,
            ),
        ]
    ) ?>

    <div class="row divider">
        <h2>Edit institution</h2>
    </div>

    <table class="standard cols-full">
        <colgroup>
            <col class="cols-2">
            <col class="cols-5">
        </colgroup>

        <tbody>
        <?php foreach (['name', 'remote_id'] as $field) : ?>
            <tr>
                <td><?php echo $institution->getAttributeLabel($field); ?></td>
                <td>
                    <?= \CHtml::activeTextField(
                        $institution,
                        $field,
                        ['class' => 'cols-full']
                    ); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php
        $address_fields = ['address1', 'address2', 'city', 'county', 'postcode'];
        foreach ($address_fields as $field) : ?>
            <tr>
                <td><?php echo $address->getAttributeLabel($field); ?></td>
                <td>
                    <?= \CHtml::activeTextField(
                        $address,
                        $field,
                        ['class' => 'cols-full']
                    ); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td>Country</td>
            <td>
                <?= \CHtml::activeDropDownList(
                    $address,
                    'country_id',
                    CHtml::listData(Country::model()->findAll(), 'id', 'name'),
                    ['class' => 'cols-full']
                ); ?>
            </td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="5">
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
                        'data-uri' => '/admin/institutions',
                        'name' => 'cancel',
                        'id' => 'et_cancel'
                    ]
                ); ?>
            </td>
        </tr>
        </tfoot>
    </table>

    <?php $this->endWidget() ?>

    <br>

    <h2>Sites</h2>
    <hr class="divider">
    <form id="admin_institution_sites">
        <table class="standard">
            <thead>
            <tr>
                <th>ID</th>
                <th>Remote ID</th>
                <th>Name</th>
                <th>Address</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($institution->sites as $site) { ?>
                <tr class="clickable"
                    data-id="<?php echo $site->id ?>"
                    data-uri="admin/editsite?site_id=<?php echo $site->id ?>">
                    <td><?php echo $site->id ?></td>
                    <td><?php echo $site->remote_id ?>&nbsp;</td>
                    <td><?php echo $site->name ?>&nbsp;</td>
                    <td>
                        <?php echo $site->getLetterAddress(
                            array('delimiter' => ', ')
                        ) ?>&nbsp
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </form>
</div>
