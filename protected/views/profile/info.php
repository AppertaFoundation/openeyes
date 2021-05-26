<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
    <h2>Basic information</h2>
    <?php $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => 'profile-form',
        'enableAjaxValidation' => false,
        'layoutColumns' => array(
            'label' => 2,
            'field' => 5,
        ),
    ))?>

        <?php if (!Yii::app()->params['profile_user_can_edit'] || !Yii::app()->params['profile_user_show_menu']) {?>
            <div class="alert-box alert">
                User editing of basic information is administratively disabled.
            </div>
        <?php }?>

        <?php $this->renderPartial('//base/_messages')?>
        <?php $this->renderPartial('//elements/form_errors', array('errors' => $errors))?>

<table class="standard">
  <tbody>
  <tr>
    <td>
        <?php echo $form->textField(
            $user,
            'title',
            array('autocomplete' => Yii::app()->params['html_autocomplete'],
                'readonly' => (!Yii::app()->params['profile_user_can_edit']
                    || !Yii::app()->params['profile_user_show_menu'])),
            null
        );?>
    </td>
  </tr>
  <tr>
    <td>
        <?php echo $form->textField(
            $user,
            'first_name',
            array('autocomplete' => Yii::app()->params['html_autocomplete'],
                'readonly' => (!Yii::app()->params['profile_user_can_edit']
                    || !Yii::app()->params['profile_user_show_menu']))
        );?>
    </td>
  </tr>
  <tr>
    <td>
        <?php echo $form->textField(
            $user,
            'last_name',
            array('autocomplete' => Yii::app()->params['html_autocomplete'],
                'readonly' => (!Yii::app()->params['profile_user_can_edit']
                    || !Yii::app()->params['profile_user_show_menu']))
        );?>
    </td>
  </tr>
  <tr>
      <td>
         <?php echo $form->textField(
             $user,
             'username',
             array('autocomplete' => Yii::app()->params['html_autocomplete'],
                'readonly' => true, 'style' => 'opacity:0.5')
         );?>
      </td>
  </tr>
  <tr>
    <td>
        <?php echo $form->textField(
            $user,
            'email',
            array('autocomplete' => Yii::app()->params['html_autocomplete'],
                'readonly' => (!Yii::app()->params['profile_user_can_edit']
                    || !Yii::app()->params['profile_user_show_menu']))
        );?>
    </td>
  </tr>
  <tr>
    <td>
        <?php echo $form->textField(
            $user,
            'qualifications',
            array('autocomplete' => Yii::app()->params['html_autocomplete'],
                'readonly' => (!Yii::app()->params['profile_user_can_edit']
                    || !Yii::app()->params['profile_user_show_menu']))
        );?>
    </td>
  </tr>
  <tr>
    <td>
      <div class="data-group flex-layout cols-full">
        <div class="cols-2">
          <label for="User_qualifications">Display Theme:</label>
        </div>
        <div class="cols-5">
            <?=\CHtml::dropDownList('display_theme', $display_theme, array(null => 'Default', 'light' => 'Light', 'dark' => 'Dark')); ?>
        </div>
      </div>
    </td>
  </tr>
  <tr>
      <td>
          <div class="data-group flex-layout cols-full">
              <div class="cols-2">
                  <label for="user_out_of_office">Out of office:</label>
              </div>
              <div class="cols-5 left-align">
                  <label class="inline highlight">
                      <?= $form->checkBox($user_out_of_office, 'enabled', array(
                          'nowrapper' => true,
                          'no-label' => true,
                          'checked' => $user_out_of_office->enabled ? true : false,
                          'style' =>'width:20px;')).'Yes' ?>
                  </label>
              </div>
          </div>
      </td>
  </tr>
  <tr id="duration_row" style="<?php echo $user_out_of_office->enabled ? '' : 'display: none' ?>">
      <td>
          <div class="data-group flex-layout cols-full">
              <div class="cols-2">
                  <label>Out of office duration:</label>
              </div>
              <div class="cols-5">
                  <div class="cols-9 flex-layout ">
                      <input type="text" style="width: 45%"
                             id="UserOutOfOffice_from_date"
                             name="UserOutOfOffice[from_date]"
                             value="<?= $user_out_of_office->from_date ? date('d M Y', strtotime($user_out_of_office->from_date)) : '' ?>"
                             placeholder="from" autocomplete="off">
                      <input type="text" style="width: 45%"
                             id="UserOutOfOffice_to_date"
                             name="UserOutOfOffice[to_date]"
                             value="<?= $user_out_of_office->to_date ? date('d M Y', strtotime($user_out_of_office->to_date)) : '' ?>"
                             placeholder="to" autocomplete="off">
                  </div>
              </div>
          </div>
      </td>
  </tr>
  <tr>
      <td>
          <div class="data-group flex-layout cols-full js-correspondence-sign-off">
              <div class="cols-2">
                  <label for="User_qualifications">Default correspondence sign-off:</label>
              </div>
              <div class="cols-5">

                  <label class="inline highlight ">
                      <input style="width:15px"
                            value=""
                            id="ElementLetter_is_signed_off_blank"
                            type="radio"
                            <?=$user->correspondence_sign_off_user_id === null ? 'checked' : ''?>
                            name="User[correspondence_sign_off_user_id]"><span>Blank</span>
                  </label>
                  <label class="inline highlight ">
                      <input style="width:15px"
                             value="<?=$user->id?>"
                             id="ElementLetter_is_signed_off_1"
                             type="radio"
                             <?=$user->correspondence_sign_off_user_id == $user->id ? 'checked' : ''?>
                             name="User[correspondence_sign_off_user_id]"><span>Myself</span>
                  </label>
                  <label class="inline highlight ">
                      <input style="width:15px"
                             value="<?=$user->correspondence_sign_off_user_id;?>"
                             id="correspondence_sign_off_user_id_other"
                             type="radio"
                            <?=!in_array($user->correspondence_sign_off_user_id, [$user->id, null]) ? 'checked' : ''?>
                             name="User[correspondence_sign_off_user_id]"><span>Other</span>
                  </label>
                  <div class="js-autocomplete-wrapper" style="display:
                    <?=($user->correspondence_sign_off_user_id != $user->id) && ($user->correspondence_sign_off_user_id !== null) ? "block" : "none";?>
                    ">
                      <?php $this->widget(
                          'application.widgets.AutoCompleteSearch',
                          ['htmlOptions' => ['placeholder' => 'Search users']]
                      ); ?>
                      <div class="js-autocomplete-results">
                          <ul class="oe-multi-select inline">
                              <?php if ($user->correspondence_sign_off_user_id && $user->correspondence_sign_off_user_id != $user->id) :?>
                                  <li><?=$user->signOffUser->fullName;?><i class="oe-i remove-circle small-icon pad-left"></i></li>
                              <?php endif;?>
                          </ul>
                      </div>
                  </div>
              </div>
          </div>
      </td>
  </tr>

  <tr id="alternate_user_row" style="<?php echo $user_out_of_office->enabled ? '' : 'display: none' ?>">
      <td>
          <div class="data-group flex-layout cols-full">
              <div class="cols-2">
                  <label for="alternate_user">Alternate User:</label>
              </div>
              <div class="cols-5">
                  <?php $this->widget('application.widgets.AutoCompleteSearch', ['field_name' => 'alternate_user']); ?>
                  <span id="alternate_user_display">
                      <?php if ($user_out_of_office->alternate_user) { ?>
                          <ul class="oe-multi-select inline"><li> <?= $user_out_of_office->alternate_user->getFullnameAndTitle() ?> <i class="oe-i remove-circle small-icon pad-left"></i></li></ul>
                      <?php } ?>
                  </span>
                  <?php echo $form->hiddenField($user_out_of_office, 'alternate_user_id') ?>
              </div>
          </div>
      </td>
  </tr>
  </tbody>
