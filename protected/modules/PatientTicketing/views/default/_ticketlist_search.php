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

/**
 * @var int $cat_id
 * @var \OEModule\PatientTicketing\services\PatientTicketing_QueueSet $queueset
 * @var string[] $patient_list
 */
?>

<?php $this->beginWidget('CActiveForm', array(
    'id' => 'ticket-filter',
    'action' => [
        '/PatientTicketing/default',
        'cat_id' => $cat_id,
        'queueset_id' => $queueset->getId(),
    ],
    'htmlOptions' => array(
        'class' => 'data-group',
    ),
    'enableAjaxValidation' => false,
)); ?>


<nav class="oe-virtual-clinic-search">

  <table class="standard">
    <colgroup>
      <col class="cols-1">
      <col class="cols-4">
      <col class="cols-1">
      <col class="cols-3">
      <col class="cols-3">
    </colgroup>

    <tbody>
    <tr class="col-gap">
        <?php
        $data = \CHtml::listData($qs_svc->getQueueSetQueues($queueset, false), 'id', 'name');
        $this->widget('application.widgets.MultiSelectDropDownList', [
            'options' => [
                'label' => 'Lists:',
                'dropDown' => [
                    'name' => null,
                    'id' => 'virtual-clinic-search-list',
                    'data' => $data,
                    'htmlOptions' => ['empty' => 'All Lists',],
                    'selectedItemsInputName' => 'queue-ids[]',
                    'selectedItems' => \Yii::app()->request->getpost('queue-ids', null),
                ],
            ],
        ]);
        ?>

        <?php

        if ($queueset->filter_subspecialty) : ?>
          <td class="fade">Subspecialty:</td>
          <td>
              <?=\CHtml::dropDownList(
                  'subspecialty-id',
                  @$_POST['subspecialty-id'],
                  Subspecialty::model()->getList(),
                  [
                      'empty' => 'All specialties',
                      'class' => 'cols-11',
                      'disabled' => (@$_POST['emergency_list'] == 1 ? 'disabled' : ''),
                  ]
              );
              ?>
          </td>
        <?php endif; ?>

      <td colspan="2">
          <?php $priorities = \Yii::app()->request->getPost('priority-ids', []); ?>
        <label class="inline highlight">
            <?= \CHtml::checkBox('priority-ids[]', in_array(1, $priorities), ['value' => 1]); ?>
          <i class="oe-i circle-red small pad"></i>
        </label>
        <label class="inline highlight">
            <?= \CHtml::checkBox('priority-ids[]', in_array(2, $priorities), ['value' => 2]); ?>
          <i class="oe-i circle-amber small pad"></i>
        </label>
        <label class="inline highlight">
            <?= \CHtml::checkBox('priority-ids[]', in_array(3, $priorities), ['value' => 3]); ?>
          <i class="oe-i circle-green small pad"></i>
        </label>
        <small>
          <label class="inline highlight">
              <?= \CHtml::checkBox(
                  'closed-tickets',
                  \Yii::app()->request->getPost('closed-tickets', false),
                  ['value' => 1]
              ); ?>
            Completed
          </label></small>
      </td>
    </tr>
    <tr class="col-gap">

        <?php
        $this->widget('application.widgets.MultiSelectDropDownList', [
            'options' => [
                'label' => 'Patients:',
                'dropDown' => [
                    'name' => null,
                    'id' => 'virtual-clinic-search-patient',
                    'data' => $patient_list,
                    'htmlOptions' => ['empty' => 'All Patients',],
                    'selectedItemsInputName' => 'patient-ids[]',
                    'selectedItems' => \Yii::app()->request->getpost('patient-ids', null),
                ],
            ],
        ]);
        ?>
      <td class="fade">Context</td>
      <td>
          <?php if (!@$_POST['subspecialty-id']) { ?>
              <?=\CHtml::dropDownList('firm-id', '', array(), array(
                  'class' => 'cols-11',
                  'empty' => 'All ' . Firm::contextLabel() . 's',
                  'disabled' => 'disabled',
              )) ?>
          <?php } else { ?>
              <?=\CHtml::dropDownList('firm-id', @$_POST['firm-id'],
                  Firm::model()->getList(@$_POST['subspecialty-id']), array(
                      'class' => 'cols-11',
                      'empty' => 'All ' . Firm::contextLabel() . 's',
                      'disabled' => (@$_POST['emergency_list'] == 1 ? 'disabled' : ''),
                  )) ?>
          <?php } ?>
      </td>
      <td colspan="2">
        <button class="green hint cols-11">Update Search</button>
      </td>
    </tr>

    </tbody>
  </table>

</nav>

<?php $this->endWidget() ?>
