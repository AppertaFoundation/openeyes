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
<div class="home-messages subgrid">
  <div class="message-actions">
    <div class="user"><?= ($user->title ? $user->title . ' ' : '') . $user->first_name . ' ' . $user->last_name; ?></div>
    <ul class="filter-messages">
        <li>
            <?=\CHtml::link(
                $number_inbox_unread > 0 ? "Unread ($number_inbox_unread)" : 'Unread',
                '#',
                array('id' => 'display-unread', 'data-filter' => 'unread', 'class' => !array_key_exists('messages', $_GET) || @$_GET['messages'] === 'unread' ? 'selected' : '')); ?>
        </li>
      <li>
        <?=\CHtml::link(
            $number_inbox_unread > 0 ? "All Messages ($number_inbox_unread)" : 'All Messages',
            '#', array('id' => 'display-inbox', 'data-filter' => 'inbox', 'class' => isset($_GET['messages']) && $_GET['messages'] === 'inbox' ? 'selected' : '')); ?>

      </li>
      <li>
        <?=\CHtml::link(
            $number_urgent_unread > 0 ? "Urgent ($number_urgent_unread)" : 'Urgent',
            '#',
            array('id' => 'display-urgent', 'data-filter' => 'urgent', 'class' => @$_GET['messages'] === 'urgent' ? 'selected' : '')); ?>
      </li>
        <li>
            <?=\CHtml::link(
                $number_query_unread > 0 ? "Query ($number_query_unread)" : 'Query',
                '#',
                array('id' => 'display-query', 'data-filter' => 'query', 'class' => @$_GET['messages'] === 'query' ? 'selected' : '')); ?>
        </li>
      <li>
        <?=\CHtml::link(
            $number_sent_unread > 0 ? "Sent ($number_sent_unread)" : 'Sent',
            '#', array('id' => 'display-sent', 'data-filter' => 'sent', 'class' => @$_GET['messages'] === 'sent' ? 'selected' : '')); ?>
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
  switch (@$_GET['messages']) {
      case 'urgent':
          $messages = $urgent;
          break;
			case 'query':
					$messages = $query;
					break;
			case 'unread':
					$messages = $unread;
					break;
      case 'sent':
          $messages = $sent;
          break;
      case 'inbox':
      default:
          $messages = $inbox;
          break;
  }
	if(!array_key_exists('messages', $_GET)){$messages = $unread;}

  echo $this->renderPartial('OphCoMessaging.views.inbox.grid', array(
    'module_class' => 'OphCoMessaging',
    'messages' => $messages->getData(),
    'dp' => $messages,
    'read_check' => true,
    'message_type' => @$_GET['messages'] ?: 'index',
), true);
  ?>
</div>
<script type="text/javascript">
    $(document).ready(function() {

        $('.filter-messages').on('click', 'a', function (e) {
            e.preventDefault();
            window.location.href = jQuery.query.set('messages', $(this).data('filter'));
        });

        $('#OphCoMessaging_to').add('#OphCoMessaging_from').each(function () {
          pickmeup('#' + $(this).attr('id'), {
              format: 'd b Y',
            hide_on_select: true,
            default_date: false
          });
          }).on('pickmeup-change change', function () {
        window.location.href = jQuery.query
          .set('OphCoMessaging_from', $('#OphCoMessaging_from').val())
          .set('OphCoMessaging_to', $('#OphCoMessaging_to').val());
          });

        $('.js-expand-message').each(function(){
        	let $expandIcon = $(this);
        	let $message = $expandIcon.closest('tr').find('.message');
        	let expander = new Expander( $(this), $message );
					showHideExpandIcon($expandIcon, $message);
        });

			$(window).resize(function(){
				$('.js-expand-message').each(function(){
					showHideExpandIcon($(this));
				});
			});
		});

		function showHideExpandIcon($expandIcon, $message = null) {
			if ($message === null) {$message = $expandIcon.closest('tr').find('.message');}
			if ($expandIcon.hasClass('collapse') || isExpandableMessage($message)) {$expandIcon.show();}
			else {$expandIcon.hide();}
		}

		function isExpandableMessage($inboxMessage) {
			let isMultiLine = $inboxMessage[0].scrollHeight > $inboxMessage.innerHeight();
			let isOverflowing = $inboxMessage[0].scrollWidth > $inboxMessage.innerWidth();
			return isMultiLine || isOverflowing;
		}

    function Expander( $icon, $message){
        let expanded = false;
        $icon.click( change );

        function change(){
            $icon.toggleClass('expand collapse');
            if(expanded){
                $message.removeClass('expand');
            } else {
                $message.addClass('expand');
            }
            showHideExpandIcon($icon);
            expanded = !expanded;
        }
    }
</script>
