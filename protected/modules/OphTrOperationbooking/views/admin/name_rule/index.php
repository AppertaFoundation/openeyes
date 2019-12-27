<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<div class="cols-5">
    <form id="operation_name_rules">
        <table class="standard">
            <thead>
            <tr>
                <th><input type="checkbox" id="checkall" class="operation_name_rules" /></th>
                <th>Theatre</th>
                <th>Operation name</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $criteria = new CDbCriteria();
            $criteria->order = 'display_order asc';
            foreach (OphTrOperationbooking_Operation_Name_Rule::model()->findAll() as $i => $rule) {?>
                <tr class="clickable sortable" data-attr-id="<?php echo $rule->id?>?>" data-uri="OphTrOperationbooking/admin/editoperationnamerule/<?php echo $rule->id?>">
                    <td><input type="checkbox" name="operation_name[]" value="<?php echo $rule->id?>" class="operation_name_rules" /></td>
                    <td><?php echo $rule->theatre->name?></td>
                    <td><?php echo $rule->name?></td>
                </tr>
            <?php }?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3">
                        <?= CHtml::submitButton(
                            'Add',
                            [
                                'class' => 'button large',
                                'name' => 'add_operation_name_rule',
                                'id' => 'et_add_operation_name_rule',
                            ]
                        ); ?>
                        <?= CHtml::submitButton(
                            'Delete',
                            [
                                'class' => 'button large',
                                'name' => 'delete_operation_name_rule',
                                'id' => 'et_delete_operation_name_rule',
                            ]
                        ); ?>

<!--                        --><?php //echo EventAction::button('Add', 'add_operation_name_rule', null, array('class' => 'button small'))->toHtml()?>
<!--                        --><?php //echo EventAction::button('Delete', 'delete_operation_name_rule', null, array('class' => 'button small'))->toHtml()?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </form>
    <p>
        The operation name rules allow the system to use specific operation names in admission letters. Any operation that is booked for a specific theatre will use the name assigned in the theatre rule, rather than the generic term &quot;operation&quot;
    </p>
</div>



