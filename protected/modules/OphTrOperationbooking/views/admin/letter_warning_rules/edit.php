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

<?php
$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => 'adminform',
        'enableAjaxValidation' => false,
        'focus' => '#contactname',
        'layoutColumns' => array(
            'label' => 2,
            'field' => 5,
        ),
    ))?>

<div class="cols-5">
    <div class="row divider">
        <h2><?php echo $rule->id ? 'Edit' : 'Add'?> letter warning rule</h2>
    </div>

    <?php echo $form->errorSummary($rule); ?>
    <table class="standard">
        <colgroup>
            <col class="cols-2">
            <col class="cols-4">
        </colgroup>
        <tbody>
        <tr>
            <td><?=$rule->getAttributeLabel('rule_type_id');?></td>
            <td><?=\CHtml::activeDropDownList(
                $rule,
                'rule_type_id',
                CHtml::listData(OphTrOperationbooking_Admission_Letter_Warning_Rule_Type::model()->findAll(), 'id', 'name'),
                ['empty' => '- Rule type -', 'class' => 'cols-full']
            ); ?>
            </td>
        </tr>
        <tr>
            <td><?=$rule->getAttributeLabel('parent_rule_id');?></td>
            <td><?=\CHtml::activeDropDownList(
                $rule,
                'parent_rule_id',
                CHtml::listData(OphTrOperationbooking_Admission_Letter_Warning_Rule::model()->getListAsTree(), 'id', 'treeName'),
                ['empty' => '- None -', 'class' => 'cols-full']
            ); ?>
            </td>
        </tr>
        <tr>
            <td><?=$rule->getAttributeLabel('rule_order');?></td>
            <td><?=\CHtml::activeTextField($rule, 'rule_order', ['class' => 'cols-full']) ?></td>
        </tr>
        <?php $dropdowns = [
            'site_id' => Site::model()->getListForCurrentInstitution('name'),
            'firm_id' => Firm::model()->getListWithSpecialties(),
            'subspecialty_id' => CHtml::listData(Subspecialty::model()->findAllByCurrentSpecialty(), 'id', 'name'),
            'theatre_id' => CHtml::listData(OphTrOperationbooking_Operation_Theatre::model()->findAll(), 'id', 'name'),
            'is_child' => array('1' => 'Child', '0' => 'Adult'),
        ]; ?>
        <?php foreach ($dropdowns as $attr => $data) : ?>
            <tr>
                <td><?=$rule->getAttributeLabel($attr);?></td>
                <td>
                    <?=\CHtml::activeDropDownList(
                        $rule,
                        $attr,
                        $data,
                        ['empty' => '- Not set -', 'class' => 'cols-full']
                    ); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td><?=$rule->getAttributeLabel('show_warning');?></td>
            <td>
                <?=\CHtml::activeRadioButtonList(
                    $rule,
                    'show_warning',
                    [1 => 'Yes', 0 => 'No'],
                    ['separator' => ' ', 'selected' => '1']
                ); ?>
            </td>
        </tr>
        <tr>
            <td><?=$rule->getAttributeLabel('warning_text');?></td>
            <td>
                <?=\CHtml::activeTextArea(
                    $rule,
                    'warning_text',
                    [
                        'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
                        'class' => 'cols-full',
                        'rows' => 5
                    ]
                ); ?>
            </td>
        </tr>
        <tr>
            <td><?=$rule->getAttributeLabel('emphasis');?></td>
            <td>
                <?=\CHtml::activeRadioButtonList(
                    $rule,
                    'emphasis',
                    [1 => 'Yes', 0 => 'No'],
                    ['separator' => ' ', 'selected' => '1']
                ); ?>
            </td>
        </tr>
        <tr>
            <td><?=$rule->getAttributeLabel('strong');?></td>
            <td>
                <?=\CHtml::activeRadioButtonList(
                    $rule,
                    'strong',
                    [1 => 'Yes', 0 => 'No'],
                    ['separator' => ' ', 'selected' => '1']
                ); ?>
            </td>
        </tr>
        </tbody>
    </table>

    <?php if ($rule->children) { ?>
        <div class="data-group">
            <div class="cols-<?php echo $form->layoutColumns['label']; ?> column">
                <div class="field-label">
                    Descendants:
                </div>
            </div>
            <div class="cols-<?php echo 12 - $form->layoutColumns['label']; ?> column">
                <div class="panel" style="margin:0">
                    <?php
                    $this->widget('CTreeView', array(
                        'data' => OphTrOperationbooking_Admission_Letter_Warning_Rule::model()->findAllAsTree($rule, true, 'textPlain'),
                    )) ?>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php echo $form->errorSummary($rule); ?>
    <?php echo $form->formActions(array(
        'delete' => $rule->id ? 'Delete' : false,
    )); ?>

</div>

<?php $this->endWidget() ?>

<script type="text/javascript">
    handleButton($('#et_cancel'), function () {
        window.location.href = baseUrl + '/OphTrOperationbooking/admin/view' + OE_rule_model + 's';
    });
    handleButton($('#et_save'), function () {
        $('#adminform').submit();
    });
    handleButton($('#et_delete'), function (e) {
        e.preventDefault();
        window.location.href = baseUrl + '/OphTrOperationbooking/admin/delete' + OE_rule_model + '/<?php echo $rule->id?>';
    });
</script>
