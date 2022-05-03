<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>
<section class="element view full patient-info patient-contacts js-toggle-container">
    <header class="element-header">
        <h3 class="element-title">Associated contacts</h3>
    </header>
  <div class="element-data full-width js-toggle-body">
    <table class="plain patient-data patient-contacts">
      <thead>
      <tr>
        <th>Name</th>
        <th>Location</th>
        <th>Type</th>
            <?php if ($this->checkAccess('OprnEditContact')) { ?>
            <?php } ?>
      </tr>
      </thead>
      <tbody id="patient_contacts">
        <?php
        foreach ($this->patient->contactAssignments as $pca) {
            $this->renderPartial('_patient_contact_row', array('pca' => $pca));
        }
        ?>
      </tbody>
    </table>
        <?php if ($this->checkAccess('OprnEditContact')) { ?>
        <div class="data-group">
          <div class="cols-2 column">
            <label for="contactname" class="align">Add contact:</label>
          </div>

          <div class="cols-4 column">
              <?php
                $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                  'name' => 'contactname',
                  'id' => 'contactname',
                  'value' => '',
                  'source' => "js:function(request, response) {

                    $('#btn-add-contact').hide();
        
                    var filter = $('#contactfilter').val();
        
                    $('img.loader').show();
        
                    $.ajax({
                      'url': '" . Yii::app()->createUrl('patient/possiblecontacts') . "',
                      'type':'GET',
                      'data':{'term': request.term, 'filter': filter},
                      'success':function(data) {
                        data = $.parseJSON(data);
        
                        var result = [];
        
                        contactCache = {};
        
                        for (var i = 0; i < data.length; i++) {
                          if (data[i]['contact_location_id']) {
                            if ($.inArray(data[i]['contact_location_id'], currentContacts['locations']) == -1) {
                              result.push(data[i]['line']);
                              contactCache[data[i]['line']] = data[i];
                            }
                          } else {
                            if ($.inArray(data[i]['contact_id'], currentContacts['contacts']) == -1) {
                              result.push(data[i]['line']);
                              contactCache[data[i]['line']] = data[i];
                            }
                          }
                        }
        
                        response(result);
        
                        $('img.loader').hide();
        
                        if (filter != 'users') {
                          $('#btn-add-contact').show();
                        }
                      }
                    });
                  }",
                'options' => array(
                    'minLength' => '3',
                    'select' => "js:function(event, ui) {
                      var value = ui.item.value;
        
                      $('#contactname').val('');
        
                      if (contactCache[value]['contact_location_id']) {
                        var querystr = 'patient_id=" . $this->patient->id . "&contact_location_id='+contactCache[value]['contact_location_id'];
                      } else {
                        var querystr = 'patient_id=" . $this->patient->id . "&contact_id='+contactCache[value]['contact_id'];
                      }
        
                      $.ajax({
                        'type': 'GET',
                        'url': '" . Yii::app()->createUrl('patient/associatecontact') . "?'+querystr,
                        'success': function(html) {
                          if (html.length >0) {
                            $('#patient_contacts').append(html);
                            if (contactCache[value]['contact_location_id']) {
                              currentContacts['locations'].push(contactCache[value]['contact_location_id']);
                            } else {
                              currentContacts['contacts'].push(contactCache[value]['contact_id']);
                            }
        
                            $('#btn-add-contact').hide();
                          }
                        }
                      });
        
                      return false;
                    }",
                  ),
                  'htmlOptions' => array(
                      'placeholder' => 'search for contacts',
                  ),
                ));
                ?>
          </div>

          <div class="cols-4 column">
            <select id="contactfilter" name="contactfilter">
                <?php foreach (ContactLabel::getList() as $key => $name) { ?>
                  <option value="<?= $key ?>"><?= $name ?></option>
                <?php } ?>
            </select>
          </div>

          <div class="cols-2 column">
            <button id="btn-add-contact" class="secondary small hide" type="button">Add</button>
          </div>
        </div>

        <div id="add_contact" style="display: none;">
            <?php
            $form = $this->beginWidget('FormLayout', array(
                'id' => 'add-contact',
                'enableAjaxValidation' => false,
                'action' => array('patient/addContact'),
                'layoutColumns' => array(
                    'label' => 3,
                    'field' => 7,
                ),
            )) ?>
          <fieldset>
            <legend>Add contact</legend>

            <input type="hidden" name="patient_id" value="<?= $this->patient->id ?>"/>
            <input type="hidden" name="contact_label_id" id="contact_label_id" value=""/>

            <div class="data-group">
              <div class="cols-<?= $form->columns('label'); ?>">
                <div class="data-label">Type:</div>
              </div>
              <div class="<?= $form->columns('field'); ?>">
                <div class="data-value contactType"></div>
              </div>
            </div>

            <div class="data-group">
              <div class="cols-<?= $form->columns('label'); ?>">
                <label for="institution_id">Institution:</label>
              </div>
              <div class="cols-<?= $form->columns('field'); ?>">
                  <?=\CHtml::dropDownList(
                      'institution_id',
                      '',
                      CHtml::listData(Institution::model()->active()->findAll(array('order' => 'name')), 'id', 'name'),
                      array('empty' => '- Select -')
                  ) ?>
              </div>
            </div>

            <div class="data-group siteID">
              <div class="<?= $form->columns('label'); ?>">
                <label for="site_id">Site:</label>
              </div>
              <div class="cols-<?= $form->columns('field'); ?>">
                  <?=\CHtml::dropDownList('site_id', '', array()) ?>
              </div>
            </div>

            <div class="data-group contactLabel">
              <div class="cols-<?= $form->columns('label'); ?>">
                <label for="label_id">Label:</label>
              </div>
              <div class="cols-<?= $form->columns('field'); ?>">
                  <?=\CHtml::dropDownList(
                      'label_id',
                      '',
                      CHtml::listData(ContactLabel::model()->active()->findAll(array('order' => 'name')), 'id', 'name'),
                      array('empty' => '- Select -')
                  ) ?>
              </div>
            </div>

            <div class="data-group">
              <div class="cols-<?= $form->columns('label'); ?>">
                <label for="title">Title:</label>
              </div>
              <div class="cols-<?= $form->columns('field'); ?>">
                  <?=\CHtml::textField('title', '', array('autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'))) ?>
              </div>
            </div>

            <div class="data-group">
              <div class="cols-<?= $form->columns('label'); ?>">
                <label for="first_name">First name:</label>
              </div>
              <div class="cols-<?= $form->columns('field'); ?>">
                  <?=\CHtml::textField('first_name', '', array('autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'))) ?>
              </div>
            </div>

            <div class="data-group">
              <div class="cols-<?= $form->columns('label'); ?>">
                <label for="last_name">Last name:</label>
              </div>
              <div class="cols-<?= $form->columns('field'); ?>">
                  <?=\CHtml::textField('last_name', '', array('autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'))) ?>
              </div>
            </div>

            <div class="data-group">
              <div class="cols-<?= $form->columns('label'); ?>">
                <label for="nick_name">Nick name:</label>
              </div>
              <div class="<?= $form->columns('field'); ?>">
                  <?=\CHtml::textField('nick_name', '', array('autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'))) ?>
              </div>
            </div>

            <div class="data-group">
              <div class="cols-<?= $form->columns('label'); ?>">
                <label for="primary_phone">Primary phone:</label>
              </div>
              <div class="cols-<?= $form->columns('field'); ?>">
                  <?=\CHtml::textField('primary_phone', '', array('autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'))) ?>
              </div>
            </div>

            <div class="data-group">
              <div class="cols-<?= $form->columns('label'); ?>">
                <label for="qualifications">Qualifications:</label>
              </div>
              <div class="cols-<?= $form->columns('field'); ?>">
                  <?=\CHtml::textField('qualifications', '', array('autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'))) ?>
              </div>
            </div>

            <div class="add_contact_form_errors alert-box alert with-icon hide"></div>

            <div class="data-group">
              <div class="cols-5 column">
                <button class="small btn_add_site" type="submit">Add site/institution</button>
              </div>
              <div class="cols-7 column text-right">
                <i class="spinner" title="Loading..." style="display: none;"></i>
                <button class="secondary small btn_save_contact" type="submit">Save</button>
                <button class="warning small btn_cancel_contact" type="submit">Cancel</button>
              </div>
            </div>
          </fieldset>

            <?php $this->endWidget() ?>
        </div>

        <div id="edit_contact" style="display: none;">
            <?php
            $form = $this->beginWidget('FormLayout', array(
                'id' => 'edit-contact',
                'enableAjaxValidation' => false,
                'action' => array('patient/editContact'),
                'layoutColumns' => array(
                    'label' => 3,
                    'field' => 9,
                ),
            )) ?>

          <fieldset>
            <legend>Edit contact</legend>

            <input type="hidden" name="patient_id" value="<?= $this->patient->id ?>"/>
            <input type="hidden" name="contact_id" id="contact_id" value=""/>
            <input type="hidden" name="pca_id" id="pca_id" value=""/>

            <div class="data-group">
              <div class="<?= $form->columns('label'); ?>">
                <div class="data-label">Contact:</div>
              </div>
              <div class="<?= $form->columns('field'); ?>">
                <div class="data-value editContactName"></div>
              </div>
            </div>

            <div class="data-group">
              <div class="<?= $form->columns('label'); ?>">
                <div class="label">Institution:</div>
              </div>
              <div class="<?= $form->columns('field'); ?>">
                  <?=\CHtml::dropDownList(
                      'institution_id',
                      '',
                      CHtml::listData(Institution::model()->active()->findAll(array('order' => 'name')), 'id', 'name'),
                      array('empty' => '- Select -')
                  ) ?>
              </div>
            </div>

            <div class="data-group siteID">
              <div class="<?= $form->columns('label'); ?>">
                <div class="label">Site:</div>
              </div>
              <div class="<?= $form->columns('field'); ?>">
                  <?=\CHtml::dropDownList('site_id', '', array()) ?>
              </div>
            </div>

            <div class="edit_contact_form_errors alert-box alert with-icon hide"></div>

            <div class="data-group">
              <div class="cols-5 column">
                <button class="small btn_add_site" type="submit">Add site/institution</button>
              </div>
              <div class="cols-7 column text-right">
                <button class="secondary small btn_save_editcontact" type="submit">Save</button>
                <button class="warning small btn_cancel_editcontact" type="submit">Cancel</button>
              </div>
            </div>
          </fieldset>

            <?php $this->endWidget() ?>
        </div>

        <!-- Add site or institution dialog -->
        <div id="add_site_dialog" title="Add site/institution" style="display: none;">
          <p>
            This form allows you to send a request to the OpenEyes support team to add a site/institution to the system for you.
          </p>
            <?php
            $form = $this->beginWidget('FormLayout', array(
                'id' => 'add_site_form',
                'enableAjaxValidation' => false,
                'action' => array('patient/sendSiteMessage'),
                'layoutColumns' => array(
                    'label' => 3,
                    'field' => 9,
                ),
            ))
            ?>
          <div class="data-group">
            <div class="<?= $form->columns('label'); ?>">
              <label for="newsite_from">From:</label>
            </div>
            <div class="<?= $form->columns('field'); ?>">
                <?=\CHtml::textField(
                    'newsite_from',
                    User::model()->findByPk(Yii::app()->user->id)->email,
                    array('autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'))
                ) ?>
            </div>
          </div>
          <div class="data-group">
            <div class="<?= $form->columns('label'); ?>">
              <label for="newsite_subject">Subject:</label>
            </div>
            <div class="<?= $form->columns('field'); ?>">
                <?=\CHtml::textField(
                    'newsite_subject',
                    'Please add the following site/institution',
                    array('autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'))
                ) ?>
            </div>
          </div>
          <div class="data-group">
            <div class="<?= $form->columns('label'); ?>">
              <label for="newsite_message">Message:</label>
            </div>
            <div class="<?= $form->columns('field'); ?>">
                <?=\CHtml::textArea('newsite_message', "Please could you add the following site/institution to OpenEyes:\n\n", array('rows' => 7, 'cols' => 55)) ?>
            </div>
          </div>
            <?php $this->endWidget() ?>
          <p>
            We will respond to your request via email as soon as it has been completed.
          </p>
          <div class="buttons">
            <button type="submit" class="secondary small btn_add_site_ok">Send</button>
            <button type="submit" class="warning small btn_add_site_cancel">Cancel</button>
          </div>
        </div>
        <?php } ?>
  </div>
