<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
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
$user = Yii::app()->session['user'];
$asset_path = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.' . $module_class . '.assets')) . '/';
$message_type = array_key_exists('messages', $_GET) && $_GET['messages'] ? $_GET['messages'] : $default_folder;
?>
<div class="home-messages subgrid">
  <div class="message-actions">
    <div class="user"><?= ($user->title ? $user->title . ' ' : '') . $user->first_name . ' ' . $user->last_name; ?></div>
    <ul class="filter-messages">
        <li>
            <?=\CHtml::link(
                $number_inbox_unread > 0 ? "Unread ($number_inbox_unread)" : 'Unread',
                '#',
                array('id' => 'display-unread', 'data-filter' => 'unread', 'class' => ($message_type === 'unread' ? 'selected ' : '') . 'js-display-counter')); ?>
        </li>
      <li>
        <?=\CHtml::link(
            $number_inbox_unread > 0 ? "All Messages ($number_inbox_unread)" : 'All Messages',
            '#', array('id' => 'display-inbox', 'data-filter' => 'inbox', 'class' => ($message_type === 'inbox' ? 'selected ' : '') . 'js-display-counter')); ?>

      </li>
      <li>
        <?=\CHtml::link(
            $number_urgent_unread > 0 ? "Urgent ($number_urgent_unread)" : 'Urgent',
            '#',
            array('id' => 'display-urgent', 'data-filter' => 'urgent', 'class' => ($message_type === 'urgent' ? 'selected ' : '') . 'js-display-counter')); ?>
      </li>
        <li>
            <?=\CHtml::link(
                $number_query_unread > 0 ? "Query ($number_query_unread)" : 'Query',
                '#',
                array('id' => 'display-query', 'data-filter' => 'query', 'class' => ($message_type === 'query' ? 'selected ' : '') . 'js-display-counter')); ?>
        </li>
      <li>
        <?=\CHtml::link(
            $number_sent_unread > 0 ? "Sent ($number_sent_unread)" : 'Sent',
            '#', array('id' => 'display-sent', 'data-filter' => 'sent', 'class' => ($message_type === 'sent' ? 'selected ' : '') . 'js-display-counter')); ?>
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
	$messages = ${$message_type}; // $message_type holds a string that matches the variable name to be passed to $messages

  echo $this->renderPartial('OphCoMessaging.views.inbox.grid', array(
    'module_class' => 'OphCoMessaging',
    'messages' => $messages->getData(),
    'dp' => $messages,
    'read_check' => true,
    'message_type' => $message_type,
		),
		true);
  ?>
</div>

<script>
    /**
     * Update side folder with correct number of messages unread
     */
    function updateSideFolders(newMessageCounts) {
        $('.js-display-counter').each(function() {
            let folder = $(this).data('filter');
            // capitalize first letter to set the folder name
            let folderName = folder.charAt(0).toUpperCase() + folder.slice(1);

            if (folder === 'unread') {
                folder = 'inbox';
            } else if (folder === 'inbox') {
                folderName = "All Messages";
            }

            let folderUnreadCount = newMessageCounts['number_' + folder + '_unread'];
            $(this).text(folderUnreadCount > 0 ? folderName + " (" + folderUnreadCount + ")" : folderName);
        });
    }

    /**
     * mark messages as read
     */
    $('.js-mark-as-read-btn').one('click', function() {
        let message_type = "<?= $message_type ?>";
        let $btn = $(this);
        let $closestTr = $btn.closest('tr');
        let eventId = $btn.closest('tr').find('.nowrap a').attr('href').split('/').slice(-1)[0];
        let url = "<?=Yii::app()->createURL("/OphCoMessaging/Default/markRead/")?>" + '/' + eventId;

        // change tick icon with a spinner
        $btn.addClass('spinner as-icon');
        $btn.removeClass('tick');

        // remove tooltip
        $btn.mouseout();
        $btn.removeClass('js-has-tooltip');

        $.ajax({
            url: url,
            data: {noRedirect: 1},
            success: function(result) {
                if (message_type === 'unread') {
                    $closestTr.hide();
                } else {
                    $closestTr.removeClass('unread').addClass('read');
                }
                $btn.parent().remove();

                // update message count in folder section
                updateSideFolders(JSON.parse(result));
            },
            error: function() {
                $btn.removeClass('spinner as-icon');
                $btn.addClass('triangle medium');
                $btn.data('tooltip-content', 'Could not mark as read. Try refreshing the page.');
            }
        });
    });
</script>
