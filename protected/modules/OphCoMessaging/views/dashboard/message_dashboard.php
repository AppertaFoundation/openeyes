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
$asset_path = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.' . $module_class . '.assets'), true) . '/';
$message_type = array_key_exists('messages', $_GET) && $_GET['messages'] ? $_GET['messages'] : $default_folder;
?>
<div class="home-messages subgrid">
  <div class="message-actions">
    <div class="user"><?= ($user->title ? $user->title . ' ' : '') . $user->first_name . ' ' . $user->last_name; ?></div>
      <nav class="sidebar-messages">
          <h3>Unread</h3>
          <ul class="filter-messages">
              <li>
                  <a id="display-unread-all" data-filter="unread_all" class="<?= ($message_type === 'unread_all' ? 'selected ' : '') . 'js-display-counter' ?>" href="#">
                      <div class="flex">
                          <div>All unread</div>
                          <?php if ($number_unread_all > 0) {
                                ?><span class="unread"><?= $number_unread_all ?></span><?php
                          } ?>
                      </div>
                  </a>
              </li>
              <li>
                  <a id="display-unread-received" data-filter="unread_received" class="<?= ($message_type === 'unread_received' ? 'selected ' : '') . 'js-display-counter' ?>" href="#">
                      <div class="flex">
                          <div>To me</div>
                          <?php if ($number_unread_received > 0) {
                                ?><span class="unread"><?= $number_unread_received ?></span><?php
                          } ?>
                      </div>
                  </a>
              </li>
              <li>
                <a id="display-unread-urgent" data-filter="unread_urgent" class="<?= ($message_type === 'unread_urgent' ? 'selected ' : '') . 'js-display-counter' ?>" href="#">
                    <div class="flex">
                        <div>Urgent</div>
                        <?php if ($number_unread_urgent > 0) {
                            ?><span class="unread"><?= $number_unread_urgent ?></span><?php
                        } ?>
                    </div>
                </a>
              </li>
              <li>
                <a id="display-unread-query" data-filter="unread_query" class="<?= ($message_type === 'unread_query' ? 'selected ' : '') . 'js-display-counter' ?>" href="#">
                    <div class="flex">
                        <div>Queries</div>
                        <?php if ($number_unread_query > 0) {
                            ?><span class="unread"><?= $number_unread_query ?></span><?php
                        } ?>
                    </div>
                </a>
              </li>
              <li>
                  <a id="display-unread-replies" data-filter="unread_replies" class="<?= ($message_type === 'unread_replies' ? 'selected ' : '') . 'js-display-counter' ?>" href="#">
                      <div class="flex">
                          <div>Replies</div>
                          <?php if ($number_unread_replies > 0) {
                                ?><span class="unread"><?= $number_unread_replies ?></span><?php
                          } ?>
                      </div>
                  </a>
              </li>
              <li>
                  <a id="display-unread-copied" data-filter="unread_copied" class="<?= ($message_type === 'unread_copied' ? 'selected ' : '') . 'js-display-counter' ?>" href="#">
                      <div class="flex">
                          <div><i class="oe-i duplicate small pad-right no-click"></i>CC on</div>
                          <?php if ($number_unread_copied > 0) {
                                ?><span class="unread"><?= $number_unread_copied ?></span><?php
                          } ?>
                      </div>
                  </a>
              </li>
          </ul>
          <h3>Read</h3>
          <ul class="filter-messages">
              <li>
                  <?= \CHtml::link(
                      "All read <span class='count'>($number_read_all)</span>",
                      '#',
                      array('id' => 'display-read-all', 'data-filter' => 'read_all', 'class' => ($message_type === 'read_all' ? 'selected ' : '') . 'js-display-counter')
                  ); ?>
              </li>
              <li>
                  <?= \CHtml::link(
                      "To me <span class='count'>($number_read_received)</span>",
                      '#',
                      array('id' => 'display-read-received', 'data-filter' => 'read_received', 'class' => ($message_type === 'read_received' ? 'selected ' : '') . 'js-display-counter')
                  ); ?>
              </li>
              <li>
                  <?= \CHtml::link(
                      "Urgent <span class='count'>($number_read_urgent)</span>",
                      '#',
                      array('id' => 'display-read-urgent', 'data-filter' => 'read_urgent', 'class' => ($message_type === 'read_urgent' ? 'selected ' : '') . 'js-display-counter')
                  ); ?>
              </li>
              <li>
                  <?= \CHtml::link(
                      "<i class=\"oe-i duplicate small pad-right no-click\"></i>CC on <span class='count'>($number_read_copied)</span>",
                      '#',
                      array('id' => 'display-read-copied', 'data-filter' => 'read_copied', 'class' => ($message_type === 'read_copied' ? 'selected ' : '') . 'js-display-counter')
                  ); ?>
              </li>
          </ul>
          <h3>Sent</h3>
          <ul class="filter-messages">
            <li>
                <?= \CHtml::link(
                    "All sent <span class='count'>($number_sent_all)</span>",
                    '#',
                    array('id' => 'display-sent-all', 'data-filter' => 'sent_all', 'class' => ($message_type === 'sent_all' ? 'selected ' : '') . 'js-display-counter')
                ); ?>
            </li>
            <li>
                <?=\CHtml::link(
                    "Awaiting reply to query <span class='count'>($number_sent_unreplied)</span>",
                    '#',
                    array('id' => 'display-sent-unreplied', 'data-filter' => 'sent_unreplied', 'class' => ($message_type === 'sent_unreplied' ? 'selected ' : '') . 'js-display-counter')
                ); ?>
            </li>
            <li>
                <?=\CHtml::link(
                    "Unread by recipient <span class='count'>($number_sent_unread)</span>",
                    '#',
                    array('id' => 'display-sent-unread', 'data-filter' => 'sent_unread', 'class' => ($message_type === 'sent_unread' ? 'selected ' : '') . 'js-display-counter')
                ); ?>
            </li>
          </ul>
    <div class="search-messages">
      <form>
          <h3>Sender</h3>
          <?= CHtml::dropDownList(
              'OphCoMessaging_Search_Sender',
              \Yii::app()->request->getQuery('OphCoMessaging_Search_Sender', '') ? \Yii::app()->request->getQuery('OphCoMessaging_Search_Sender', '') : '',
              !(strpos($message_type, 'sent') !== false) ? \OEModule\OphCoMessaging\models\Element_OphCoMessaging_Message::model()->getSenders() : array(),
              array('class' => 'cols-full', 'empty' => 'All senders')
          ); ?>
          <h3>Type</h3>
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
          <h3>Search Messages</h3>
          <?=\CHtml::textField(
              'OphCoMessaging_Search',
              \Yii::app()->request->getQuery('OphCoMessaging_Search', '') ? \Yii::app()->request->getQuery('OphCoMessaging_Search', '') : '',
              array('class' => 'search cols-full')
          ); ?>
        <h3>Filter by Date</h3>
        <div class="flex">
            <input type="text" id="OphCoMessaging_from" name="OphCoMessaging_from" placeholder="from" class="date datepicker-from" value="<?=\Yii::app()->request->getQuery('OphCoMessaging_from', '')?>" />
            <input type="text" id="OphCoMessaging_to" name="OphCoMessaging_to" placeholder="to" class="date datepicker-to" value="<?=\Yii::app()->request->getQuery('OphCoMessaging_to', '')?>" />
            <label class="inline highlight">
                <?= CHtml::checkBox('OpCoMessaging_All', (Yii::app()->request->getQuery('OphCoMessaging_to', '') || Yii::app()->request->getQuery('OphCoMessaging_from', '')) ? false : true).'All' ?>
            </label>
        </div>
        <div class="fast-date-range past">
            <div class="selectors">
                <div class="range" data-range="yesterday">Yesterday</div>
                <div class="range" data-range="today">Today</div>
            </div>
            <div class="selectors">
                <div class="range" data-range="last-week">Last week</div>
                <div class="range" data-range="this-week">This week</div>
            </div>
        </div>
        <div class="row">
            <button type="submit" class="cols-full green hint" id="OphCoMessaging_Submit">Search</button>
        </div>
      </form>
    </div>
      </nav>
  </div>
    <?php
    $messages = ${$message_type}; // $message_type holds a string that matches the variable name to be passed to $messages

    echo $this->renderPartial(
        'OphCoMessaging.views.inbox.grid',
        array(
        'module_class' => 'OphCoMessaging',
        'messages' => $messages->getData(),
        'dp' => $messages,
        'read_check' => true,
        'message_type' => $message_type,
        ),
        true
    );
    ?>
</div>

<script>
    /**
     * Update side folder with correct number of messages unread
     */
    function updateSideFolders(newMessageCounts) {
        $('.js-display-counter').each(function() {
            let folder = $(this).data('filter');
            // Get the text of the folder name before the message count
            let folderUnreadCount = newMessageCounts['number_' + folder];
            if ($(this).find('.count').length !== 0) {
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
</script>
