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
 * @copyright Copyright (C) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<div class="row divider">
    <h2>
        <?php echo $firm->id ? 'Edit' : 'Add' ?>
        <?php echo Firm::contextLabel() . ' / ' . Firm::serviceLabel() ?>
    </h2>
</div>
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

<div class="cols-5">
    <table class="standard cols-full">
        <colgroup>
            <col class="cols-3">
            <col class="cols-5">
        </colgroup>
        <tbody>
        <tr>
            <td>Pass Code</td>
            <td> <?php echo CHtml::activeTextField(
                    $firm,
                    'pas_code',
                    ['class' => 'cols-full']
                ); ?> </td>
        </tr>
        <tr>
            <td>Name</td>
            <td> <?php echo CHtml::activeTextField(
                    $firm,
                    'name',
                    ['class' => 'cols-full']
                ); ?> </td>
        </tr>
        <tr class="col-gap">
            <td>Subspecialty</td>
            <td>
                <?php echo CHtml::activeDropDownList(
                    $firm,
                    'subspecialty_id',
                    CHtml::listData(
                        Subspecialty::model()->findAll(
                            array('order' => 'name')
                        ),
                        'id',
                        'name'
                    ),
                    ['class' => 'cols-full', 'empty' => '- None -']
                ); ?>
            </td>
        </tr>
        <tr class="col-gap">
            <td>Consultant</td>
            <td>
                <?php echo CHtml::activeDropDownList(
                    $firm,
                    'consultant_id',
                    CHtml::listData(
                        User::model()->findAll(
                            array('order' => 'first_name,last_name')
                        ),
                        'id',
                        'fullName'
                    ),
                    ['class' => 'cols-full', 'empty' => '- None -']
                ); ?>
            </td>
        </tr>
        <tr class="col-gap">
            <td>Service Enabled</td>
            <td><?php echo CHtml::activeCheckBox(
                    $firm,
                    'can_own_an_episode'
                ) ?></td>
        </tr>
        <tr class="col-gap">
            <td>Context Enabled:</td>
            <td><?php echo CHtml::activeCheckBox(
                    $firm,
                    'runtime_selectable'
                ) ?></td>
        </tr>
        <tr class="col-gap">
            <td>Active</td>
            <td><?php echo CHtml::activeCheckBox($firm, 'active') ?></td>
        </tr>
        </tbody>

        <tfoot>
        <tr>
            <td colspan="5">
                <?php echo CHtml::button(
                    'Save',
                    [
                        'class' => 'button large primary event-action',
                        'name' => 'save',
                        'type' => 'submit',
                        'id' => 'et_save'
                    ]
                ); ?>
                <?php echo CHtml::button(
                    'Cancel',
                    [
                        'class' => 'warning button large primary event-action',
                        'data-uri' => '/admin/firms',
                        'type' => 'submit',
                        'name' => 'cancel',
                        'id' => 'et_cancel'
                    ]
                ); ?>
            </td>
        </tr>
        </tfoot>
    </table>
</div>


<?php $this->endWidget() ?>

<?php if (isset($siteSecretaries) && $siteSecretaries) : ?>
    <?php echo $this->renderPartial(
        'application.modules.OphCoCorrespondence.views.admin.secretary.edit',
        [
            'errors' => array(),
            'siteSecretaries' => $siteSecretaries
        ]
    )
    ?>
<?php endif; ?>

