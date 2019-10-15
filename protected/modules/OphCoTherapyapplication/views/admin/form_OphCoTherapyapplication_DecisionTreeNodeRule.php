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

<p class="note">Fields with <span class="required">*</span> are required.</p>

<?php echo $form->errorSummary($model); ?>

<div class="data-group outcomeAdmin parent_check eventDetail">
    <div class="label"><?php echo $form->labelEx($model, 'parent_check'); ?></div>
    <div class="data"><?php echo $form->dropdownlist($model, 'parent_check', $model->COMPARATORS, array('empty' => 'Select', 'nowrapper' => true)); ?></div>
    <?php echo $form->error($model, 'parent_check'); ?>
</div>

<div class="data-group parent_check_value eventDetail">
    <div class="label"><?php echo $form->labelEx($model, 'parent_check_value');?></div>
    <div class="data">
    <?php
    $rtype = $model->node->parent->response_type;

    if ($rtype && $rtype->datatype == 'bool') {
        $this->renderPartial('template_OphCoTherapyapplication_DecisionTreeNode_default_value_bool',
                array('name' => get_class($model).'[parent_check_value]',
                        'id' => get_class($model).'_parent_check_value',
                        'val' => $model->parent_check_value,
                ));
    } elseif ($rtype && $rtype->datatype == 'va') {
        $this->renderPartial('template_OphCoTherapyapplication_DecisionTreeNode_default_value_va',
                array('name' => get_class($model).'[parent_check_value]',
                        'id' => get_class($model).'_parent_check_value',
                        'val' => $model->parent_check_value,
                ));
    } else {
        $this->renderPartial('template_OphCoTherapyapplication_DecisionTreeNode_default_value_default',
                array('name' => get_class($model).'[parent_check_value]',
                        'id' => get_class($model).'_parent_check_value',
                        'val' => $model->parent_check_value,
                ));
    }
    ?>
    </div>
    <?php echo $form->error($model, 'parent_check_value'); ?>
</div>
