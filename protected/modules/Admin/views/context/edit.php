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
                'field' => 5,
            ),
        ]
    ) ?>

    <table class="standard">
        <colgroup>
            <col class="cols-3">
            <col class="cols-5">
        </colgroup>
        <tbody>
        <tr>
            <td>PAS Code</td>
            <td> <?=\CHtml::activeTextField($firm, 'pas_code', ['class' => 'cols-full']); ?> </td>
        </tr>
        <tr>
            <td>Name</td>
            <td> <?=\CHtml::activeTextField($firm, 'name', ['class' => 'cols-full']); ?> </td>
        </tr>
        <tr>
            <td>Subspecialty</td>
            <td>
                <?=\CHtml::activeDropDownList($firm, 'subspecialty_id', $subspecialties_list_data, ['class' => 'cols-full', 'empty' => '- None -']); ?>
            </td>
        </tr>
        <tr>
            <td>Consultant</td>
            <td>
                <?=\CHtml::activeDropDownList($firm, 'consultant_id', $consultant_list_data, ['class' => 'cols-full', 'empty' => '- None -']); ?>
            </td>
        </tr>
        <tr>
            <td><?=$firm->getAttributeLabel('cost_code');?></td>
            <td> <?=\CHtml::activeTextField($firm, 'cost_code', ['class' => 'cols-full', ]);?></td>
        </tr>

        <tr class="col-gap">
            <td>Service Enabled</td>
            <td><?=\CHtml::activeCheckBox(
                $firm,
                'can_own_an_episode'
            ) ?></td>
        </tr>
        <tr class="col-gap">
            <td>Context Enabled:</td>
            <td><?=\CHtml::activeCheckBox(
                $firm,
                'runtime_selectable'
            ) ?></td>
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
                ); ?>
                <?=\CHtml::submitButton(
                    'Cancel',
                    [
                        'class' => 'button large',
                        'data-uri' => '/Admin/context/index',
                        'name' => 'cancel',
                        'id' => 'et_cancel'
                    ]
                ); ?>
            </td>
        </tr>
        </tfoot>
    </table>


    <?php $this->endWidget() ?>

    <?php if (isset($siteSecretaries) && $siteSecretaries) : ?>
        <?=$this->renderPartial(
            'application.modules.OphCoCorrespondence.views.admin.secretary.edit',
            [
                'errors' => array(),
                'siteSecretaries' => $siteSecretaries
            ]
        ) ?>
    <?php endif; ?>
</div>
