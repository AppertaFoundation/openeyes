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

/**
 * @var $element ElementLetter
 */
?>

<?php echo $form->hiddenInput($element, 'draft', 1) ?>
<?php
Yii::app()->clientScript->registerScriptFile("{$this->assetPath}/js/OpenEyes.OphCoCorrespondence.LetterMacro.js", CClientScript::POS_HEAD);
$api = Yii::app()->moduleAPI->get('OphCoCorrespondence');
$layoutColumns = $form->layoutColumns;
$macro_id = $_POST['macro_id'] ?? $element->macro->id ?? null;

if (!$macro_id) {
    $macro_id = $element->document_instance[0]->document_instance_data[0]->macro_id ?? null;
}

$macro_letter_type_id = null;
if ($macro_id) {
    $macro = LetterMacro::model()->findByPk($macro_id);
    $macro_letter_type_id = $macro->letter_type_id;
}

$element->letter_type_id = ($element->letter_type_id ?: $macro_letter_type_id);
$patient_id = Yii::app()->request->getQuery('patient_id', null);
$patient = Patient::model()->findByPk($patient_id);
$creating = $creating ?? false;
?>
<?php if ($creating === false) : ?>
    <input type="hidden" id="re_default"
           value="<?= $element->calculateRe() ?>"/>