</table>
<?php if (Yii::app()->params['profile_user_can_edit']) {?>
      <div class="profile-actions">
          <?php echo EventAction::button('Update', 'save', null, array('class'=>'button large hint green'))->toHtml()?>
        <i class="spinner" title="Loading..." style="display: none;"></i>
      </div>
<?php }?>

<?php $this->endWidget()?>

<script>
    OpenEyes.UI.AutoCompleteSearch.init({
        input: $('#alternate_user'),
        url: '/user/autocomplete',
        onSelect: function () {
            let AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
            $('#alternate_user_display').html('<ul class="oe-multi-select inline"><li>' +
                AutoCompleteResponse.label +
                '<i class="oe-i remove-circle small-icon pad-left"></i></li></ul>');
            $('#UserOutOfOffice_alternate_user_id').val(AutoCompleteResponse.id);
            return false;
        }
    });

    $(document).ready(function() {

        pickmeup('#UserOutOfOffice_from_date', {
            format: 'd b Y',
            hide_on_select: true,
            date: $('#UserOutOfOffice_from_date').val(),
            default_date: false,
        });
        pickmeup('#UserOutOfOffice_to_date', {
            format: 'd b Y',
            hide_on_select: true,
            date: $('#UserOutOfOffice_to_date').val(),
            default_date: false,
        });

        $('#UserOutOfOffice_enabled').on('click', function () {
            if ($(this).prop("checked")) {
                $('#alternate_user_row').show();
                $('#duration_row').show();
            } else {
                $('#alternate_user_row').hide();
                $('#duration_row').hide();
            }
        });

        $(this).on('click', '.oe-i.remove-circle.small-icon.pad-left', function (e) {
            e.preventDefault();
            $(this).closest('ul').remove();
            $('#UserOutOfOffice_alternate_user_id').val('');
        });

        OpenEyes.UI.AutoCompleteSearch.init({
            input: $('#oe-autocompletesearch'),
            url: baseUrl + '/OphCoCorrespondence/default/users/correspondence-footer/true',
            onSelect: function () {
                let AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
                const $ul = document.querySelector('.js-autocomplete-results ul');

                // remove all child
                $ul.innerHTML = '';

                const name = document.createTextNode(AutoCompleteResponse.fullname);
                const $li = document.createElement("li");
                const $i = document.createElement("i");
                $i.classList.add('oe-i', 'remove-circle', 'small-icon', 'pad-left');

                $li.appendChild(name);
                $li.appendChild($i);
                $ul.appendChild($li);

                document.getElementById('correspondence_sign_off_user_id_other').value = AutoCompleteResponse.id;
            }
        });

        document.querySelector('.js-correspondence-sign-off').addEventListener('click', function(e) {
            function handler() {
                document.querySelector('.js-autocomplete-wrapper').style.display = this.id === 'correspondence_sign_off_user_id_other' ? 'block' : 'none';
            }
            // loop parent nodes from the target to the delegation node
            for (let target = e.target; target && target != this; target = target.parentNode) {
                if (target.matches('input[type=radio]')) {
                    handler.call(target, e);
                    break;
                }
            }
        }, false);

        document.querySelector('.js-correspondence-sign-off').addEventListener('click', function(e) {
            function handler() {
                const $ul = document.querySelector('.js-autocomplete-results ul');
                $ul.innerHTML = '';
                document.getElementById('correspondence_sign_off_user_id_other').value = null;
            }
            // loop parent nodes from the target to the delegation node
            for (let target = e.target; target && target != this; target = target.parentNode) {
                if (target.matches('.remove-circle')) {
                    handler.call(target, e);
                    break;
                }
            }
        }, false);
    });
</script>

