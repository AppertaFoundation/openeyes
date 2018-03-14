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
<div class="home-messages flex-layout flex-top">
  <div class="message-actions">
    <div class="user"><?= ($user->title ? $user->title . ' ' : '') . $user->first_name . ' ' . $user->last_name; ?></div>
    <button class="green hint cols-full send-message">Send New Message</button>
    <ul class="filter-messages">
      <li>
        <?php echo CHtml::link(
            $inbox_unread > 0 ? "Inbox ($inbox_unread)" : 'Inbox',
            '#', array('id' => 'display-inbox', 'class' => !array_key_exists('messages', $_GET) || @$_GET['messages'] === 'inbox' ? 'selected' : '')); ?>
      </li>
      <li>
        <?php echo CHtml::link(
            $urgent_unread > 0 ? "Urgent ($urgent_unread)" : 'Urgent',
            '#',
            array('id' => 'display-urgent', 'class' => @$_GET['messages'] === 'urgent' ? 'selected' : '')); ?>
      </li>
      <li>
        <?php echo CHtml::link(
            $sent_unread > 0 ? "Sent ($sent_unread)" : 'Sent',
            '#', array('id' => 'display-sent', 'class' => @$_GET['messages'] === 'sent' ? 'selected' : '')); ?>
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
      case 'sent':
          $messages = $sent;
          break;
      case 'inbox':
      default:
          $messages = $inbox;
          break;
  }

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
        $('#grid_header_form .datepicker').datepicker({'showAnim':'fold','dateFormat':'d M yy'});

        $('#display-inbox').click(function(e) {
            e.preventDefault();
            window.location.href = jQuery.query.set('messages', 'inbox')
        });

        $('#display-urgent').click(function(e) {
            e.preventDefault();
            window.location.href = jQuery.query.set('messages', 'urgent')
        });

        $('#display-sent').click(function(e) {
            e.preventDefault();
            window.location.href = jQuery.query.set('messages', 'sent')
        });

    $('#OphCoMessaging_to').add('#OphCoMessaging_from').each(function () {
      pickmeup('#' + $(this).attr('id'), {
        format: 'Y-m-d',
        hide_on_select: true,
        default_date: false
      });
    }).on('blur', function () {
      updateDateRange()
    }).on('keypress', function (e) {
      if (e.which === 13) {
        updateDateRange();
      }
    });
    });

  function updateDateRange() {
    window.location.href = jQuery.query
      .set('OphCoMessaging_from', $('#OphCoMessaging_from').val())
      .set('OphCoMessaging_to', $('#OphCoMessaging_to').val());
  }


    $('.js-expand-message').each(function(){

        var message = $(this).parent().parent().find('.message');
        var expander = new Expander( $(this),
            message );
    });

    function Expander( $icon, $message){
        var expanded = false;

        $icon.click( change );

        function change(){

            $icon.toggleClass('expand collapse');

            if(expanded){
                $message.removeClass('expand');
            } else {
                $message.addClass('expand');
            }

            expanded = !expanded;
        }
    }
</script>