<?php endif; ?>
<div class="element-fields full-width flex-layout flex-top col-gap">
    <div class="cols-3">
        <div class="data-group">
          <table class="cols-full">
            <colgroup>
              <col class="cols-3">
              <col class="cols-7">
            </colgroup>
            <tbody>
            <?php
            $correspondeceApp = Yii::app()->params['ask_correspondence_approval'];
            if ($correspondeceApp === 'on') { ?>
            <tr>
              <td>
                  <?php echo $element->getAttributeLabel('is_signed_off') ?>:
              </td>
              <td>
                  <?php echo $form->radioButtons(
                      $element,
                      'is_signed_off',
                      array(
                        1 => 'Yes',
                        0 => 'No',
                      ),
                      $element->is_signed_off,
                      false,
                      false,
                      false,
                      false,
                      array('nowrapper' => true)
                  ); ?>
              </td>
            </tr>
            <?php } ?>
            </tbody>
          </table>
        </div>
            <?php if ($element->isInternalReferralEnabled()) : ?>
              <div class="internal-referrer-wrapper <?php echo $element->isInternalreferral() ? '' : 'hidden'; ?> ">
                  <?php $this->renderPartial('_internal_referral', array('element' => $element)); ?>
              </div>
            <?php endif; ?>
        <div class="data-group">
          <table class="cols-full">
                        <colgroup>
                            <col class="cols-3">
                            <col class="cols-7">
                        </colgroup>
            <tbody>
            <tr>
              <td>
                Site
              </td>
              <td>
                    <?php echo $form->dropDownList(
                        $element,
                        'site_id',
                        Site::model()->getLongListForCurrentInstitution(),
                        array('empty' => 'Select', 'nowrapper' => true, 'class' => 'cols-full')
                    ) ?>
              </td>
            </tr>
            <tr>
              <td>
                Date
              </td>
              <td>
                    <?php echo $form->datePicker(
                        $element,
                        'date',
                        array('maxDate' => 'today'),
                        array('nowrapper' => true, 'class' => 'cols-7')
                    ) ?>
              </td>
            </tr>
            <tr>
              <td>
                Letter type
              </td>
              <td>
                    <?php echo $form->dropDownList(
                        $element,
                        'letter_type_id',
                        CHtml::listData(LetterType::model()->getActiveLetterTypes(), 'id', 'name'),
                        array('empty' => 'Select', 'nowrapper' => true, 'class' => 'full-width cols-full')
                    ) ?>
              </td>
            </tr>
            <!--                  Clinic Date  -->
            <tr>
              <td>
                Clinic Date
              </td>
              <td>
                    <?php echo $form->datePicker(
                        $element,
                        'clinic_date',
                        array('maxDate' => 'today'),
                        array('nowrapper' => true, 'null' => true, 'class' => 'cols-7')
                    ) ?>
              </td>
            </tr>
            <!--                    Direct Line-->
            <tr>
              <td>
                Direct Line
              </td>
              <td>
                    <?php echo $form->textField(
                        $element,
                        'direct_line',
                        array('nowrapper' => true, 'class' => 'cols-full'),
                        array(),
                        array_merge($layoutColumns, array('field' => 2))
                    ) ?>
              </td>
            </tr>
            <!--                    Fax-->
            <tr>
              <td>
                Fax
              </td>
              <td>
                    <?php echo $form->textField(
                        $element,
                        'fax',
                        array('nowrapper' => true, 'class' => 'cols-full'),
                        array(),
                        array_merge($layoutColumns, array('field' => 2))
                    ) ?>
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
                          'row_index' => ($row_index ?? 0),
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
                        $i = 0;
                        foreach ($_POST['DocumentTarget'] as $document_target) {
                            if (isset($document_target['attributes']['ToCc'])) {
                                if ($document_target['attributes']['ToCc'] === 'To') {
                                    $macro_data['to'] = array(
                                        'contact_type' => $document_target['attributes']['contact_type'],
                                        'contact_id' => $document_target['attributes']['contact_id'] ?? null,
                                        'contact_name' => $document_target['attributes']['contact_name'] ?? null,
                                        'address' => $document_target['attributes']['address'] ?? null,
                                        'email' => $document_target['attributes']['email'] ?? null,
                                    );
                                } elseif ($document_target['attributes']['ToCc'] === 'Cc') {
                                    $macro_data['cc'][] = array(
                                        'contact_type' => $document_target['attributes']['contact_type'],
                                        'contact_id' => $document_target['attributes']['contact_id'] ?? null,
                                        'contact_name' => $document_target['attributes']['contact_name'] ?? null,
                                        'address' => $document_target['attributes']['address'] ?? null,
                                        'email' => $document_target['attributes']['email'] ?? null,
                                        'is_mandatory' => false,
                                    );
                                }
                            }
                        }
                    }
                    /**
                     * @var $gp_address Address|null
                     */
                    $gp_address = $patient->gp->contact->correspondAddress ?? $patient->gp->contact->address ?? null;
                    if (!$gp_address) {
                        $gp_address = $patient->practice->contact->correspondAddress ?? $patient->practice->contact->address ?? null;
                    }

                    if (!$gp_address) {
                        $gp_address = 'The contact does not have a valid address.';
                    } else {
                        $gp_address = implode("\n", $gp_address->getLetterArray());
                    }

                    $contact_string = '';
                    if ($patient->gp) {
                        $contact_string = 'Gp' . $patient->gp->id;
                    } elseif ($patient->practice) {
                        $contact_string = 'Practice' . $patient->practice->id;
                    }

                    /**
                     * @var $patient_address Address|null
                     */
                    $patient_address = $patient->contact->correspondAddress ?? $patient->contact->address ?? null;

                    if (!$patient_address) {
                        $patient_address = 'The contact does not have a valid address.';
                    } else {
                        $patient_address = implode("\n", $patient_address->getLetterArray());
                    }

                    $address_data = array();
                    if ($contact_string) {
                        $address_data = $api->getAddress($patient_id, $contact_string);
                    }

                    $contact_id = $address_data['contact_id'] ?? null;
                    $contact_name = $address_data['contact_name'] ?? null;
                    $contact_nickname = $address_data['contact_nickname'] ?? null;
                    $address = $address_data['address'] ?? null;

                    $internal_referral = LetterType::model()->findByAttributes(['name' => 'Internal Referral']);

                    $this->renderPartial('//docman/_create', array(
                      'row_index' => ($row_index ?? 0),
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
                              'contact_nickname' => $contact_nickname,
                          ),
                          'Cc' => array(
                              'contact_id' => $patient->contact->id ?? null,
                              'contact_name' => isset($patient->contact->id) ? $patient->getCorrespondenceName() : null,
                              'contact_type' => 'PATIENT',
                              'address' => $patient_address,
                          ),
                      ),
                    ));
                } ?>
          </div>
        </div>
    </div>
