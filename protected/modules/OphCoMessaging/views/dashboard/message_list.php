<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
$items_per_page = $dp->getPagination()->getPageSize();
$page_num = $dp->getPagination()->getCurrentPage();
$from = ($page_num * $items_per_page) + 1;
$to = min(($page_num + 1) * $items_per_page, $dp->totalItemCount);

?>

<table id="<?php echo $type; ?>" class="messages <?php echo !$display ? 'hidden' : ''; ?>">
  <colgroup>
    <col style="width: 80px;">
    <col style="width: 70px;">
    <col style="width: 50px;">
    <col>
    <col style="width: 20px;">
    <col style="width: 90px;">
  </colgroup>
  <thead>
  <tr>
    <th>No.</th>
    <th>Gender</th>
    <th>Age</th>
    <th>Patient</th>
    <th>Messages</th>
    <th></th>
    <th colspan="3">
        <?php echo $from . ' - ' . $to . ' of ' . $dp->totalItemCount; ?>
      <i class="oe-i arrow-left-bold medium pad"></i>
      <i class="oe-i arrow-right-bold medium pad"></i>
    </th>
  </tr>
  </thead>
  <tbody>
  <?php foreach ($dp->getData() as $message): ?>
    <tr class="<?php echo $message->marked_as_read ? 'read' : 'unread'; ?>">
      <td>
          <?php echo CHtml::link(CHtml::encode($message->event->episode->patient->hos_num), Yii::app()->createUrl('/OphCoMessaging/default/view', array('id' => $message->event->id))); ?>
      </td>
      <td>
          <?php echo CHtml::encode($message->event->episode->patient->gender); ?>
      </td>
      <td>
          <?php echo CHtml::encode(Helper::getAge($message->event->episode->patient->dob)); ?>
      </td>
      <td class="nowrap patient">
          <?php echo CHtml::link(CHtml::encode($message->event->episode->patient->getHSCICName()), Yii::app()->createUrl('/OphCoMessaging/default/view', array('id' => $message->event->id))); ?>
      </td>
      <td>
          <?php if ($message->urgent): ?>
            <svg class="urgent-message" viewBox="0 0 8 8" height="8" width="8">
              <circle cx="4" cy="4" r="4"></circle>
            </svg>
          <?php endif; ?>
      </td>
      <td>
          <?php echo CHtml::encode(Helper::convertMySQL2NHS($message->created_date)); ?>
      </td>
      <td class="nowrap sender">
          <?php echo CHtml::encode(\User::model()->findByPk($message->created_user_id)->getFullNameAndTitle()); ?>
      </td>
      <td>
        <div class="message">
          <!-- Can display all of the message here as the CSS/JS will condense it accordingly and allow inline expansion using the new expand control. -->
            <?php echo Yii::app()->format->Ntext($message->message_text); ?>
        </div>
      </td>
      <td>
        <i class="oe-i expand small js-expand-message"></i>
      </td>
      <td>
        <!--Display the new message event page.-->
        <a href="#">reply</a>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>