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

use OEModule\OphCoMessaging\models\Mailbox;

$user = Yii::app()->session['user'];
$asset_path = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.' . $module_class . '.assets'), true) . '/';

$coreapi = new CoreAPI();
?>
<div class="home-messages subgrid">
  <div class="message-actions">
      <nav class="sidebar-messages">
          <a class="button all-unread-messages js-all-unread-messages selected">All unread <span class="unread"><?= $count_unread_total ?></span></a>
          <hr class="divider"></hr>
          <?php foreach ($mailboxes_with_counts as $mailbox_with_counts) {
                $this->renderPartial('OphCoMessaging.views.dashboard.mailbox', ['mailbox_with_counts' => $mailbox_with_counts]);
          } ?>
          <hr class="divider"></hr>
          <h3>Search all messages</h3>
          <table class="standard normal-text last-right">
            <tbody>
              <tr>
                <td>Mailbox</td>
                <td>
                  <?= CHtml::dropDownList(
                      'OphCoMessaging_Search_Mailbox',
                      \Yii::app()->request->getQuery('OphCoMessaging_Search_Mailbox', '') ? \Yii::app()->request->getQuery('OphCoMessaging_Search_Mailbox', '') : '',
                      CHtml::listData($mailboxes_with_counts, 'id', 'name'),
                      array('class' => 'cols-full', 'empty' => 'All mailboxes')
                  ); ?>
                </td>
              </tr>
              <tr>
                <td>Sender</td>
                <td>
                  <?= CHtml::dropDownList(
                      'OphCoMessaging_Search_Sender',
                      \Yii::app()->request->getQuery('OphCoMessaging_Search_Sender', '') ? \Yii::app()->request->getQuery('OphCoMessaging_Search_Sender', '') : '',
                      !(strpos($message_type, 'sent') !== false) ? \OEModule\OphCoMessaging\models\Element_OphCoMessaging_Message::model()->getSenders() : array(),
                      array('class' => 'cols-full', 'empty' => 'All senders')
                  ); ?>
                </td>
              </tr>
              <tr>
                <td>Type</td>
                <td>
                  <?= CHtml::dropDownList(
                      'OphCoMessaging_Search_MessageType',
                      \Yii::app()->request->getQuery('OphCoMessaging_Search_MessageType', '') ? \Yii::app()->request->getQuery('OphCoMessaging_Search_MessageType', '') : '',
                      CHtml::listData(
                          \OEModule\OphCoMessaging\models\OphCoMessaging_Message_MessageType::model()->findAll(array('order' => 'display_order asc')),
                          'id',
                          'name'
                      ),
                      array('class' => 'cols-full', 'empty' => 'All types')
                  ); ?>
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <?=\CHtml::textField(
                      'OphCoMessaging_Search',
                      \Yii::app()->request->getQuery('OphCoMessaging_Search', '') ? \Yii::app()->request->getQuery('OphCoMessaging_Search', '') : '',
                      array('class' => 'search cols-full', 'placeholder' => 'Message text keyword')
                  ); ?>
                </td>
              </tr>
            </tbody>
          </table>
          <h3>Date range <small>(start → end)</small></h3>
          <div class="set-date-range">
            <div class="date-inputs">
              <input type="text" id="OphCoMessaging_from" name="OphCoMessaging_from" placeholder="from" class="date datepicker-from" value="<?=\Yii::app()->request->getQuery('OphCoMessaging_from', '')?>" />
              <input type="text" id="OphCoMessaging_to" name="OphCoMessaging_to" placeholder="to" class="date datepicker-to" value="<?=\Yii::app()->request->getQuery('OphCoMessaging_to', '')?>" />
            </div>
            <fieldset class="js-quick-date">
              <div class="selectors">
                <label>
                  <?= CHtml::radioButton('OphCoMessaging_All', (Yii::app()->request->getQuery('OphCoMessaging_to', '') || Yii::app()->request->getQuery('OphCoMessaging_from', '')) ? false : true) ?>
                  <span class="btn">No date range filter</span>
                </label>
              </div>
              <div class="selectors">
                <label>
                  <?= CHtml::radioButton('OphCoMessaging_All', false) ?>
                  <span class="btn js-range" data-range="last-week">Last week</span>
                </label>
                <label>
                  <?= CHtml::radioButton('OphCoMessaging_All', false) ?>
                  <span class="btn js-range" data-range="this-week">This week</span>
                </label>
              </div>
              <div class="selectors">
                <label>
                  <?= CHtml::radioButton('OphCoMessaging_All', false) ?>
                  <span class="btn js-range" data-range="last-month">Last month</span>
                </label>
                <label>
                  <?= CHtml::radioButton('OphCoMessaging_All', false) ?>
                  <span class="btn js-range" data-range="this-month">This month</span>
                </label>
              </div>
            </fieldset>
          </div>
          <div class="row">
              <button type="submit" class="cols-full green hint" id="OphCoMessaging_Submit">Search</button>
          </div>
      </nav>
  </div>
  <div class="messages-all">
      <?php $this->renderPartial('application.modules.OphCoMessaging.views.inbox.grid', array(
            'mailbox' => $selected_mailbox,
            'message_type' => $message_type,
            'messages' => $messages,
            'defer_to_comments' => !$is_a_sent_folder, // Show original message in sent folder views, but replies in received folder views
            'coreapi' => $coreapi
        )); ?>
  </div>