</section>
<script type="text/javascript">
  $(document).ready(function () {
    $('#btn-add-contact').click(function () {
                    if ($('#add_contact').is(':hidden')) {
                        $('#add_contact').slideToggle('fast');
                        $('#contact_label_id').val($('#contactfilter').val());
                        if ($('#contactfilter').val() == 'nonspecialty') {
                                $('div.contactLabel').show();
                        } else {
                            $('div.contactLabel').hide();
                        }

                            $('#add_contact .contactType').text($('#contactfilter option:selected').text());
                            $('#add_contact #site_id').html('<option value="">- Select -</option>');
                            $('#add_contact .siteID').hide();
                            $('#add_contact #institution_id').val('');
                            $('#add_contact #title').val('');
                            $('#add_contact #first_name').val('');
                            $('#add_contact #last_name').val('');
                            $('#add_contact #nick_name').val('');
                            $('#add_contact #primary_phone').val('');
                            $('#add_contact #qualifications').val('');
                            $('#btn-add-contact').hide();
                    }
                    });
                    $('#contactfilter').change(function () {
                            if (!$('#add_contact').is(':hidden')) {
                                $('#add_contact').slideToggle('fast');
                            }
                            $('#btn-add-contact').hide();

                            if ($('#contactname').val().length >= 3) {
                                $('#contactname').focus();
                                $('#contactname').autocomplete('search', $('#contactname').val());
                            }
                        });
                    $('#add_contact #institution_id').change(function () {
                            var institution_id = $(this).val();

                            if (institution_id != '') {
                                $.ajax({
                                'type': 'GET',
                                'dataType': 'json',
                                'url': baseUrl + '/patient/institutionSites?institution_id=' + institution_id,
                                'success': function (data) {
                                                  var options = '<option value="">- Select -</option>';
                                    for (var i in data) {
                                        options += '<option value="' + i + '">' + data[i] + '</option>';
                                    }
                                    $('#add_contact #site_id').html(options);
                                    sort_selectbox($('#add_contact #site_id'));
                                    if (i > 0) {
                                        $('#add_contact .siteID').show();
                                    } else {
                                        $('#add_contact .siteID').hide();
                                    }
                                }
                                });
                            } else {
                                $('#add_contact .siteID').hide();
                            }
                        });
                    $('button.btn_cancel_contact').click(function (e) {
                            e.preventDefault();
                            $('#add_contact').slideToggle('fast');
                            $('#btn-add-contact').hide();
                        });
                    handleButton($('button.btn_save_contact'), function (e) {
                            e.preventDefault();

                            $.ajax({
                            'type': 'POST',
                            'dataType': 'json',
                            'data': $('#add-contact').serialize() + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
                            'url': baseUrl + '/patient/validateSaveContact',
                            'success': function (data) {
                                $('.add_contact_form_errors').hide().html('');
                                if (data.length == 0) {
                                    $('img.add_contact_loader').show();
                                    $('#add-contact').submit();
                                    return true;
                                } else {
                                    for (var i in data) {
                                                      $('.add_contact_form_errors').show().append('<div>' + data[i] + '</div>');
                                    }
                                    enableButtons();
                                }
                            }
                            });
                        });
                    $('a.editContact').die('click').live('click', function (e) {
                            e.preventDefault();

                            var location_id = $(this).parent().parent().attr('data-attr-location-id');
                            var pca_id = $(this).parent().parent().attr('data-attr-pca-id');

                            $.ajax({
                            'type': 'GET',
                            'dataType': 'json',
                            'url': baseUrl + '/patient/getContactLocation?location_id=' + location_id,
                            'success': function (data) {
                                editContactSiteID = data['site_id'];
                                $('#edit_contact #institution_id').val(data['institution_id']);
                                $('#edit_contact #institution_id').change();
                                $('#edit_contact .editContactName').text(data['name']);
                                $('#edit_contact #contact_id').val(data['contact_id']);
                                $('#edit_contact #pca_id').val(pca_id);
                            }
                            });

                            if ($('#edit_contact').is(':hidden')) {
                                $('#edit_contact').slideToggle('fast');
                            }
                        });
                    $('#edit_contact #institution_id').change(function () {
                            var institution_id = $(this).val();

                            if (institution_id != '') {
                                $.ajax({
                                'type': 'GET',
                                'dataType': 'json',
                                'url': baseUrl + '/patient/institutionSites?institution_id=' + institution_id,
                                'success': function (data) {
                                                  var options = '<option value="">- Select -</option>';
                                    for (var i in data) {
                                        options += '<option value="' + i + '">' + data[i] + '</option>';
                                    }
                                    $('#edit_contact #site_id').html(options);
                                    sort_selectbox($('#edit_contact #site_id'));
                                    if (i > 0) {
                                        $('#edit_contact .siteID').show();
                                    } else {
                                        $('#edit_contact .siteID').hide();
                                    }

                                    if (editContactSiteID) {
                                        $('#edit_contact #site_id').val(editContactSiteID);
                                        editContactSiteID = null;
                                    }
                                }
                                });
                            } else {
                                $('#edit_contact .siteID').hide();
                            }
                        });
                    $('button.btn_save_editcontact').click(function (e) {
                            e.preventDefault();

                            $.ajax({
                            'type': 'POST',
                            'dataType': 'json',
                            'data': $('#edit-contact').serialize() + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
                            'url': baseUrl + '/patient/validateEditContact',
                            'success': function (data) {
                                $('div.edit_contact_form_errors').hide().html('');
                                if (data.length == 0) {
                                    $('img.edit_contact_loader').show();
                                    $('#edit-contact').submit();
                                    return true;
                                } else {
                                    for (var i in data) {
                                                      $('div.edit_contact_form_errors').show().append('<div>' + data[i] + '</div>');
                                    }
                                    enableButtons();
                                }
                            }
                            });
                        });
                    $('button.btn_cancel_editcontact').click(function (e) {
                            e.preventDefault();

                            if (!$('#edit_contact').is(':hidden')) {
                                $('#edit_contact').slideToggle('fast');
                            }
                        });
                    $('button.btn_add_site').click(function (e) {
                            e.preventDefault();

                            $('#newsite_from').val('<?= User::model()->findByPk(Yii::app()->user->id)->email ?>');
                            $('#newsite_subject').val('Please add the following site/institution');
                            $('#newsite_message').val("Please could you add the following site/institution to OpenEyes:\n\n");

                            $('#add_site_dialog').dialog({
                            resizable: false,
                            modal: true,
                            width: 560
                            });

                            $('#newsite_message').focus();
                            var length = $('#newsite_message').val().length;
                            $('#newsite_message').selectRange(length, length);
                        });
                    $('button.btn_add_site_cancel').click(function (e) {
                            e.preventDefault();
                            $('#add_site_dialog').dialog('close');
                        });
                    $('button.btn_add_site_ok').click(function (e) {
                            e.preventDefault();

                            $.ajax({
                            'type': 'POST',
                            'data': $('#add_site_form').serialize() + "&YII_CSRF_TOKEN=" + YII_CSRF_TOKEN,
                            'url': baseUrl + '/patient/sendSiteMessage',
                            'success': function (html) {
                                if (html == "1") {
                                    $('#add_site_dialog').dialog('close');
                                    new OpenEyes.UI.Dialog.Alert({
                                    content: "Your request has been sent, we aim to process requests within 1 working day."
                                    }).open();
                                } else {
                                    new OpenEyes.UI.Dialog.Alert({
                                    content: "There was an unexpected error sending your message, please try again or contact support for assistance."
                                    }).open();
                                }
                            }
                            });
                        });
                    }
                );

                        $.fn.selectRange = function (start, end) {
                            return this.each(function () {
                                if (this.setSelectionRange) {
                                                      this.focus();
                                                      this.setSelectionRange(start, end);
                                } else if (this.createTextRange) {
                                                    var range = this.createTextRange();
                                                    range.collapse(true);
                                                    range.moveEnd('character', end);
                                                    range.moveStart('character', start);
                                                    range.select();
                                }
                            });
                        };

                        var editContactSiteID = null;

                        </script>
