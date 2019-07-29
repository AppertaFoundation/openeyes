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
    <h3>Queue Set:</h3>
    <div>
        <?php
        if (!$service = Yii::app()->service->getService('PatientTicketing_QueueSet')) {
            throw new Exception('Service not found: PatientTicketing_QueueSet');
        }
        $queueset_resource = $service->modelToResource($queueset);
        ?>
        <?php echo $form->dropdownList($queueset, 'category_id', \CHtml::listData(OEModule\PatientTicketing\models\QueueSetCategory::model()->activeOrPk($queueset->category_id)->findAll(), 'id', 'name')); ?>
        <?php echo $form->textField($queueset, 'name'); ?>
        <?php echo $form->textArea($queueset, 'description'); ?>
        <?php echo $form->radioBoolean($queueset, 'allow_null_priority'); ?>
        <?php echo $form->radioBoolean($queueset, 'summary_link'); ?>
    <?php echo $form->dropDownList($queueset, 'default_queue_id', \CHtml::listData($service->getQueueSetQueues($queueset_resource), 'id', 'name'), array('empty' => '- None -'))?>
    </div>

    <div>
        <h3>Search Filters:</h3>
        <?php echo $form->radioBoolean($queueset, 'filter_priority'); ?>
        <?php echo $form->radioBoolean($queueset, 'filter_subspecialty'); ?>
        <?php echo $form->radioBoolean($queueset, 'filter_firm'); ?>
        <?php echo $form->radioBoolean($queueset, 'filter_closed_tickets'); ?>
    </div>

    <?php if ($queue) {?>
        <h3>Initial Queue:</h3>
        <div>
            <?php echo $form->textField($queue, 'name'); ?>
            <?php echo $form->textArea($queue, 'description'); ?>
            <?php echo $form->textArea($queue, 'report_definition'); ?>
            <?php echo $form->textArea($queue, 'assignment_fields'); ?>
        </div>
    <?php }?>

<?php
$this->endWidget();
