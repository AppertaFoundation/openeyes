<?php

/**
 * (C) OpenEyes Foundation, 2018
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

$logo_helper = new LogoHelper();
$default_urls = $logo_helper->getLogoURLs();
?>
<style>
    .flash-success {
        border: 1px solid #1DDD50;
        background: #C3FFD3;
        text-align: center;
        padding: 7px 15px;
        color: #000000;
        margin-bottom: 20px;
    }

    .error {
        border: 1px solid #ff6666;
        background: #ffe6e6;
        text-align: center;
        padding: 7px 15px;
        color: #000000;
        margin-bottom: 20px;
    }

    .remove-logo {
        display: block;
        position: absolute;
        top: 1px;
        right: 2px;
        padding: 11px 11px;
        background-color: rgba(255, 255, 255, .5);
    }
</style>
<?php if (Yii::app()->user->hasFlash('success')) : ?>
    <div class="flash-success">
        <?= Yii::app()->user->getFlash('success'); ?>
    </div>

<?php endif; ?>
<?php if (Yii::app()->user->hasFlash('error')) : ?>
    <div class="error">
        <?= Yii::app()->user->getFlash('error'); ?>
    </div>

<?php endif; ?>

<?php $form = $this->beginWidget('BaseEventTypeCActiveForm', [
    'id' => 'adminform',
    'enableAjaxValidation' => false,
    'focus' => '#username',
    'layoutColumns' => [
        'label' => 2,
        'field' => 5,
    ],
    'method' => "POST",
    'htmlOptions' => array('enctype' => 'multipart/form-data')
]); ?>
<div class="cols-10">
    <?= $this->renderPartial('_form_errors', array('errors' => $errors)) ?>
    <?php
    $sites_headers = [
        'name' => 'Site',
        'short_name' =>  'Site',
        'remote_id' =>  'Site',
        'email' => 'Contact',
        'address1' => 'Address',
        'address2' => 'Address',
        'city' => 'Address',
        'county' => 'Address',
        'postcode' => 'Address',
        'telephone' => 'Site',
        'fax' => 'Site',
        'country_id' => 'Address'
    ];

    $necessity_options_for_json = [];
    foreach ($necessity_options_with_labels as $option => $label) {
        $necessity_options_for_json[] = [
            'option' => $option,
            'label' => $label,
        ];
    }
    $app = Yii::app();
    $request = $app->request
    ?>
    <div class="row divider">
        <h2>
            <?php
            if ($new) {
                echo "Add institution";
            } else {
                echo "Edit institution";
            }
            ?>
        </h2>
    </div>

    <table class="standard cols-full">
        <colgroup>
            <col class="cols-3">
            <col class="cols-5">
        </colgroup>

        <tbody>
            <?php foreach (['name', 'short_name'] as $field) : ?>
                <tr>
                    <td><?= $institution->getAttributeLabel($field); ?></td>
                    <td>
                        <?= \CHtml::activeTextField(
                            $institution,
                            $field,
                            [
                                'class' => 'cols-full',
                                'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
                            ]
                        ); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td><?= $institution->getAttributeLabel('remote_id'); ?></td>
                <td>
                    <?php if (!$new) { ?>
                        <?= htmlspecialchars($institution->remote_id) ?>
                    <?php } else { ?>
                        <div class="alert-box alert">Once added, this field will not be editable</div>
                        <?= CHtml::activeTextField(
                            $institution,
                            'remote_id',
                            [
                                'class' => 'cols-full',
                                'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
                            ]
                        );
                    } ?>
                </td>
            </tr>
            <tr>
                <td><?= $institution->getAttributeLabel('pas_key'); ?></td>
                <td>
                    <?= CHtml::activeTextField(
                        $institution,
                        'pas_key',
                        [
                            'class' => 'cols-full',
                            'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete')
                        ]
                    ) ?>
                </td>
            </tr>
            <?php
            $contact_fields = ['title', 'first_name', 'last_name', 'nick_name', 'email', 'primary_phone'];
            foreach ($contact_fields as $field) : ?>
                <tr>
                    <td><?= "Institution contact " . $contact->getAttributeLabel($field); ?></td>
                    <td>
                        <?= CHtml::activeTextField(
                            $contact,
                            $field,
                            [
                                'class' => 'cols-full',
                                'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
                            ]
                        ) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php
            $address_fields = ['address1', 'address2', 'city', 'county', 'postcode'];
            foreach ($address_fields as $field) : ?>
                <tr>
                    <td><?= $address->getAttributeLabel($field); ?></td>
                    <td>
                        <?= \CHtml::activeTextField(
                            $address,
                            $field,
                            [
                                'class' => 'cols-full',
                                'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
                            ]
                        ); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td>Country</td>
                <td>
                    <?= \CHtml::activeDropDownList(
                        $address,
                        'country_id',
                        CHtml::listData(Country::model()->findAll(), 'id', 'name'),
                        [
                            'class' => 'cols-full',
                            'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
                        ]
                    ); ?>
                </td>
            </tr>
            <tr>
                <td>Primary logo</td>
                <td>
                    <?php
                    echo $form->fileField($logo, 'primary_logo');
                    if (empty($default_urls['primaryLogo']) && !($logo->primary_logo)) {
                        echo "<div class='alert-box info'>No uploaded primary logo and no default logo</div>";
                    } else {
                        if (!($logo) || !($logo->primary_logo)) {
                            echo "<div class='alert-box info'>Currently using system default logo</div>";
                            echo "<img src='" . $default_urls['primaryLogo'] . "' style='width:100%;'>";
                        } elseif (!$new) {
                            echo '<div style=" margin-top: 5px; position: relative; ">';
                            echo "<img src='" . $logo->getImageUrl() . "' style='width:100%;'>";
                            echo '<br>' . CHtml::button(
                                '',
                                array(
                                    'submit' => array('admin/deletelogo/'),
                                    'params' => array(
                                        'institution_id' => $institution->id,
                                        'deletePrimaryLogo' => true,
                                    ),
                                    'csrf' => true,
                                    'class' => 'remove-logo oe-i remove-circle small',
                                    'confirm' => 'Are you sure you want to delete the primary logo? You will lose all unsaved edits you have made to this institution.',
                                    'data-method' => "POST"
                                )
                            );
                            echo '</div>';
                        }
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td>Secondary logo</td>
                <td>
                    <?php
                    echo $form->fileField($logo, 'secondary_logo');
                    if (empty($default_urls['secondaryLogo']) && !($logo->secondary_logo)) {
                        echo "<div class='alert-box info'>No uploaded secondary logo and no default logo</div>";
                    } else {
                        if (!($logo) || !($logo->secondary_logo)) {
                            echo "<div class='alert-box info'>Currently using system default logo</div>";
                            echo "<img src='" . $default_urls['secondaryLogo'] . "' style='width:100%;'>";
                        } elseif (!$new) {
                            echo '<div style="
                        margin-top: 5px;
                        position: relative;
                    ">';
                            echo "<img src='" . $logo->getImageUrl(true) . "' style='width:100%;'>";
                            echo '<br>' . CHtml::button(
                                '',
                                array(
                                    'submit' => array('admin/deletelogo/'),
                                    'params' => array(
                                        'institution_id' => $institution->id,
                                        'deleteSecondaryLogo' => true,
                                    ),
                                    'csrf' => true,
                                    'class' => 'remove-logo oe-i remove-circle small',
                                    'confirm' => 'Are you sure you want to delete the secondary logo? You will lose all unsaved edits you have made to this institution.',
                                    'data-method' => "POST"
                                )
                            );
                            echo '</div>';
                        }
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td><?= $institution->getAttributeLabel('any_number_search_allowed'); ?></td>
                <td><?= \CHtml::activeCheckBox($institution, 'any_number_search_allowed'); ?></td>
            </tr>
            <tr>
                <td><?= $institution->getAttributeLabel('first_used_site_id'); ?></td>
                <td><?= \CHtml::activeDropDownList(
                    $institution,
                    'first_used_site_id',
                    CHtml::listData(Site::model()->findAllByAttributes(['institution_id' => $institution->id]), 'id', 'name'),
                    [
                            'class' => 'cols-full',
                            'empty' => '- None -'
                        ]
                ); ?></td>
            </tr>
        </tbody>
    </table>

    <br>

    <h2>Sites</h2>
    <hr class="divider">
    <table id="admin_institution_sites" class="standard">
        <thead>
            <tr>
                <th>ID</th>
                <th>Remote ID</th>
                <th>Name</th>
                <th>Address</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($institution->sites as $site) { ?>
                <tr class="clickable" data-id="<?= $site->id ?>" data-uri="admin/editsite?site_id=<?= $site->id ?>">
                    <td><?= $site->id ?></td>
                    <td><?= $site->remote_id ?>&nbsp;</td>
                    <td><?= $site->name ?>&nbsp;</td>
                    <td>
                        <?= $site->getLetterAddress(
                            array('delimiter' => ', ')
                        ) ?>&nbsp
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <div class="flex-layout flex-left">
        <?= \CHtml::button(
            'Add Site',
            [
                'class' => 'button large',
                'id' => 'add-institution-sites-btn'
            ]
        ); ?>
    </div>
</div>
<div class="cols-10">
    <table class="standard" id="institution-sites-table" style="display: none">
        <thead>
            <tr>
                <?php foreach ($sites_headers as $field => $model_name) { ?>
                    <th><?= $model_name::model()->getAttributeLabel($field) ?></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php
            if (isset($invalid_entries['Site'])) {
                $row_key = 0;
                foreach ($invalid_entries['Site'] as $key => $entry) {
                    $this->renderPartial('/admin/sites/_site_row', [
                        'key' => $row_key,
                        'site' => $entry,
                        'address' => $invalid_entries['SiteAddress'][$key]
                    ]);
                    $row_key++;
                }
            } ?>
        </tbody>
    </table>
</div>
<div class="cols-10">
    <br>
    <h2>Authentication Methods</h2>
    <hr class="divider">
    <?php
    $institution_authentication_fields = ['id', 'site_id', 'user_authentication_method', 'description', 'ldap_config_id', 'active'];
    $institution_authentications = InstitutionAuthentication::model()->findAllByAttributes(['institution_id' => $institution->id]);
    ?>
    <table class="standard" id="institution-authentications">
        <thead>
            <tr>
                <?php foreach ($institution_authentication_fields as $field) { ?>
                    <th><?= InstitutionAuthentication::model()->getAttributeLabel($field) ?></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($institution_authentications as $key => $institution_authentication) { ?>
                <tr class="clickable" data-id="<?= $institution_authentication->id ?>" data-uri="admin/editinstitutionauthentication?institution_authentication_id=<?= $institution_authentication->id ?>" data-key="<?= $key ?>">
                    <td><?= $institution_authentication->id ?></td>
                    <td><?= $institution_authentication->site ? $institution_authentication->site->name : '-' ?></td>
                    <td><?= $institution_authentication->userAuthenticationMethod->code ?></td>
                    <td><?= $institution_authentication->description ?></td>
                    <td><?= $institution_authentication->ldap_config_id ? $institution_authentication->LDAPConfig->description : '-' ?></td>
                    <td><i class="oe-i <?= $institution_authentication->active ? 'tick' : 'remove' ?> small"></i></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <div class="flex-layout flex-left">
        <?= \CHtml::button(
            'Add Authentication Method',
            [
                'class' => 'button large',
                'id' => 'et_add',
                'data-uri' => "/admin/editinstitutionauthentication?institution_id=$institution->id",
                'disabled' => $new ? 'disabled' : ''
            ]
        ); ?>
    </div>
    <br>
</div>
<div class="cols-10">
    <br>

    <h2>Patient Identifier Numbering Systems</h2>
    <hr class="divider">
    <?php $patient_identifier_type_fields = [
        'id', 'site_id', 'usage_type', 'short_title', 'long_title',
        'validate_regex', 'value_display_prefix', 'value_display_suffix', 'pad', 'spacing_rule'
    ] ?>
    <table class="standard" id="institution-patient-identifiers">
        <thead>
            <tr>
                <?php foreach ($patient_identifier_type_fields as $field) { ?>
                    <th><?= PatientIdentifierType::model()->getAttributeLabel($field) ?></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($patient_identifier_types as $key => $patient_identifier_type) { ?>
                <tr class="clickable" data-id="<?= $patient_identifier_type->id ?>" data-uri="Admin/PatientIdentifierType/edit?patient_identifier_type_id=<?= $patient_identifier_type->id ?>" data-key="<?= $key ?>">
                    <td><?= $patient_identifier_type->id ?></td>
                    <td><?= $patient_identifier_type->site ? $patient_identifier_type->site->name : '-' ?></td>
                    <td><?= $patient_identifier_type->usage_type ?></td>
                    <td><?= $patient_identifier_type->short_title ?></td>
                    <td><?= $patient_identifier_type->long_title ?></td>
                    <td><?= $patient_identifier_type->validate_regex ? $patient_identifier_type->validate_regex : '-' ?></td>
                    <td><?= $patient_identifier_type->value_display_prefix ? $patient_identifier_type->value_display_prefix : '-' ?></td>
                    <td><?= $patient_identifier_type->value_display_suffix ? $patient_identifier_type->value_display_suffix : '-' ?></td>
                    <td><?= $patient_identifier_type->pad ? $patient_identifier_type->pad : '-' ?></td>
                    <td><?= $patient_identifier_type->spacing_rule ? $patient_identifier_type->spacing_rule : '-' ?></td>
                </tr>
            <?php } ?>
            <?php
            if (isset($invalid_entries['PatientIdentifierType'])) {
                $row_key = isset($key) ? $key + 1 : 0;
                foreach ($invalid_entries['PatientIdentifierType'] as $entry) {
                    $this->renderPartial('application.modules.Admin.views.patientIdentifierType._identifier_type_row', [
                        'key' => $row_key,
                        'element' => $entry,
                        'sites' => $institution->sites
                    ]);
                    $row_key++;
                }
            } ?>
        </tbody>
    </table>
    <div class="flex-layout flex-left">
        <?= \CHtml::button(
            'Add Patient Identifier',
            [
                'class' => 'button large',
                'id' => 'add-patient-identifier-btn'
            ]
        ); ?>
    </div>
    <br>
</div>
<div class="cols-10">
    <br>

    <?php $patient_identifier_usage_type = $request->getParam('patient_identifier_usage_type') ?: $app->params['display_primary_number_usage_code'];
    $patient_identifier_site = $request->getParam('patient_identifier_site', null);
    $criteria = new CDbCriteria();
    $criteria->condition = 'institution_id=:institution_id AND patient_identifier_type_id IN (SELECT id FROM patient_identifier_type WHERE usage_type=:usage_type)';
    $criteria->params = [':institution_id' => $institution->id, 'usage_type' => $patient_identifier_usage_type];
    $criteria->order = 'display_order';
    if ($patient_identifier_site && !empty($patient_identifier_site)) {
        $criteria->addCondition('site_id=:site_id');
        $criteria->params[':site_id'] = $patient_identifier_site;
    } else {
        $criteria->addCondition('site_id IS NULL');
    }
    $identifier_rules = PatientIdentifierTypeDisplayOrder::model()->findAll($criteria);
    if ($request->isPostRequest) {
        $posted_identifier_rules = $request->getParam('PatientIdentifierTypeDisplayOrder', []);
        foreach ($posted_identifier_rules as $id_rule) {
            if (empty($id_rule['id'])) {
                $rule = new PatientIdentifierTypeDisplayOrder();
                $rule->attributes = $id_rule;
                $rule->institution = $institution;
                $identifier_rules[] = $rule;
            }
        }
    }
    $identifier_types = PatientIdentifierType::model()->findAll('usage_type=:usage_type', [':usage_type' => $patient_identifier_usage_type]);
    ?>

    <h2>Patient Identifier Display Preferences</h2>
    <hr class="divider">
    
    <?= \CHtml::dropDownList(
        'patient_identifier_usage_type',
        $patient_identifier_usage_type,
        CHtml::listData(PatientIdentifierType::model()->findAll(['select' => 't.usage_type', 'distinct' => true]), 'usage_type', 'usage_type'),
        ['class' => 'cols-4']
    ); ?>
    <?= \CHtml::dropDownList(
        'patient_identifier_site',
        $patient_identifier_site,
        CHtml::listData(Site::model()->findAll('institution_id=:institution_id', [':institution_id' => $institution->id]), 'id', 'name'),
        ['empty' => 'NOT SITE SPECIFIC','class' => 'cols-4']
    ); ?>
    <table class="standard sortable" id="patient_identifiers_entry_table">
        <thead>
            <tr>
                <th>&uarr;&darr;</th>
                <th>Usage Type</th>
                <th>Long Title</th>
                <th>Short Title</th>
                <th>Institution name</th>
                <th>Site name</th>
                <th>Prefix</th>
                <th>Suffix</th>
                <th>Searchable</th>
                <th>Protocols</th>
                <th>Necessity</th>
                <th>Status Necessity</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($identifier_rules) > 0) {
                foreach ($identifier_rules as $row_count => $identifier_rule) { ?>
                    <tr data-row="<?= $row_count ?>" data-id="<?= $identifier_rule->id ?>" data-pid_type="<?= $identifier_rule->patient_identifier_type_id ?>">
                        <td>
                            <?php
                            echo \CHtml::activeHiddenField($identifier_rule, "[{$row_count}]id");
                            echo \CHtml::activeHiddenField($identifier_rule, "[{$row_count}]patient_identifier_type_id");
                            echo \CHtml::activeHiddenField($identifier_rule, "[{$row_count}]display_order");
                            ?>
                        </td>
                        <td><?= $identifier_rule->patientIdentifierType->usage_type ?: '-' ?></td>
                        <td><?= $identifier_rule->patientIdentifierType->long_title ?: '-' ?></td>
                        <td><?= $identifier_rule->patientIdentifierType->short_title ?: '-' ?></td>
                        <td><?= $identifier_rule->institution->name ?: '-' ?></td>
                        <td><?= $identifier_rule->patientIdentifierType->site ? $identifier_rule->patientIdentifierType->site->name : '-'; ?></td>
                        <td><?= $identifier_rule->patientIdentifierType->value_display_prefix ?: '-' ?></td>
                        <td><?= $identifier_rule->patientIdentifierType->value_display_suffix ?: '-' ?></td>
                        <td><?= \CHtml::activeCheckBox($identifier_rule, "[{$row_count}]searchable") ?></td>
                        <td><?= \CHtml::activeTextField($identifier_rule, "[{$row_count}]search_protocol_prefix", [
                                'placeholder' => 'add multiple: AB|CD|EF',
                            ]) ?></td>
                        <td><?= \CHtml::activeDropDownList(
                            $identifier_rule,
                            "[{$row_count}]necessity",
                            $necessity_options_with_labels,
                            ) ?>
                        </td>
                        <td><?= \CHtml::activeDropDownList(
                            $identifier_rule,
                            "[{$row_count}]status_necessity",
                            $necessity_options_with_labels,
                            ) ?>
                        </td>
                        <td><a href="javascript:void(0)" class="js-delete_patient_identifier">Delete</a></td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr class="empty">
                    <td colspan="4">No results found.</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <div class="flex-layout flex-right">
        <div id="patient_identifier_popup" class="add-data-actions flex-item-bottom">
            <button class="button hint green" id="add-new-rule" type="button">
                <i class="oe-i plus pro-theme"></i>
            </button>
        </div>
    </div>
    <div>
        <?= \CHtml::submitButton(
            'Save',
            [
                'class' => 'button large',
                'name' => 'save',
                'id' => 'et_save'
            ]
        ); ?>
        <?= \CHtml::submitButton(
            'Cancel',
            [
                'class' => 'button large',
                'data-uri' => '/admin/institutions',
                'name' => 'cancel',
                'id' => 'et_cancel'
            ]
        ); ?>
    </div>
</div>
<script type="x-tmpl-mustache" id="add-patient-identifier-template">
    <?php

    $this->renderPartial('application.modules.Admin.views.patientIdentifierType._identifier_type_row', [
        'key' => '{{key}}',
        'element' => new PatientIdentifierType(),
        'sites' => $institution->sites
    ]);
    ?>
</script>
<script type="x-tmpl-mustache" id="add-site-template">
    <?php
    $this->renderPartial('/admin/sites/_site_row', [
        'key' => '{{key}}',
        'site' => new Site(),
        'address' => new Address()
    ]);
    ?>
</script>
<script type="text/template" id="patient-identifier-entry-template">
    <tr data-row="{{row_count}}">
        <td>
            <input type="hidden" name="PatientIdentifierTypeDisplayOrder[{{row_count}}][id]" value="">
            <input type="hidden" name="PatientIdentifierTypeDisplayOrder[{{row_count}}][patient_identifier_type_id]"
                   value="{{patient_identifier_type_id}}">
            <input type="hidden" name="PatientIdentifierTypeDisplayOrder[{{row_count}}][display_order]" value="{{display_order}}">
        </td>
        <td>{{usage_type}}</td>
        <td>{{long_title}}</td>
        <td>{{short_title}}</td>
        <td>{{institution_name}}</td>
        <td>{{site_name}}</td>
        <td>{{value_display_prefix}}</td>
        <td>{{value_display_suffix}}</td>
        <td>
            <input id="ytPatientIdentifierTypeDisplayOrder_{{row_count}}_searchable"
                   type="hidden"
                   value="0"
                   name="PatientIdentifierTypeDisplayOrder[{{row_count}}][searchable]">

            <input id="PatientIdentifierTypeDisplayOrder_{{row_count}}_searchable"
                   type="checkbox"
                   value="1"
                   name="PatientIdentifierTypeDisplayOrder[{{row_count}}][searchable]">
        </td>
        <td><input type="text"
                   value="{{protocols}}"
                   name="PatientIdentifierTypeDisplayOrder[{{row_count}}][search_protocol_prefix]"
                   placeholder="add multiple: AB|CD|EF"
            ></td>
        <td>
            <select id = "PatientIdentifierTypeDisplayOrder_{{row_count}}_necessity"
                    name = "PatientIdentifierTypeDisplayOrder[{{row_count}}][necessity]" >
                {{#necessity_options}} <option value="{{option}}" >{{label}}</option>{{/necessity_options}}
            </select>
        </td>
        <td>
            <select id = "PatientIdentifierTypeDisplayOrder_{{row_count}}_status_necessity"
                    name = "PatientIdentifierTypeDisplayOrder[{{row_count}}][status_necessity]" >
                {{#necessity_options}} <option value="{{option}}" >{{label}}</option>{{/necessity_options}}
            </select>
        </td>
        <td><a href="javascript:void(0)" class="js-delete_patient_identifier">Delete</a></td>
    </tr>
</script>
<script>
    $(document).ready(function() {
        let $institution_sites_table = $('#institution-sites-table');
        $institution_sites_table.toggle($institution_sites_table.find('tbody').children().length !== 0);

        $('#add-institution-sites-btn').on('click', function() {
            this.blur();
            let $table = $('#institution-sites-table tbody');
            $table.parent().show();
            let nextDataKey = OpenEyes.Util.getNextDataKey($table.find('tr'), 'key');
            let tr = Mustache.render($('#add-site-template').text(), {
                key: nextDataKey
            });
            $table.append(tr);
        });

        $('#add-patient-identifier-btn').on('click', function() {
            this.blur();
            let $table = $('#institution-patient-identifiers tbody');
            let nextDataKey = OpenEyes.Util.getNextDataKey($table.find('tr'), 'key');
            let tr = Mustache.render($('#add-patient-identifier-template').text(), {
                key: nextDataKey
            });
            $table.append(tr);
        });

        $('#institution-patient-identifiers, #institution-sites-table').on('click', '.js-remove-row', function() {
            let $table = $(this).parents('table');
            $(this).closest('tr').remove();
            if ($table.attr('id') === 'institution-sites-table') {
                $table.toggle($table.find('tbody').children().length !== 0);
            }
        });

        $('#patient_identifier_usage_type, #patient_identifier_site').on('change', function() {
            window.location.href = "/admin/editinstitution?institution_id=" + <?= $institution->id ?> + "&patient_identifier_usage_type=" + $('#patient_identifier_usage_type').val() + "&patient_identifier_site=" + $('#patient_identifier_site').val();
            new OpenEyes.UI.LoadingOverlay().open();
        });

        $('.sortable tbody').sortable({
            stop: function(e, ui) {
                $('.sortable tbody tr').each(function(index, tr) {
                    index++;
                    $(tr).find("[name$='display_order]']").val(index);
                });
            }
        });

        $('#patient_identifiers_entry_table').on('click', '.js-delete_patient_identifier', function() {
            let $tr = $(this).closest('tr');
            let patient_identifier_type_id = $tr.find('input[name$="patient_identifier_type_id]"]').val();
            $('#patient_identifier_popup .select-options ul li[data-id="' + patient_identifier_type_id + '"]').removeClass('js-already-used');
            $tr.remove();
            $('.sortable tbody').sortable("refresh");
        });

        new OpenEyes.UI.AdderDialog({
            openButton: $('#add-new-rule'),
            itemSets: [new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
                array_map(function ($identifier) {
                                                                    return [
                                                                        'label' => $identifier->long_title . ' (' . $identifier->institution->name . ')',
                                                                        'id' => $identifier->id,
                                                                        'usage_type' => $identifier->usage_type,
                                                                        'long_title' => $identifier->long_title ?: '',
                                                                        'short_title' => $identifier->short_title ?: '',
                                                                        'institution_name' => $identifier->institution->name ?: '',
                                                                        'site_name' => $identifier->site ? $identifier->site->name : '',
                                                                        'prefix' => $identifier->value_display_prefix ?: '',
                                                                        'suffix' => $identifier->value_display_suffix ?: ''
                                                                    ];
                }, $identifier_types)
            ) ?>, {
                'multiSelect': true
            })],
            onOpen: function(adder_dialog) {
                adder_dialog.popup.find('li').each(function() {
                    let already_used = $(this).hasClass('js-already-used') || $('#patient_identifiers_entry_table').find('tr[data-pid_type=' + $(this).data('id') + ']').length !== 0;
                    $(this).toggle(!already_used);
                });
            },
            onReturn: function(adder_dialog, selected_items) {
                let $entry_table = $('#patient_identifiers_entry_table');
                let $tr = $('#patient_identifiers_entry_table tbody tr:not(.empty)');
                $entry_table.find('.empty').hide();
                for (let index = 0; index < selected_items.length; index++) {
                    let item = selected_items[index];
                    let output = Mustache.render($('#patient-identifier-entry-template').text(), {
                        'row_count': OpenEyes.Util.getNextDataKey($tr, 'row') + index,
                        'display_order': $tr.length + index + 1,
                        'patient_identifier_type_id': item['id'],
                        'usage_type': item['usage_type'],
                        'long_title': item['long_title'],
                        'short_title': item['short_title'],
                        'institution_name': item['institution_name'],
                        'site_name': item['site_name'],
                        'value_display_prefix': item['prefix'],
                        'value_display_suffix': item['suffix'],
                        'searchable': item['searchable'],
                        'necessity_options': <?= json_encode($necessity_options_for_json) ?>
                    });
                    $entry_table.find('tbody').append(output);
                    adder_dialog.popup.find('li[data-id=' + item['id'] + ']').addClass('js-already-used');
                }
                return true;
            }
        });
    });
</script>
<?php $this->endWidget() ?>
