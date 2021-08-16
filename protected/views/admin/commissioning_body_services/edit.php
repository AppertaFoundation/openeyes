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

<div class="cols-5">

    <div class="row divider">
        <h2><?php echo $cbs->id ? 'Edit' : 'Add' ?> commissioning body service</h2>
    </div>


    <?php echo $this->renderPartial(
        '//admin/_form_errors',
        ['errors' => $errors]
    ) ?>

    <?php
    $form = $this->beginWidget(
        'BaseEventTypeCActiveForm',
        [
            'id' => 'adminform',
            'enableAjaxValidation' => false,
            'focus' => '#username',
            'layoutColumns' => array(
                'label' => 2,
                'field' => 5
            )
        ]
    );

    $criteria = new CDbCriteria();
    $criteria->order = 't.name asc';
    if ($commissioning_bt) {
        $criteria->addColumnCondition(
            ['commissioning_body_type_id' => $commissioning_bt->id]
        );
    }
    ?>

    <table class="standard cols-full">
        <colgroup>
            <col class="cols-2">
            <col class="cols-5">
        </colgroup>
        <tbody>
        <tr>
            <td>Commissioning body:</td>
            <td>
                <?= \CHtml::activeDropDownList(
                    $cbs,
                    'commissioning_body_id',
                    CHtml::listData(
                        CommissioningBody::model()->findAll($criteria),
                        'id',
                        'name'
                    ),
                    ['class' => 'cols-full']
                ); ?>
            </td>
        </tr>
        <tr>
            <td>Service type:</td>
            <td>
                <?php if ($commissioning_bst) { ?>
                    <div id="div_CommissioningBodyService_commissioning_body_service_type_id" class="data-group">
                        <div class="cols-5 column end">
                            <?php
                            echo $form->hiddenInput(
                                $cbs,
                                'commissioning_body_service_type_id',
                                $commissioning_bst->id
                            );
                            echo $commissioning_bst->name;
                            ?>
                        </div>
                    </div>
                <?php } else {
                    echo CHtml::activeDropDownList(
                        $cbs,
                        'commissioning_body_service_type_id',
                        CHtml::listData(
                            CommissioningBodyServiceType::model()->findAll($criteria),
                            'id',
                            'name'
                        ),
                        ['class' => 'cols-full']
                    ); ?>
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td>Name</td>
            <td> <?= \CHtml::activeTextField(
                $cbs,
                'name',
                ['class' => 'cols-full',
                    'autocomplete' => Yii::app()->params['html_autocomplete']]
            ); ?> </td>
        </tr>
        <tr>
            <td>Code</td>
            <td> <?= \CHtml::activeTextField(
                $cbs,
                'code',
                [
                    'class' => 'cols-full',
                    'autocomplete' => Yii::app()->params['html_autocomplete'],
                    'field' => 2
                ]
            ); ?> </td>
        </tr>
        <tr>
            <td>Phone number</td>
            <td>
                <?php
                if (!$cbs->contact) {
                    $cbs->contact = new Contact();
                }
                echo CHtml::activeTextField(
                    $cbs->contact,
                    'primary_phone',
                    [
                        'class' => 'cols-full',
                        'autocomplete' => Yii::app()->params['html_autocomplete'],
                        'field' => 2
                    ]
                );
                ?>
            </td>
        </tr>
        <tr>
            <td>Email</td>
            <td>
                <?php
                echo CHtml::activeTextField(
                    $cbs->contact,
                    'email',
                    [
                        'class' => 'cols-full',
                        'autocomplete' => Yii::app()->params['html_autocomplete']
                    ]
                ); ?>
            </td>
        </tr>

        <tr>
            <td>First Name</td>
            <td>
                <?php
                echo CHtml::activeTextField(
                    $cbs->contact,
                    'first_name',
                    [
                        'class' => 'cols-full',
                        'autocomplete' => Yii::app()->params['html_autocomplete']
                    ]
                ); ?>
            </td>
        </tr>

        <tr>
            <td>Last Name</td>
            <td>
                <?php
                echo CHtml::activeTextField(
                    $cbs->contact,
                    'last_name',
                    [
                        'class' => 'cols-full',
                        'autocomplete' => Yii::app()->params['html_autocomplete']
                    ]
                ); ?>
            </td>
        </tr>

        <?php
        $address_fields = ['address1', 'address2', 'city', 'county', 'postcode'];
        foreach ($address_fields as $field) : ?>
            <tr>
                <td><?php echo $address->getAttributeLabel($field); ?></td>
                <td>
                    <?= \CHtml::activeTextField(
                        $address,
                        $field,
                        [
                            'class' => 'cols-full',
                            'autocomplete' => Yii::app()->params['html_autocomplete']
                        ]
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
                    CHtml::listData(
                        Country::model()->findAll(),
                        'id',
                        'name'
                    ),
                    ['class' => 'cols-full']
                ); ?>
            </td>
        </tr>

        <tfoot>
        <tr>
            <td colspan="2">
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
                        'data-uri' => $return_url,
                        'name' => 'cancel',
                        'id' => 'et_cancel'
                    ]
                ); ?>
            </td>
        </tr>
        </tfoot>
    </table>

    <?php $this->endWidget() ?>
</div>

