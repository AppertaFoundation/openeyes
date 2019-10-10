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

<?php $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => 'adminform',
        'enableAjaxValidation' => false,
        'focus' => '#contactname',
        'layoutColumns' => array(
            'label' => 2,
            'field' => 5,
        ),
    ))?>

<div class="cols-7">
    <div class="row divider">
        <h2><?php echo $rule->id ? 'Edit' : 'Add'?> letter contact rule</h2>
    </div>

    <?php echo $form->errorSummary($rule); ?>
    <table class="standard">
        <colgroup>
            <col class="cols-2">
            <col class="cols-4">
        </colgroup>
        <tbody>
        <tr>
            <td><?=$rule->getAttributeLabel('parent_rule_id');?></td>
            <td><?=\CHtml::activeDropDownList(
                $rule,
                'rule_order',
                CHtml::listData(OphTrOperationbooking_Letter_Contact_Rule::model()->getListAsTree(), 'id', 'treeName'),
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
            'theatre_id' => CHtml::listData(OphTrOperationbooking_Operation_Theatre::model()->findAll(), 'id', 'name')
        ]; ?>
        <?php foreach ($dropdowns as $attr => $data) : ?>
            <tr>
                <td><?=$rule->getAttributeLabel($attr);?></td>
                <td><?=\CHtml::activeDropDownList(
                    $rule,
                    $attr,
                    $data,
                    ['empty' => '- Not set -', 'class' => 'cols-full']
                    ); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php $dropdowns = ['refuse_telephone', 'refuse_title', 'health_telephone']; ?>
        <?php foreach ($dropdowns as $attr => $data) : ?>
            <tr>
                <td><?=$rule->getAttributeLabel($data);?></td>
                <td><?=\CHtml::activeTextField($rule, $data, ['class' => 'cols-full']) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if ($rule->children) { ?>
        <tr>
            <td>Descendants</td>
            <td>
                <?php $this->widget('CTreeView', array(
                    'data' => OphTrOperationbooking_Letter_Contact_Rule::model()->findAllAsTree($rule, true, 'textPlain'),
                )); ?>
            </td>
        </tr>
        <?php } ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="2">
                <?php echo $form->errorSummary($rule); ?>
                <?php echo $form->formActions(array(
                    'delete' => $rule->id ? 'Delete' : false,
                )); ?>
            </td>
        </tr>
        </tfoot>
    </table>

    <?php $this->endWidget() ?>
</div>

<script type="text/javascript">
    handleButton($('#et_cancel'),function() {
        window.location.href = baseUrl+'/OphTrOperationbooking/admin/view'+OE_rule_model+'s';
    });
    handleButton($('#et_save'),function() {
        $('#adminform').submit();
    });
    handleButton($('#et_delete'),function(e) {
        e.preventDefault();
        window.location.href = baseUrl+'/OphTrOperationbooking/admin/delete'+OE_rule_model+'/<?php echo $rule->id?>';
    });
</script>
