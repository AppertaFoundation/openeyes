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

<?php
$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => 'queueset-form',
        'enableAjaxValidation' => false,
        'layoutColumns' => array(
                'label' => 3,
                'field' => 8,
        ), ));

$this->renderPartial('//elements/form_errors', array('errors' => $errors, 'bottom' => false));

?>
    <h3>Queue Set</h3>
    <?php
    if (!$service = Yii::app()->service->getService('PatientTicketing_QueueSet')) {
        throw new Exception('Service not found: PatientTicketing_QueueSet');
    }
    $queueset_resource = $service->modelToResource($queueset);
    ?>
    <table>
        <colgroup>
            <col class="cols-5">
            <col>
        </colgroup>
        <tbody>
            <tr>
                <td><?= $queueset->getAttributeLabel('category_id') ?></td>
                <td>
                    <?=\CHtml::activeDropDownList(
                        $queueset,
                        'category_id',
                        \CHtml::listData(OEModule\PatientTicketing\models\QueueSetCategory::model()->findAllAtLevel(ReferenceData::LEVEL_INSTITUTION), 'id', 'name'),
                        ['class' => 'cols-11']); ?>
                </td>
            </tr>
            <tr>
                <td><?= $queueset->getAttributeLabel('name') ?></td>
                <td><?=\CHtml::activeTextField($queueset, 'name', ['class' => 'cols-full', 'data-test' => 'queueset-form-name',]); ?></td>
            </tr>
            <tr>
                <td><?= $queueset->getAttributeLabel('description') ?></td>
                <td><?= \CHtml::activeTextArea(
                        $queueset,
                        'description',
                        ['class' => 'cols-full', 'data-test' => 'queueset-form-description']); ?>
                </td>
            </tr>
            <?php foreach (['allow_null_priority', 'summary_link'] as $field) {
                $this->renderPartial('form_queueset_radio', ['queueset' => $queueset, 'field' => $field]);
            } ?>
            <tr>
                <td><?= $queueset->getAttributeLabel('default_queue_id') ?></td>
                <td><?=\CHtml::activeDropDownList(
                        $queueset,
                        'default_queue_id',
                        \CHtml::listData($service->getQueueSetQueues($queueset_resource), 'id', 'name'),
                        ['class' => 'cols-11', 'empty' => '- None -']) ?>
                </td>
            </tr>
        </tbody>
    </table>
<h4>Search Filters</h4>
<table>
    <colgroup>
        <col class="cols-5">
        <col>
    </colgroup>
    <tbody>
    <?php foreach (['filter_priority', 'filter_subspecialty', 'filter_firm', 'filter_closed_tickets'] as $field) {
        $this->renderPartial('form_queueset_radio', ['queueset' => $queueset, 'field' => $field]);
    } ?>
    </tbody>
</table>
    <?php if ($queue) {?>
        <h4>Initial Queue</h4>
    <table>
        <colgroup>
            <col class="cols-5">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <td><?= $queue->getAttributeLabel('name') ?></td>
            <td><?=\CHtml::activeTextField($queue, 'name', ['class' => 'cols-full', "data-test" => "initial-queue-name"]); ?></td>
        </tr>
        <?php foreach (['description', 'report_definition', 'assignment_fields'] as $field) { ?>
            <tr>
                <td><?= $queue->getAttributeLabel($field) ?></td>
                <td><?= \CHtml::activeTextArea(
                        $queue,
                        $field,
                        ['class' => 'cols-full', "data-test" => "initial-queue-$field"]); ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <?php }?>

<?php
$this->endWidget();
