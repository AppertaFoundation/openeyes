<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php $this->renderPartial('//elements/form_errors', array('errors' => $errors, 'bottom' => false)); ?>
<form>
	<input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>" />
	<?php if ($parent) {?>
		<input type="hidden" name="parent_id" value="<?=$parent->id?>" />
	<?php }?>

    <div>

    <table class="standard">
        <colgroup></colgroup>
        <tbody>
        <tr>
            <th>Name:</th>
            <td><?=\CHtml::textField('name', $queue->name, ['class' => 'cols-full']); ?></td>
        </tr>
        <tr>
            <th>Description:</th>
            <td><?=\CHtml::textArea('description', $queue->description, ['class' => 'cols-full']); ?></td>
        </tr>
        <tr>
            <th>Action Label:</th>
            <td><?=\CHtml::textField('action_label', $queue->action_label, ['class' => 'cols-full']); ?></td>
        </tr>
        <tr>
            <th>Report Definition:</th>
            <td><?=\CHtml::textArea('report_definition', $queue->report_definition, ['class' => 'cols-full']); ?></td>
        </tr>
        <tr>
            <th>Assignment Fields:</th>
            <td><?=\CHtml::textArea('assignment_fields', $queue->assignment_fields, ['class' => 'cols-full', 'rows' => 5]); ?></td>
        </tr>
        <tr>
            <th>Event types:</th>
            <th>
                <?php
                $this->widget('application.widgets.MultiSelectList', array(
                    'element' => $queue,
                    'field' => 'event_types',
                    'relation' => 'event_type_assignments',
                    'relation_id_field' => 'event_type_id',
                    'options' => EventType::model()->getActiveList(),
                    'default_options' => array(),
                    'htmlOptions' => array(
                        'label' => null,
                        'empty' => '- Select -',
                        'nowrapper' => true,
                        'class' => 'cols-full'
                    ),
                    'hidden' => false,
                    'inline' => false,
                    'noSelectionsMessage' => 'None',
                    'showRemoveAllLink' => false,
                    'layoutColumns' => array(
                        'label' => 3,
                        'field' => 8,
                    ),
                    'sortable' => true,
                ))?>
            </th>
        </tr>

        </tbody>
    </table>

	</div>
</form>
