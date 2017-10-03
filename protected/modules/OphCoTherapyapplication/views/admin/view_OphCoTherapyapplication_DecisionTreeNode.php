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
	<div><a href="<?php echo Yii::app()->createUrl('OphCoTherapyapplication/admin/viewdecisiontree/').'/'.$model->decisiontree_id.'?node_id='.$model->parent_id ?>" class="view_parent">&lt;&lt; Parent</a></div>
<?php

}
if ($model->rules) {
    ?>

	<div class="rules curvybox blue column end">
		<h4>Rules</h4>
		<?php foreach ($model->rules as $rule) {
            $this->renderPartial('view_OphCoTherapyapplication_DecisionTreeNodeRule', array(
                    'model' => $rule,
            ));
        }?>
	</div>
<?php

}
?>
		<?php
            if ($model->canAddRule()) {?>
		<div>
			<a href="#" class="add_rule" data-node_id="<?php echo $model->id ?>">Add rule</a>
		</div>
		<?php } ?>


		<div class="node curvybox white">
<?php if ($model->question) { ?>
		<div class="question">
		<b>Question:</b><?php echo $model->question; ?>
		</div>
		<div class="response">
		<b>Response Type</b>
		<?php echo $model->response_type->label ?>
		</div>
	<?php } elseif ($model->outcome) { ?>
	<div class="outcome">
		<b>Outcome</b>
		<?php echo $model->outcome->name ?>
		</div>
	<?php } ?>
<a href="#" class="edit_node" data-node_id="<?php echo $model->id ?>">Edit</a>
</div>
<?php
if ($model->children) {
?>
<div class="children curvybox blue">
	<h4>Children</h4>
	<?php foreach ($model->children as $child) {
    ?>
		<div class="child curvybox">
			<?php
            if ($child->rules) {
                foreach ($child->rules as $rule) {
                    echo ' ['.$rule->displayParentCheck().' '.$rule->displayParentCheckValue().']';
                }
            } else {
                echo '[DEFAULT]';
            }
            ?>:
			<a href="<?php echo Yii::app()->createUrl('OphCoTherapyapplication/admin/viewdecisiontree/').'/'.$model->decisiontree_id.'?node_id='.$child->id ?>"><?php echo $child->question ? $child->question : $child->outcome->name ?></a>
		</div>
	<?php
    }?>
</div>
<?php
}
?>
<div>
<?php if ($model->canAddChild()) {?>
<a href="#" class="add_node" data-dt_id="<?php echo $model->decisiontree_id ?>" data-parent_id="<?php echo $model->id?>">Add child</a>
<?php } ?>
</div>

</div>
