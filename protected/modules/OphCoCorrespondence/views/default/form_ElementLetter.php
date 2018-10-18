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


<?php echo $form->hiddenInput($element, 'draft', 1) ?>
<?php
Yii::app()->clientScript->registerScriptFile("{$this->assetPath}/js/macros.js", \CClientScript::POS_HEAD);
$api = Yii::app()->moduleAPI->get('OphCoCorrespondence');
$layoutColumns = $form->layoutColumns;

$macro_id = isset($_POST['macro_id']) ? $_POST['macro_id'] : (isset($element->macro->id) ? $element->macro->id : null);

if (!$macro_id) {
    $macro_id = isset($element->document_instance[0]->document_instance_data[0]->macro_id) ? $element->document_instance[0]->document_instance_data[0]->macro_id : null;
}

$macro_letter_type_id = null;
if ($macro_id) {
    $macro = LetterMacro::model()->findByPk($macro_id);
    $macro_letter_type_id = $macro->letter_type_id;
}

$element->letter_type_id = ($element->letter_type_id ? $element->letter_type_id : $macro_letter_type_id);
$patient_id = Yii::app()->request->getQuery('patient_id', null);
$patient = Patient::model()->findByPk($patient_id);

$creating = $creating ?: false;
?>
<?php if ($creating === false): ?>
  <input type="hidden" id="re_default" value="<?php echo $element->calculateRe($element->event->episode->patient) ?>"/>
<?php endif; ?>