</div>

<script>
    /**
     * Update side folder with correct number of messages unread
     */
    function updateSideFolders(newMessageCounts) {
      $('.js-all-unread-messages .unread').text(newMessageCounts['count_unread_total']);

      for ([id, counts] of Object.entries(newMessageCounts['mailboxes_with_counts'])) {
        $(`.js-mailbox[data-mailbox-id="${id}"] .js-folder-counter`).each(function() {
            let folder = $(this).data('filter');
            // Get the text of the folder name before the message count
            let folderUnreadCount = counts['count_' + folder] || 0;

            if ($(this).find('.unread').length !== 0) {
              $(this).find('.unread').text(folderUnreadCount);
            } else if ($(this).find('.count').length !== 0) {
                $(this).find('.count').text(" (" + folderUnreadCount + ")");
            } else {
                if (folderUnreadCount > 0){
                    $(this).find('.unread').text(folderUnreadCount);
                } else {
                    $(this).find('.unread').hide();
                }
            }

        });
      }
    }

    /**
     * mark messages as read
     */
    $('.js-mark-as-read-btn').one('click', function() {
        let message_type = "<?= $message_type ?>";
        let $btn = $(this);
        let $closestTr = $btn.closest('tr');
        let eventId = $btn.closest('tr').attr('data-event-id');
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
                if (message_type.includes('unread') || message_type.includes('sent')) {
                    $closestTr.hide();
                } else {
                    $closestTr.removeClass('unread').addClass('read');
                }
                $btn.parent().remove();

                // update message count in folder section
                updateSideFolders(result);
            },
            error: function() {
                $btn.removeClass('spinner as-icon');
                $btn.addClass('triangle medium');
                $btn.data('tooltip-content', 'Could not mark as read. Try refreshing the page.');
            }
        });
    });

    $(document).ready(function() {
        const $selectedMailbox = $('.js-mailbox[data-mailbox-id="' + <?= $selected_mailbox->id ?? '"all"' ?> + '"]');
        const filter = '<?= $message_type ?>';

        if ($selectedMailbox.length > 0) {
            $selectedMailbox.find('.js-mailbox-hd').addClass('collapse');
            $selectedMailbox.find('.js-mailbox-hd').removeClass('expand');
            $selectedMailbox.find('.mailbox-filters').removeClass('hidden');
            $selectedMailbox.find('.mailbox-filters').show();

            if (filter) {
                $selectedMailbox.find('.mailbox-filters a[data-filter="' + filter + '"]').addClass('selected');
            }

            const allUnreadButton = $('a.js-all-unread-messages');

            allUnreadButton.removeClass('selected');

            allUnreadButton.on('click', function() {
              window.location.href = jQuery.query.remove('mailbox').set('messages', 'all').toString();
            });
        }
      $('.js-mailbox-hd').click(function() {
        const button = $(this);
        if (button.hasClass('expand')) {
          button.removeClass('expand');
          button.addClass('collapse');

          button.next().show();
        } else {
          button.removeClass('collapse');
          button.addClass('expand');

          button.next().hide();
        }
      });
    });
</script>
