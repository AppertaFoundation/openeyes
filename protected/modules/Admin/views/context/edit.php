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
        <h2>
            <?=$firm->id ? 'Edit' : 'Add' ?>
            <?=Firm::contextLabel() . ' / ' . Firm::serviceLabel() ?>
        </h2>
    </div>

    <?=$this->renderPartial('//admin/_form_errors', array('errors' => $errors)) ?>
    <?php
    $form = $this->beginWidget(
        'BaseEventTypeCActiveForm',
        [
            'id' => 'adminform',
            'enableAjaxValidation' => false,
            'focus' => '#username',
            'layoutColumns' => array(
                'label' => 2,
                'field' => 10,
            ),
        ]
    ) ?>

    <table class="standard">
        <colgroup>
            <col class="cols-2">
            <col class="cols-4">
            <col class="cols-3">
        </colgroup>
        <tbody>
        <tr>
            <td>PAS Code</td>
            <td> <?=\CHtml::activeTextField($firm, 'pas_code', ['class' => 'cols-full']) ?> </td>
        </tr>
        <tr>
            <td>Name</td>
            <td> <?=\CHtml::activeTextField($firm, 'name', ['class' => 'cols-full']) ?> </td>
        </tr>

        <tr>
            <td>Institution</td>
            <?php if ($this->checkAccess('admin')) { ?>
                <td><?=\CHtml::activeDropDownList($firm, 'institution_id', Institution::model()->getList(), ['class' => 'cols-full']) ?></td>
            <?php } elseif ($firm->institution_id) { ?>
                <td>
                    <?= $firm->institution->name ?>
                    <?= CHtml::activeHiddenField($firm, 'institution_id') ?>
                </td>
                <td><i class='oe-i info small pad js-has-tooltip' data-tooltip-content="Only OpenEyes Administrators can change a firm's institution."></i></td>
            <?php } else { ?>
                <td>
                    <?= Institution::model()->getCurrent()->name ?>
                    <?= CHtml::activeHiddenField($firm, 'institution_id', array('value' => Institution::model()->getCurrent()->id)) ?>
                </td>
                <td><i class='oe-i info small pad js-has-tooltip' data-tooltip-content="Only OpenEyes Administrators can change a firm's institution."></td>
            <?php } ?>
        </tr>
        <tr>
            <td>Subspecialty</td>
            <td>
                <?=\CHtml::activeDropDownList($firm, 'subspecialty_id', $subspecialties_list_data, ['class' => 'cols-full', 'empty' => '- None -']) ?>
            </td>
        </tr>
        <tr>
            <td>Consultant</td>
            <td>
                <?=\CHtml::activeDropDownList($firm, 'consultant_id', $consultant_list_data, ['class' => 'cols-full', 'empty' => '- None -']) ?>
            </td>
        </tr>
        <tr>
            <td><?=$firm->getAttributeLabel('cost_code');?></td>
            <td> <?=\CHtml::activeTextField($firm, 'cost_code', ['class' => 'cols-full', ])?></td>
        </tr>

        <tr class="col-gap">
            <td>Service Enabled</td>
            <td><?=\CHtml::activeCheckBox(
                $firm,
                'can_own_an_episode',
                array('checked' => $firm->can_own_an_episode)
            ) ?></td>
            <td> <?= $form->textField($firm, 'service_email', array('placeholder' => 'Enter email address', 'class' => 'cols-full')) ?> </td>
        </tr>
        <tr class="col-gap">
            <td>Context Enabled</td>
            <td><?=\CHtml::activeCheckBox(
                $firm,
                'runtime_selectable',
                array('checked' => $firm->runtime_selectable)
            ) ?></td>
            <td> <?= $form->textField($firm, 'context_email', array('placeholder' => 'Enter email address', 'class' => 'cols-full')) ?> </td>
        </tr>
        <tr class="col-gap">
            <td>Active</td>
            <td><?=\CHtml::activeCheckBox($firm, 'active') ?></td>
        </tr>
        </tbody>

        <tfoot>
        <tr>
            <td colspan="5">
                <?=\CHtml::submitButton(
                    'Save',
                    [
                        'class' => 'button large',
                        'name' => 'save',
                        'id' => 'et_save'
                    ]
                ) ?>
                <?=\CHtml::submitButton(
                    'Cancel',
                    [
                        'class' => 'button large',
                        'data-uri' => '/Admin/context/index',
                        'name' => 'cancel',
                        'id' => 'et_cancel'
                    ]
                ) ?>
            </td>
        </tr>
        </tfoot>
    </table>


    <?php $this->endWidget() ?>

    <?php if (isset($siteSecretaries)) : ?>
        <?=$this->renderPartial(
            'application.modules.OphCoCorrespondence.views.admin.secretary.edit',
            [
                'errors' => array(),
                'siteSecretaries' => $siteSecretaries,
                'newSiteSecretary' => $newSiteSecretary
            ]
        ) ?>
    <?php endif; ?>
</div>

<script>
    $(document).ready(function () {

        // This is to make the email textbox visible on adding a new context service as the checkbox is checked.
        toggleEmailTextBox('input[id=Firm_can_own_an_episode]', '#div_Firm_service_email');
        toggleEmailTextBox('input[id=Firm_runtime_selectable]', '#div_Firm_context_email');

        $('input[id=Firm_can_own_an_episode]').change(function(){
            toggleEmailTextBox(this, '#div_Firm_service_email');
        });

        $('input[id=Firm_runtime_selectable]').change(function(){
            toggleEmailTextBox(this, '#div_Firm_context_email');
        });

        // This function controls the email textbox based on whether the service enabled is checked or not.
        function toggleEmailTextBox($element, $emailContainer) {
            if($($element).is(':checked')) {
                $($emailContainer).show();
            } else {
                $($emailContainer).hide();
            }
        }
    })
</script>
