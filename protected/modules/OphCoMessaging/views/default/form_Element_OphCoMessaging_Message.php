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
use OEModule\OphCoMessaging\models\OphCoMessaging_Message_Recipient;
use OEModule\OphCoMessaging\models\OphCoMessaging_Message_MessageType;

$for_the_attention_of = $element->for_the_attention_of ?? new OphCoMessaging_Message_Recipient();

if ($for_the_attention_of->isNewRecord) {
    $for_the_attention_of->primary_recipient = 1;
}

if (!isset($cc_recipients)) {
    $cc_recipients = new OphCoMessaging_Message_Recipient();
}

$copy_to_recipient_limit = Yii::app()->params['OphCoMessaging_copyto_user_limit'];
$personal_mailbox_id = Mailbox::model()->forPersonalMailbox(\Yii::app()->user->id)->find()->id;

$general_type_id = OphCoMessaging_Message_MessageType::model()->find('name = "General"')->id;
$query_type_id = OphCoMessaging_Message_MessageType::model()->find('name = "Query"')->id;

if ($element->isNewRecord) {
    $element->message_type_id = $general_type_id;
    $element->sender_mailbox_id = $personal_mailbox_id;
}
?>

<div>
  <div class="element-fields full-width">
    <div class="flex-t">
      <div class="cols-5">
        <table class="cols-full last-left">
          <colgroup>
            <col class="cols-3" />
          </colgroup>
          <tbody>
            <tr>
              <td>Send to</td>
              <td>
                <?php
                if ($element->isNewRecord) {
                    $this->widget('application.widgets.AutoCompleteSearch', ['field_name' => 'fao-search', 'htmlOptions' => ['placeholder' => 'Search for recipient', 'data-test' => 'fao-search']]);
                    ?>
                    <div id="fao-field">
                        <?php if ($element->for_the_attention_of) { ?>
                            <ul class="oe-multi-select inline"><li><?= $element->for_the_attention_of->mailbox->name ?><i class="oe-i remove-circle small-icon pad-left"></i></li></ul>
                            <script type="text/javascript">$("#fao-search").hide();</script>
                        <?php } ?>
                    </div>
                <?php } else { ?>
                <div class="data-value"><?= $element->for_the_attention_of->mailbox->name; ?></div>
                <?php } ?>
              </td>
            </tr>
            <tr>
              <td>Copy to <small>(<?= $copy_to_recipient_limit ?> max.)</small></td>
              <td>
                <?php
                if ($element->isNewRecord) {
                    $this->widget('application.widgets.AutoCompleteSearch', ['field_name' => 'copyto-search', 'htmlOptions' => ['placeholder' => 'Search recipients (only ' . $copy_to_recipient_limit . ' can be copied in)']]);
                    ?>
                <div id="copyto-field"></div>
                <?php } else { ?>
                <div class="data-value">
                    <?= implode(array_map(static function ($recipient) {
    return $recipient->mailbox->name;
                    }, $element->cc_recipients)) ?>
                </div>
                <?php } ?>
              </td>
            </tr>
            <tr>
          <td>
            Type
          </td>
          <td>
              <?php echo $form->radioButtons(
                  $element,
                  'message_type_id',
                  CHtml::listData(
                      OEModule\OphCoMessaging\models\OphCoMessaging_Message_MessageType::model()->findAll(array('order' => 'display_order asc')),
                      'id',
                      'name'
                  ),
                  $element->message_type_id ? $element->message_type_id : 2,
                  false,
                  false,
                  false,
                  false,
                  array('nowrapper' => true)
              ) ?>
          </td>
        </tr>
            <tr>
              <td>Priority</td>
              <td>
                <label class="highlight inline">
                  <?= CHtml::activeCheckBox($element, 'urgent') ?>
                  Message is urgent <i class="oe-i status-urgent small pad-l"></i>
                </label>
              </td>
            </tr>
          </tbody>
        </table>
        <?= $form->hiddenField($element, 'sender_mailbox_id') ?>
      </div>
      <div class="cols-6">
        <div class="highlighter">
          Information in a message should relate only to the clinical care of the patient. Messages should not be used for the purposes of general communication between users.
        </div>
        <div class="row">
          <b>Messages are part of the patient record and can not be edited once sent.</b>
        </div>
        <div class="msg-editor">
            <?php if ($element->isNewRecord) { ?>
                <?= CHtml::activeTextArea(
                    $element,
                    'message_text',
                    [
                      'class' => 'cols-full increase-text autosize msg-write js-editor-area',
                      'placeholder' => 'Your Message...',
                      'rows' => 1,
                      'data-test' => 'your-message'
                    ]
                ) ?>
                <div class="msg-preview js-preview-area" style="display: none"></div>
                <div class="msg-actions js-preview-action">
                    <button class="blue hint js-preview-message" type="button" data-test="preview-and-check">Preview & check</button>
                </div>
                <div class="msg-actions js-edit-or-send-actions" style="display: none">
                    <button class="blue hint js-edit-message" type="button" data-test="edit-message">Edit message</button>
                    <button class="green hint" type="submit" data-test="send-message">Send message</button>
                </div>
            <?php } else { ?>
                <div class="msg-preview"><?= Yii::app()->format->Ntext(preg_replace("/\n/", "", preg_replace('/(\s{4})\s+/', '$1', $element->message_text))) ?></div>
            <?php } ?>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
   function checkRecipientAndCopiedUsers(recipient_id) {
        var usernotexists = true;

        $.each($('input[data-recipient-index]'), function () {
                if ($(this).attr('value') === recipient_id) {
                    usernotexists = false;
                }
            });

        return usernotexists;
    }

    function checkUserOutOfOffice(mailbox_id) {
        $.ajax({
            data: {
              mailbox_id: mailbox_id,
              YII_CSRF_TOKEN: $('input[name="YII_CSRF_TOKEN"]').val(),
              event_date: $('extra-info .js-event-date').val()
            },
            type: 'POST',
            url: '<?= Yii::app()->createUrl($this->getModule()->name . '/Default/checkUserOutOfOffice/') ?>',
            success: function (response) {
                if (response) {
                    var outOfOfficeDialog = new OpenEyes.UI.Dialog.Alert({
                        title: 'User is out of office',
                        content: response,
                    });
                    outOfOfficeDialog.open()
                }
            },
            error: function () {
                new OpenEyes.UI.Dialog.Alert({
                    content: "Sorry, an internal error occurred.\n\nPlease contact support for assistance."
                }).open();
            },
        });
   }

   function makeRecipient(id, name, isPrimaryRecipient) {
     const primaryValue = isPrimaryRecipient ? '1' : '0';

     const recipientLabel = `<li>${name}<i class="oe-i remove-circle small-icon pad-left"></i></li>`;
     const mailboxField = `<input type="hidden" name="OEModule_OphCoMessaging_models_OphCoMessaging_Message_Recipient[mailbox_id][${id}]" value="${id}" data-recipient-index="{$index}" />`;
     const primaryRecipientField = `<input type="hidden" name="OEModule_OphCoMessaging_models_OphCoMessaging_Message_Recipient[primary_recipient][${id}]" value="${primaryValue}" />`;

     return $('<ul class="oe-multi-select inline">' + recipientLabel + mailboxField + primaryRecipientField + '</ul>');
   }

   function splitLinesIntoBRsIn(intoContainer, text)
   {
     const lines = text.split('\n');

     intoContainer.empty();

     for (line of lines) {
       intoContainer.append(document.createTextNode(line));
       intoContainer.append('<br />');
     }
   }

   $(document).ready(function() {
     OpenEyes.UI.AutoCompleteSearch.init({
        input: $('#fao-search'),
        url: '/OphCoMessaging/Default/autocompleteMailbox',
        onSelect: function () {
            let AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();

            if (checkRecipientAndCopiedUsers(AutoCompleteResponse.id)) {
                checkUserOutOfOffice(AutoCompleteResponse.id);
                const faoField = $('#fao-field');

                faoField.empty();
                faoField.append(makeRecipient(AutoCompleteResponse.id, AutoCompleteResponse.label, true));

                // Recipient has been added so remove the search field until the user removes it
                $('#fao-search').hide();
                // Block the user from CC'ing if they are sending message to themselves
                if (AutoCompleteResponse.id === '<?= $personal_mailbox_id ?>') {
                    $('#copyto-field').empty();
                    $('#copyto-search').hide();
                }
            }
            return false;
        }
    });

    OpenEyes.UI.AutoCompleteSearch.init({
        input: $('#copyto-search'),
        url: '/OphCoMessaging/Default/autocompleteMailbox',
        onSelect: function () {
            let AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
            // Logged in user cannot CC themselves
            if (AutoCompleteResponse.id === '<?= $personal_mailbox_id ?>') {
                return false;
            }
            // Check if the user has already been added to cc list
            if (checkRecipientAndCopiedUsers(AutoCompleteResponse.id)) {
                checkUserOutOfOffice(AutoCompleteResponse.id);

                $('#copyto-field').append(makeRecipient(AutoCompleteResponse.id, AutoCompleteResponse.label, false));

                if ($('#copyto-field ul').length === <?= $copy_to_recipient_limit ?>) {
                    // The copyto user limit has reached so hide the search bar
                    $('#copyto-search').hide();
                }
            }
            return false;
        }
    });

     $(document).on('click', '.oe-i.remove-circle.small-icon.pad-left', function (e) {
        e.preventDefault();

        const userField = $(this).closest('ul');

        const is_primary = userField.find('input[name^="OEModule_OphCoMessaging_models_OphCoMessaging_Message_Recipient[primary_recipient]"]').val();

        if (is_primary === '1') {
          $('#fao-search').show();
        } else {
          $('#copyto-search').show();
        }

        userField.remove();
    });

    $('.js-preview-action').click(function() {
      splitLinesIntoBRsIn($('.js-preview-area'), $('.js-editor-area').val())

      $('.js-preview-action, .js-editor-area').hide();
      $('.js-edit-or-send-actions, .js-preview-area').show();
    });

    $('.js-edit-message').click(function() {
      $('.js-preview-action, .js-editor-area').show();
      $('.js-edit-or-send-actions, .js-preview-area').hide();
    });

    $('.js-message-is-reply').change(function() {
      const newState = $(this).attr('checked') === 'checked';

      $('.js-message-type').val(newState ? '<?= $query_type_id ?>' : '<?= $general_type_id ?>');
    });
   });
</script>
