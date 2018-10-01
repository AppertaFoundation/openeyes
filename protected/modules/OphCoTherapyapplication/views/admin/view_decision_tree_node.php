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
<div class="OphCoTherapyapplication_DecisionTreeNode">
    <?php if ($model->parent) {
        ?>
        <div>
            <a href="<?php echo Yii::app()->createUrl('OphCoTherapyapplication/admin/viewdecisiontree/') . '/' . $model->decisiontree_id . '?node_id=' . $model->parent_id ?>"
               class="view_parent button large">Back to Parent</a></div>
        <?php
    }?>

    <div class="row divider">
        <div class="cols-5">

            <table class="standard">
                <colgroup>
                    <col class="cols-1">
                    <col class="cols-2">
                </colgroup>
                <tbody>
                <?php if ($model->question) : ?>
                    <tr>
                        <td>Question:</td>
                        <td><?php echo $model->question; ?></td>
                    </tr>
                    <tr>
                        <td>Response Type:</td>
                        <td><?php echo $model->response_type->label ?></td>
                    </tr>
                <?php elseif ($model->outcome) : ?>
                    <tr>
                        <td>Outcome:</td>
                        <td><?php echo $model->outcome->name ?></td>
                    </tr>
                <?php endif; ?>
                </tbody>
                <tfoot class="pagination-container">
                <tr>
                    <td colspan="2">
                        <a class="edit_node button large" data-node_id="<?php echo $model->id ?>">Edit</a>

                        <?php if ($model->canAddRule()) : ?>
                            <a class="button large add_rule" data-node_id="<?php echo $model->id ?>">Add rule</a>
                        <?php endif; ?>

                        <?php if ($model->canAddChild()) { ?>
                            <a class="add_node button large" data-dt_id="<?php echo $model->decisiontree_id ?>"
                               data-parent_id="<?php echo $model->id ?>">Add child</a>
                        <?php } ?>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="row divider">
        <div class="cols-5">
            <h4>Rules</h4>
            <table class="standard OphCoTherapyapplication_DecisionTreeNode">
                <colgroup>
                    <col class="cols-1">
                    <col class="cols-2">
                </colgroup>
                <tbody>
                <?php foreach ($model->rules as $rule) {
                    $this->renderPartial('view_OphCoTherapyapplication_DecisionTreeNodeRule', array(
                        'model' => $rule,
                    ));
                } ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($model->children) : ?>
        <div class="row divider">
            <h4>Children</h4>
            <div class="cols-5 offset-1">
                <table class="standard">
                    <colgroup>
                        <col class="cols-1">
                        <col class="cols-1">
                        <col class="cols-2">
                    </colgroup>
                    <tbody>
                    <?php foreach ($model->children as $child) : ?>
                        <tr>
                            <td></td>
                            <td>
                                <?php
                                if ($child->rules) {
                                    foreach ($child->rules as $rule) {
                                        echo ' [' . $rule->displayParentCheck() . ' ' . $rule->displayParentCheckValue() . ']';
                                    }
                                } else {
                                    echo '[DEFAULT]';
                                }
                                ?>:
                            </td>
                            <td>
                                <a href="<?php echo Yii::app()->createUrl('OphCoTherapyapplication/admin/viewdecisiontree/') . '/' . $model->decisiontree_id . '?node_id=' . $child->id ?>">
                                    <?php echo $child->question ? $child->question : $child->outcome->name ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

</div>
