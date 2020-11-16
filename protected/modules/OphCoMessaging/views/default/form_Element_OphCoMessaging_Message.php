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
?>

<?php
if (!isset($copyto_users)) {
    $copyto_users = new \OEModule\OphCoMessaging\models\OphCoMessaging_Message_CopyTo_Users();
}
    $copyto_user_limit = Yii::app()->params['OphCoMessaging_copyto_user_limit'];
?>

<div>
  <div class="element-fields full-width flex-layout flex-top col-gap">
    <div class="cols-6">
      <table class="cols-full last-left">
        <colgroup>
          <col class="cols-3">
        </colgroup>
        <tbody>
        <tr>
          <td>
            <label for="find-user">Send to  <span class="js-has-tooltip fa oe-i info small"
                                                               data-tooltip-content="Cannot be changed after message creation."></span></label>
          </td>
            <td>
                <?php if ($element->isNewRecord) {
                    $this->widget('application.widgets.AutoCompleteSearch', ['field_name' => 'fao-search', 'htmlOptions' => ['placeholder' => 'Search for recipient']]); ?>
                    <div id="fao-field">
                        <?php if ($element->for_the_attention_of_user) {
                            echo '<ul class="oe-multi-select inline"><li>' . $element->for_the_attention_of_user->getFullnameAndTitle() . '<i class="oe-i remove-circle small-icon pad-left"></i></li></ul>';
                            echo '<script type="text/javascript">$("#fao-search").hide();</script>';
                        } ?>
                    </div>
                <?php } else { ?>
                    <div class="data-value"><?= $element->for_the_attention_of_user->getFullnameAndTitle(); ?></div>
                <?php } ?>
                <?php echo $form->hiddenField($element, 'for_the_attention_of_user_id'); ?>
            </td>
        </tr>
        <tr>
            <td>
                <label for="find-copyto">Copy to  <span class="js-has-tooltip fa oe-i info small"
                                                      data-tooltip-content="Only <?= $copyto_user_limit ?> users can be copied in."></span></label>
            </td>
            <td>
                <?php if ($element->isNewRecord) {
                    $this->widget('application.widgets.AutoCompleteSearch', ['field_name' => 'copyto-search', 'htmlOptions' => ['placeholder' => 'Search recipients (only '.$copyto_user_limit.' can be copied in)']]); ?>
                    <div id="copyto-field"></div>
                <?php } else { ?>
                        <div class="data-value">
                            <?php
                            $copied_users = [];
                            foreach ($element->copyto_users as $copied_user) {
                                array_push($copied_users, $copied_user->user->getFullnameAndTitle());
                            }
                            echo implode(', ', $copied_users);
                            ?>
                    </div>
                <?php } ?>
                <?php echo $form->hiddenField($copyto_users, 'user_id'); ?>
            </td>
        </tr>
        <tr>
          <td>
            Type
          </td>
          <td class="align-left">
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
          <td>
            Priority
          </td>
          <td style="text-align: left;">
              <label class="highlight inline">
                  <?php echo $form->checkbox(
                      $element,
                      'urgent',
                      array('nowrapper' => true, 'no-label' => true),
                      array('field' => 11)
                  ) ?>
                  <i class="oe-i status-urgent no-hover small pad-right"></i>
                  Message is urgent!
              </label>
          </td>
        </tr>
        </tbody>
      </table>
    </div>
    <div class="cols-5">
        <div class="row large-text">Your Message</div>
        <?php if ($element->isNewRecord) {
            echo CHtml::activeTextArea(
                $element,
                'message_text',
                array('class' => 'cols-full increase-text', 'placeholder' => 'Your Message...', 'rows' => 5)
            );
        } else { ?>
            <p><?= Yii::app()->format->Ntext($element->message_text) ?></p>
        <?php } ?>
    </div>
  </div>
