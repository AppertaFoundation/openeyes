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
$user = Yii::app()->session['user'];
$asset_path = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.' . $module_class . '.assets')) . '/';
?>
<script type="text/javascript">
    $(document).ready(function() {
        $('#grid_header_form .datepicker').datepicker({'showAnim':'fold','dateFormat':'d M yy'});

        $('#display-inbox').click(function(e) {
            e.preventDefault();
            $('#inbox').removeClass('hidden');
            $('#display-inbox').addClass('selected');
            $('#sent').addClass('hidden');
            $('#display-sent').removeClass('selected');
            $('#urgent').addClass('hidden');
            $('#display-urgent').removeClass('selected');
        });

        $('#display-urgent').click(function(e) {
            e.preventDefault();
            $('#urgent').removeClass('hidden');
            $('#display-urgent').addClass('selected');
            $('#sent').addClass('hidden');
            $('#display-sent').removeClass('selected');
            $('#inbox').addClass('hidden');
            $('#display-inbox').removeClass('selected');
        });

        $('#display-sent').click(function(e) {
            e.preventDefault();
            $('#inbox').addClass('hidden');
            $('#display-inbox').removeClass('selected');
            $('#sent').removeClass('hidden');
            $('#display-sent').addClass('selected');
            $('#urgent').addClass('hidden');
            $('#display-urgent').removeClass('selected');
        });
    });
</script>
<div class="home-messages flex-layout flex-top">
  <div class="message-actions">
    <div class="user"><?= ($user->title ? $user->title . ' ' : '') . $user->first_name . ' ' . $user->last_name; ?></div>
    <button class="green hint cols-full send-message">Send New Message</button>
    <ul class="filter-messages">
      <li>
        <?php if ($inbox_unread > 0) {
          echo CHtml::link("Inbox ($inbox_unread)", '#', array('id' => 'display-inbox', 'class' => 'selected'));
        } else {
            echo CHtml::link('Inbox', '#', array('id' => 'display-inbox', 'class' => 'selected'));
        } ?>
      </li>
      <li>
          <?php if ($urgent_unread > 0) {
              echo CHtml::link("Urgent ($urgent_unread)", '#', array('id' => 'display-urgent'));
          } else {
              echo CHtml::link('Urgent', '#', array('id' => 'display-urgent'));
          } ?>
      </li>
      <li>
          <?php if ($sent_unread > 0) {
          echo CHtml::link("Sent ($sent_unread)", '#', array('id' => 'display-sent'));
        } else {
            echo CHtml::link('Sent', '#', array('id' => 'display-sent'));
        } ?>
      </li>
    </ul>
    <div class="search-messages">
      <form>
        <h3>Filter by Date</h3>
        <div class="flex-layout">
          <input type="text" id="OphCoMessaging_from" name="OphCoMessaging_from" placeholder="from" class="cols-5" value="<?=\Yii::app()->request->getQuery('OphCoMessaging_from', '')?>" />
          <input type="text" id="OphCoMessaging_to" name="OphCoMessaging_to" placeholder="to" class="cols-5" value="<?=\Yii::app()->request->getQuery('OphCoMessaging_to', '')?>" />
        </div>
      </form>
    </div>
  </div>
  <?php
  $this->renderPartial('OphCoMessaging.views.dashboard.message_list', array(
      'dp' => $inbox,
      'type' => 'inbox',
      'display' => true
  ));
  $this->renderPartial('OphCoMessaging.views.dashboard.message_list', array(
      'dp' => $urgent,
      'type' => 'urgent',
      'display' => false
  ));
  $this->renderPartial('OphCoMessaging.views.dashboard.message_list', array(
      'dp' => $sent,
      'type' => 'sent',
      'display' => false
  ));
  ?>
</div>