<div class="element-fields full-width flex-layout flex-top col-gap">

    <?php
    $correspondeceApp = Yii::app()->params['ask_correspondence_approval'];
    if ($correspondeceApp === "on") {
        ?>
      <div class="cols-3">
        <div class="data-group">
          <table class="cols-full">
            <colgroup>
              <col class="cols-5">
              <col class="cols-7">
            </colgroup>
            <tbody>
            <tr>
              <td>
                Macro
              </td>
              <td>
                  <?=\CHtml::dropDownList('macro_id', $macro_id, $element->letter_macros,
                      array('empty' => '- Macro -', 'nowrapper' => true, 'class' => 'cols-full')); ?>
              </td>
            </tr>
            <tr>
              <td>
                  <?php echo $element->getAttributeLabel('is_signed_off') ?>:
              </td>
              <td>
                  <?php echo $form->radioButtons($element, 'is_signed_off', array(
                      1 => 'Yes',
                      0 => 'No',
                  ),
                      $element->is_signed_off,
                      false, false, false, false,
                      array('nowrapper' => true)
                  ); ?>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
        <div class="data-group">
          <table class="cols-full pad-top">
            <tbody>
            <tr>
              <td>
                Site
              </td>
              <td>
                  <?php echo $form->dropDownList($element, 'site_id', Site::model()->getLongListForCurrentInstitution(),
                      array('empty' => '- Please select -', 'nowrapper' => true)) ?>
              </td>
            </tr>
            <tr>
              <td>
                Date
              </td>
              <td>
                  <?php echo $form->datePicker($element, 'date', array('maxDate' => 'today'),
                      array('nowrapper' => true)) ?>
              </td>
            </tr>
            <tr>
              <td>
                Letter type
              </td>
              <td>
                  <?php echo $form->dropDownList($element, 'letter_type_id',
                      CHtml::listData(LetterType::model()->getActiveLetterTypes(), 'id', 'name'),
                      array('empty' => '- Please select -', 'nowrapper' => true, 'class' => 'full-width')) ?>
              </td>
            </tr>
            <!--                  Clinic Date  -->
            <tr>
              <td>
                Clinic Date
              </td>
              <td>
                  <?php echo $form->datePicker($element, 'clinic_date', array('maxDate' => 'today'),
                      array('nowrapper' => true, 'null' => true)) ?>
              </td>
            </tr>
            <!--                    Direct Line-->
            <tr>
              <td>
                Direct Line
              </td>
              <td>
                  <?php echo $form->textField($element, 'direct_line', array('nowrapper' => true), array(),
                      array_merge($layoutColumns, array('field' => 2))) ?>
              </td>
            </tr>
            <!--                    Fax-->
            <tr>
              <td>
                Fax
              </td>
              <td>
                  <?php echo $form->textField($element, 'fax', array('nowrapper' => true), array(),
                      array_merge($layoutColumns, array('field' => 2))) ?>
              </td>
            </tr>
            <tr>
                <?php if ($element->isInternalReferralEnabled()): ?>

                  <div
                      class="data-group internal-referrer-wrapper <?php echo $element->isInternalreferral() ? '' : 'hidden'; ?> ">
                    <div class="cols-2 column"></div>

                    <div class="cols-10 column">
                        <?php $this->renderPartial('_internal_referral', array('element' => $element)); ?>
                    </div>
                  </div>

                <?php endif; ?>
            </tr>
            </tbody>
          </table>
        </div>
        <div class="data-group">
          <table class="cols-full last-left pad-top">
            <tbody>
            <tr>
              <td>
                &nbsp;
              </td>
            </tr>
            <tr>
              <td class="large-text">
                Insert Quick Text
              </td>
            </tr>
            <tr>
              <td>
                <div class="column">
                    <?php
                    $firm = Firm::model()->with('serviceSubspecialtyAssignment')->findByPk(Yii::app()->session['selected_firm_id']);

                    $event_types = array();
                    foreach (EventType::model()->with('elementTypes')->findAll() as $event_type) {
                        $event_types[$event_type->class_name] = array();

                        foreach ($event_type->elementTypes as $elementType) {
                            $event_types[$event_type->class_name][] = $elementType->class_name;
                        }
                    }

                    if (isset($_GET['patient_id'])) {
                        $patient = Patient::model()->findByPk($_GET['patient_id']);
                    } else {
                        $patient = Yii::app()->getController()->patient;
                    }

                    $with = array(
                        'firmLetterStrings' => array(
                            'on' => 'firm_id is null or firm_id = :firm_id',
                            'params' => array(
                                ':firm_id' => $firm->id,
                            ),
                            'order' => 'firmLetterStrings.display_order asc',
                        ),
                        'subspecialtyLetterStrings' => array(
                            'on' => 'subspecialty_id is null',
                            'order' => 'subspecialtyLetterStrings.display_order asc',
                        ),
                        'siteLetterStrings' => array(
                            'on' => 'site_id is null or site_id = :site_id',
                            'params' => array(
                                ':site_id' => Yii::app()->session['selected_site_id'],
                            ),
                            'order' => 'siteLetterStrings.display_order',
                        ),
                    );
                    if ($firm->getSubspecialtyID()) {
                        $with['subspecialtyLetterStrings']['on'] = 'subspecialty_id is null or subspecialty_id = :subspecialty_id';
                        $with['subspecialtyLetterStrings']['params'] = array(':subspecialty_id' => $firm->getSubspecialtyID());
                    }
                    foreach (LetterStringGroup::model()->with($with)->findAll(array('order' => 't.display_order')) as $string_group) {
                        $strings = $string_group->getStrings($patient, $event_types);
                        ?>
                        <?php echo $form->dropDownListNoPost(strtolower($string_group->name), $strings, '', array(
                            'empty' => '- ' . $string_group->name . ' -',
                            'nowrapper' => true,
                            'class' => 'stringgroup full-width cols-full',
                            'disabled' => empty($strings),
                        )) ?>
                    <?php } ?>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="cols-9">
        <div class="cols-full">
          <div id="docman_block" class="cols-12">
              <?php
              if (!$creating) {
                  $document_set = DocumentSet::model()->findByAttributes(array('event_id' => $element->event_id));

                  if ($document_set) {
                      $this->renderPartial('//docman/_update', array(
                          'row_index' => (isset($row_index) ? $row_index : 0),
                          'document_set' => $document_set,
                          'macro_id' => $macro_id,
                          'element' => $element,
                          'can_send_electronically' => true,
                      ));
                  }
              } else {
                  $macro_data = array();
                  if (isset($element->macro) && !isset($_POST['DocumentTarget'])) {
                      $macro_data = $api->getMacroTargets($patient_id, $macro_id);
                  }
                  // set back posted data on error
                  if (isset($_POST['DocumentTarget'])) {

                      foreach ($_POST['DocumentTarget'] as $document_target) {

                          if (isset($document_target['attributes']['ToCc']) && $document_target['attributes']['ToCc'] == 'To') {
                              $macro_data['to'] = array(
                                  'contact_type' => $document_target['attributes']['contact_type'],
                                  'contact_id' => isset($document_target['attributes']['contact_id']) ? $document_target['attributes']['contact_id'] : null,
                                  'contact_name' => isset($document_target['attributes']['contact_name']) ? $document_target['attributes']['contact_name'] : null,
                                  'address' => isset($document_target['attributes']['address']) ? $document_target['attributes']['address'] : null,
                              );
                          } else {

                              if (isset($document_target['attributes']['ToCc']) && $document_target['attributes']['ToCc'] == 'Cc') {

                                  $macro_data['cc'][] = array(
                                      'contact_type' => $document_target['attributes']['contact_type'],
                                      'contact_id' => isset($document_target['attributes']['contact_id']) ? $document_target['attributes']['contact_id'] : null,
                                      'contact_name' => isset($document_target['attributes']['contact_name']) ? $document_target['attributes']['contact_name'] : null,
                                      'address' => isset($document_target['attributes']['address']) ? $document_target['attributes']['address'] : null,
                                      'is_mandatory' => false,
                                  );
                              }
                          }
                      }
                  }
                  $gp_address = isset($patient->gp->contact->correspondAddress) ? $patient->gp->contact->correspondAddress : (isset($patient->gp->contact->address) ? $patient->gp->contact->address : null);
                  if (!$gp_address) {
                      $gp_address = isset($patient->practice->contact->correspondAddress) ? $patient->practice->contact->correspondAddress : (isset($patient->practice->contact->address) ? $patient->practice->contact->address : null);
                  }

                  if (!$gp_address) {
                      $gp_address = "The contact does not have a valid address.";
                  } else {
                      $gp_address = implode("\n", $gp_address->getLetterArray());
                  }

                  $contact_string = '';
                  if ($patient->gp) {
                      $contact_string = 'Gp' . $patient->gp->id;
                  } else {
                      if ($patient->practice) {
                          $contact_string = 'Practice' . $patient->practice->id;
                      }
                  }

                  $patient_address = isset($patient->contact->correspondAddress) ? $patient->contact->correspondAddress : (isset($patient->contact->address) ? $patient->contact->address : null);

                  if (!$patient_address) {
                      $patient_address = "The contact does not have a valid address.";
                  } else {
                      $patient_address = implode("\n", $patient_address->getLetterArray());
                  }

                  $address_data = array();
                  if ($contact_string) {
                      $address_data = $api->getAddress($patient_id, $contact_string);
                  }

                  $contact_id = isset($address_data['contact_id']) ? $address_data['contact_id'] : null;
                  $contact_name = isset($address_data['contact_name']) ? $address_data['contact_name'] : null;
                  $address = isset($address_data['address']) ? $address_data['address'] : null;

                  $internal_referral = LetterType::model()->findByAttributes(['name' => 'Internal Referral']);

                  $this->renderPartial('//docman/_create', array(
                      'row_index' => (isset($row_index) ? $row_index : 0),
                      'macro_data' => $macro_data,
                      'macro_id' => $macro_id,
                      'element' => $element,
                      'can_send_electronically' => true,
                      'defaults' => array(
                          'To' => array(
                              'contact_id' => $contact_id,
                              'contact_type' => 'GP',
                              'contact_name' => $contact_name,
                              'address' => $address,
                          ),
                          'Cc' => array(
                              'contact_id' => isset($patient->contact->id) ? $patient->contact->id : null,
                              'contact_name' => isset($patient->contact->id) ? $patient->getCorrespondenceName() : null,
                              'contact_type' => 'PATIENT',
                              'address' => $patient_address,
                          ),
                      ),
                  ));
              } ?>
          </div>
        </div>
          <?php if (!$element->document_instance): ?>
            <div class="data-group">
              <div class="cols-<?php echo $layoutColumns['label']; ?> column">
                  <?php echo $form->dropDownListNoPost('address_target', $element->address_targets,
                      $element->address_target,
                      array('empty' => '- Recipient -', 'nowrapper' => true, 'class' => 'full-width')) ?>
              </div>
              <div class="cols-6 column end">
                  <?php echo $form->textArea($element, 'address',
                      array('rows' => 7, 'label' => false, 'nowrapper' => true), false, array('class' => 'address')) ?>
              </div>
            </div>
          <?php endif; ?>
        <div class="data-group">
          <table class="cols-full">
            <colgroup>
              <col>
              <col class="cols-8">
            </colgroup>
            <tbody>
            <tr>
              <!--Nickname-->
              <td style="text-align: left;">
                  <?php echo $form->checkBox($element, 'use_nickname', array('nowrapper' => true)) ?>
              </td>
            </tr>
            <tr>
              <!--                        Introduction/ Salutation-->
              <td colspan="2" class="cols-full correspondence-letter-text">
                  <?php echo $form->textArea($element, 'introduction',
                      array('rows' => 2, 'label' => false, 'nowrapper' => true), false, array('class' => 'address')) ?>
              </td>
            </tr>
            <tr>
              <!--                        Subject-->
              <td colspan="2">
                <div
                    class="cols-<?php echo $layoutColumns['field']; ?> column large-offset-<?php echo $layoutColumns['label']; ?> end">
                    <?php echo $form->textArea(
                        $element,
                        're',
                        array('rows' => 2, 'label' => false, 'nowrapper' => true),
                        empty($_POST) ? strlen($element->re) == 0 : strlen(@$_POST['ElementLetter']['re']) == 0,
                        array('class' => 'address')
                    ) ?>
              </td>
            </tr>
            <tr>
              <td colspan="2">
                  <?php echo $form->textArea($element, 'body',
                      array('rows' => 20, 'label' => false, 'nowrapper' => true),
                      false, array('class' => 'address')) ?>
              </td>
            </tr>
            <tr>
              <td>
                From
              </td>
              <td>
                  <?php
                  $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                      'id' => 'OphCoCorrespondence_footerAutoComplete',
                      'name' => 'OphCoCorrespondence_footerAutoComplete',
                      'value' => '',
                      'sourceUrl' => array('default/users/correspondence-footer/true'),
                      'options' => array(
                          'minLength' => '3',
                          'select' => "js:function(event, ui) {
									$('#ElementLetter_footer').val(ui.item.correspondence_footer_text);
									$('#OphCoCorrespondence_footerAutoComplete').val('');
									return false;
								}",
                      ),
                      'htmlOptions' => array(
                          'placeholder' => 'type to search for users',
                          'class' => 'cols-full search',
                      ),
                  ));
                  ?>
                  <?php echo $form->textArea($element, 'footer',
                      array('rows' => 9, 'label' => false, 'nowrapper' => true), false, array('class' => 'address')) ?>
              </td>
            </tr>
            <tr>
              <td>
                Enclosures
              </td>
              <td>
                  <?php if (!$element->document_instance): ?>
                    <div class="data-group">
                      <div class="cols-<?php echo $layoutColumns['label']; ?> column">
                          <?php echo $form->dropDownListNoPost('cc', $element->address_targets, '',
                              array('empty' => '- Cc -', 'nowrapper' => true)) ?>
                      </div>
                      <div class="cols-<?php echo $layoutColumns['field']; ?> column end">
                          <?php echo $form->textArea($element, 'cc',
                              array('rows' => 8, 'label' => false, 'nowrapper' => true), false,
                              array('class' => 'address')) ?>
                      </div>
                      <div id="cc_targets">
                          <?php foreach ($element->cc_targets as $cc_target) {
                              ?>
                            <input type="hidden" name="CC_Targets[]" value="<?php echo $cc_target ?>"/>
                              <?php
                          } ?>
                      </div>
                    </div>
                  <?php endif; ?>

                  <?php if (is_array(@$_POST['EnclosureItems'])) { ?>
                      <?php foreach ($_POST['EnclosureItems'] as $key => $value) { ?>
                      <div class="enclosureItem"><?=\CHtml::textField("EnclosureItems[$key]", $value,
                              array('autocomplete' => Yii::app()->params['html_autocomplete'], 'size' => 60)) ?><a
                            href="#" class="removeEnclosure">Remove</a></div>
                      <?php } ?>
                  <?php } else { ?>
                      <?php foreach ($element->enclosures as $i => $item) { ?>
                      <div class="enclosureItem"><?=\CHtml::textField("EnclosureItems[enclosure$i]",
                              $item->content,
                              array('autocomplete' => Yii::app()->params['html_autocomplete'], 'size' => 60)) ?><a
                            href="#" class="removeEnclosure">Remove</a></div>
                      <?php } ?>
                  <?php } ?>

                <input type="hidden" name="update_enclosures" value="1"/>
                <div id="enclosureItems"
                     class="<?php echo !is_array(@$_POST['EnclosureItems']) && empty($element->enclosures) ? ' hide' : ''; ?>">
                    <?php if (is_array(@$_POST['EnclosureItems'])) { ?>
                        <?php foreach ($_POST['EnclosureItems'] as $key => $value) { ?>
                        <div class=" row collapse in enclosureItem">
                          <div class="cols-8 column">
                              <?=\CHtml::textField("EnclosureItems[$key]", $value,
                                  array('autocomplete' => Yii::app()->params['html_autocomplete'])) ?>
                          </div>
                          <div class="cols-4 column end">
                            <div class="postfix align"><a href="#" class="field-info removeEnclosure">Remove</a></div>
                          </div>
                        </div>
                        <?php } ?>
                    <?php } else { ?>
                        <?php foreach ($element->enclosures as $i => $item) { ?>
                        <div class="data-group collapse in enclosureItem">
                          <div class="cols-8 column">
                              <?=\CHtml::textField("EnclosureItems[enclosure$i]", $item->content,
                                  array('autocomplete' => Yii::app()->params['html_autocomplete'])) ?>
                          </div>
                          <div class="cols-4 column end">
                            <div class="postfix align"><a href="#" class="field-info removeEnclosure">Remove</a></div>
                          </div>
                        </div>
                        <?php } ?>
                    <?php } ?>
                </div>
                <button class="addEnclosure secondary small" type="button">
                  Add
                </button>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
    <?php } ?>
</div>
</section> <!--this closing tag closes a <section> tag that was opened in a different file. To be fixed later on. -->
<section class="element edit full edit-xxx">
<div id="attachments_content_container">
    <?php
    $associated_content = EventAssociatedContent::model()
        ->with('initAssociatedContent')
        ->findAllByAttributes(
            array('parent_event_id' => $element->event_id),
            array('order' => 't.display_order asc')
        );

    $api = Yii::app()->moduleAPI->get('OphCoCorrespondence');
    if ($associated_content == null) {
        $associated_content = MacroInitAssociatedContent::model()->findAllByAttributes(array('macro_id' => $macro_id),
            array('order' => 'display_order asc'));
    }

    $this->renderPartial('event_associated_content', array(
        'associated_content' => $associated_content,
        'api' => Yii::app()->moduleAPI->get('OphCoCorrespondence'),
    ));
    ?>
</div>
<script>
  var element_letter_controller;
  $(document).ready(function(){
    element_letter_controller =
      new OpenEyes.OphCoCorrespondence.LetterMacroController(
        "ElementLetter_body",
        <?= CJSON::encode(\Yii::app()->params['tinymce_default_options'])?>
        );
  });
</script>