</div>
<script>
    let userids = [];
    OpenEyes.UI.AutoCompleteSearch.init({
        input: $('#fao-search'),
        url: '/user/autocomplete',
        onSelect: function () {
            let AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
            if (checkRecipientAndCopiedUsers(AutoCompleteResponse.id)) {
                checkUserOutOfOffice(AutoCompleteResponse.id);
                $('#fao-field').html('<ul class="oe-multi-select inline"><li>'+AutoCompleteResponse.label+'<i class="oe-i remove-circle small-icon pad-left"></i></li></ul>');
                $('#OEModule_OphCoMessaging_models_Element_OphCoMessaging_Message_for_the_attention_of_user_id').val(AutoCompleteResponse.id);
                // Recipient has been added so remove the search field until the user removes it
                $('#fao-search').hide();
                // Block the user from CC'ing if they are sending message to themselves
                if (AutoCompleteResponse.id === '<?= Yii::app()->user->id ?>') {
                    $('#copyto-field').empty();
                    $('#OEModule_OphCoMessaging_models_OphCoMessaging_Message_CopyTo_Users_user_id').val('');
                    userids = [];
                    $('#copyto-search').hide();
                }
            }
            return false;
        }
    });
    OpenEyes.UI.AutoCompleteSearch.init({
        input: $('#copyto-search'),
        url: '/user/autocomplete',
        onSelect: function () {
            let AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
            // Logged in user cannot CC themselves
            if (AutoCompleteResponse.id === '<?= Yii::app()->user->id ?>') {
                return false;
            }
            // Check if the user has already been added to cc list
            if (checkRecipientAndCopiedUsers(AutoCompleteResponse.id)) {
                checkUserOutOfOffice(AutoCompleteResponse.id);
                $('#copyto-field').append('<ul class="oe-multi-select inline" id="'+ AutoCompleteResponse.id +'"><li>' + AutoCompleteResponse.label + '<i class="oe-i remove-circle small-icon pad-left"></i></li></ul>');
                userids.push(AutoCompleteResponse.id);
                $('#OEModule_OphCoMessaging_models_OphCoMessaging_Message_CopyTo_Users_user_id').val(userids.join(', '));
                if ($('#copyto-field ul').length === <?= $copyto_user_limit ?>) {
                    // The copyto user limit has reached so hide the search bar
                    $('#copyto-search').hide();
                }
            }
            return false;
        }
    });
    function checkRecipientAndCopiedUsers(user_id) {
        var usernotexists = true;
        // Check if the user is intended recipient or in copied list
        if ($('#OEModule_OphCoMessaging_models_Element_OphCoMessaging_Message_for_the_attention_of_user_id').val() === user_id) {
            usernotexists = false;
        } else {
            $.each($('#copyto-field ul'), function () {
                if ($(this).attr('id') === user_id) {
                    usernotexists = false;
                    return false;
                }
            });
        }
        return usernotexists;
    }
    function checkUserOutOfOffice(user_id) {
        $.ajax({
            data: {user_id: user_id,
                YII_CSRF_TOKEN: $('input[name="YII_CSRF_TOKEN"]').val(),
                event_date: $('extra-info .js-event-date').val() },
            type: 'POST',
            url: '<?php echo Yii::app()->createUrl($this->getModule()->name.'/Default/checkUserOutOfOffice/') ?>',
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
    $(document).on('click', '.oe-i.remove-circle.small-icon.pad-left', function (e) {
        e.preventDefault();
        let hiddenField = $(this).closest('td').children('input');
        let userField = $(this).closest('ul');
        // If the user belongs to one of ids in the copied to user list
        if (hiddenField.attr('id') === 'OEModule_OphCoMessaging_models_OphCoMessaging_Message_CopyTo_Users_user_id') {
            userids.splice(userids.indexOf(userField.attr('id')), 1);
            hiddenField.val(userids.join(', '));
            // Show the copyto search bar once one of the users has been removed
            $('#copyto-search').show();
        }
        // There is a single recipient so remove it directly
        else {
            if (hiddenField.val() === '<?= Yii::app()->user->id ?>') {
                $('#copyto-search').show();
            }
            hiddenField.val('');
            $('#fao-search').show();
        }
        userField.remove();
    });
</script>