</div>

<hr class="divider">

<div class="flex-layout flex-top col-gap">
    <div class="cols-3">
        <div class="data-group">
            <table class="  cols-full last-left">
                <tbody>
                <tr>
                    <td>
                        <h3>Letter Templates</h3>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?= CHtml::dropDownList(
                            'macro_id',
                            $macro_id,
                            $element->letter_macros,
                            array('empty' => 'Select', 'nowrapper' => true, 'class' => 'cols-full')
                        ) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h3>Insert Quick Text</h3>
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
                                'letterStrings',
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
                                    'order' => 'letterStrings.display_order',
                                ),
                                'institutionLetterStrings' => array(
                                    'on' => 'institutionLetterStrings.institution_id is null or institutionLetterStrings.institution_id = :institution_id',
                                    'params' => array(
                                        ':institution_id' => Yii::app()->session['selected_institution_id'],
                                    ),
                                    'order' => 'letterStrings.display_order',
                                ),
                            );
                            if ($firm->getSubspecialtyID()) {
                                $with['subspecialtyLetterStrings']['on'] = 'subspecialty_id is null or subspecialty_id = :subspecialty_id';
                                $with['subspecialtyLetterStrings']['params'] = array(':subspecialty_id' => $firm->getSubspecialtyID());
                            }

                            $criteria = new CDbCriteria();
                            $criteria->addCondition('t.institution_id = :institution_id');
                            $criteria->params[':institution_id'] = Yii::app()->session['selected_institution_id'];
                            $criteria->order = 't.display_order asc';
                            foreach (LetterStringGroup::model()->with($with)->findAll($criteria) as $string_group) {
                                $strings = $string_group->getStrings($patient, $event_types);
                                ?>
                                <?php echo $form->dropDownListNoPost(strtolower($string_group->name), $strings, '', array(
                                    'empty' => '- ' . $string_group->name . ' -',
                                    'nowrapper' => true,
                                    'class' => 'stringgroup full-width cols-full',
                                    'disabled' => empty($strings),
                                )) ?>
                            <?php } ?>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="cols-9">
        <table class="cols-full">
            <colgroup>
                <col class="cols-9">
                <col>
            </colgroup>
            <tbody>
            <tr>
                <!--                        Introduction/ Salutation-->
                <td>
                    <?php echo $form->textArea(
                        $element,
                        'introduction',
                        array('rows' => 1, 'label' => false, 'nowrapper' => true),
                        false,
                        array('class' => 'address correspondence-letter-text')
                    ) ?>
                </td>
                <!--Nickname-->
                <td>
                    <?php echo $form->checkBox($element, 'use_nickname', array('nowrapper' => true)) ?>
                </td>
            </tr>
            <tr>
                <!--                        Subject-->
                <td>
                    <input type="hidden" id="default_re" value="<?= $element->calculateRe($patient) ?>">
                        <?php echo $form->textArea(
                            $element,
                            're',
                            array('rows' => 1, 'label' => false, 'nowrapper' => true),
                            empty($_POST) ? strlen($element->re) === 0 : strlen(@$_POST['ElementLetter']['re']) === 0,
                            array('class' => 'autosize')
                        ) ?>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <?php echo $form->textArea(
                        $element,
                        'body',
                        array('rows' => 20, 'label' => false, 'nowrapper' => true),
                        false,
                        array('class' => 'address')
                    ) ?>
                </td>
            </tr>
        </table>
        <table class="cols-full">
          <colgroup>
            <col class="cols-2">
            <col>
          </colgroup>
          <tbody>
            <?php if (strlen($element->footer) > 0) : ?>
            <tr>
              <td>From</td>
              <td>
                <?php echo $form->textArea(
                    $element,
                    'footer',
                    array('label' => false, 'nowrapper' => true),
                    false,
                    array(
                        'readonly' => true,
                        'class' => 'correspondence-letter-text autosize',
                        'style' => 'overflow: hidden; overflow-wrap: break-word; height: 54px;'
                    )
                ) ?>
              </td>
            </tr>
            <?php else : ?>
                <?= $form->hiddenField($element, "footer") ?>
            <?php endif; ?>
            <tr>
                <td>
                    Enclosures
                </td>
                <td>

                    <input type="hidden" name="update_enclosures" value="1"/>
                    <div id="enclosureItems"
                         class="<?php echo !is_array(@$_POST['EnclosureItems']) && empty($element->enclosures) ? ' hide' : ''; ?>">
                        <?php if (is_array(@$_POST['EnclosureItems'])) { ?>
                            <?php foreach ($_POST['EnclosureItems'] as $key => $value) { ?>
                                <div class="data-group collapse in enclosureItem flex-layout">
                                    <?= CHtml::textField(
                                        "EnclosureItems[$key]",
                                        $value,
                                        array('autocomplete' => Yii::app()->params['html_autocomplete'], 'class' => 'cols-full')
                                    ) ?>
                                    <i class="oe-i trash removeEnclosure"></i>
                                </div>

                            <?php } ?>
                        <?php } else { ?>
                            <?php foreach ($element->enclosures as $i => $item) { ?>
                                <div class="data-group collapse in enclosureItem flex-layout">
                                    <?= CHtml::textField(
                                        "EnclosureItems[enclosure$i]",
                                        $item->content,
                                        array('autocomplete' => Yii::app()->params['html_autocomplete'], 'class' => 'cols-full')
                                    ) ?>
                                    <i class="oe-i trash removeEnclosure"></i>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                    <div class="add-data-actions">
                        <button class="addEnclosure secondary small" type="button">Add</button>
                    </div>
                </td>
            </tr>
          </tbody>
        </table>
    </div>
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
        if ($associated_content === null) {
            $associated_content = MacroInitAssociatedContent::model()->findAllByAttributes(
                array('macro_id' => $macro_id),
                array('order' => 'display_order asc')
            );
        }

        $this->renderPartial('event_associated_content', array(
            'associated_content' => $associated_content,
            'api' => Yii::app()->moduleAPI->get('OphCoCorrespondence'),
        ));
        ?>
    </div>
    <script>
        let element_letter_controller;
        $(document).ready(function () {
            element_letter_controller =
                new OpenEyes.OphCoCorrespondence.LetterMacroController(
                    "ElementLetter_body",
                    <?= CJSON::encode(Yii::app()->params['tinymce_default_options'])?>
                );

            OpenEyes.UI.AutoCompleteSearch.init({
                input: $('#oe-autocompletesearch'),
                url: baseUrl + '/'+moduleName+'/default/users/correspondence-footer/true',
                onSelect: function () {
                    let AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
                    $('#ElementLetter_footer').val(AutoCompleteResponse.correspondence_footer_text);
                }
            });
            $('#ElementLetter_use_nickname').on('click', function () {
                let nickname_check = $(this).is(':checked');
                let addressee = $('#DocumentTarget_0_attributes_contact_name').val();
                let recipient_to = addressee.split(' ');
                let nickname;

                if (recipient_to.length > 1) {
                    if (recipient_to.length === 2) {
                        addressee = recipient_to[0] + " " + recipient_to[1];
                    } else {
                        addressee = recipient_to[0] + ' ' + (recipient_to[2] !== undefined ? recipient_to[2] : recipient_to[1]);
                    }

                    if (nickname_check) {
                        nickname = $('#DocumentTarget_0_attributes_contact_nickname').val();
                        if (nickname.length > 0) {
                            addressee = nickname;
                        }
                    }
                }

                $('#ElementLetter_introduction').val('Dear ' + addressee + ',');

            });
        });
    </script>