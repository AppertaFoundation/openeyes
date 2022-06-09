<?php

/**
 * (C) OpenEyes Foundation, 2020
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

<div class="cols-full">
    <div class="row divider">
        <h2><?= $patient_identifier_type->isNewRecord ? 'Add' : 'Edit' ?> Patient Identifier Type</h2>
    </div>

    <?php
    echo $this->renderPartial('//admin/_form_errors', array('errors' => $errors));
    $form = $this->beginWidget(
        'BaseEventTypeCActiveForm',
        [
            'id' => 'adminform',
            'enableAjaxValidation' => false,
            'focus' => '#username',
            'layoutColumns' => array(
                'label' => 2,
                'field' => 5,
            ),
        ]
    ) ?>

    <table class="standard cols-full">
        <colgroup>
            <col class="cols-3">
            <col class="cols-full">
        </colgroup>

        <tbody>
        <?php foreach (['short_title', 'long_title'] as $field) { ?>
            <tr>
                <td><?php echo $patient_identifier_type->getAttributeLabel($field); ?></td>
                <td>
                    <?= \CHtml::activeTextField(
                        $patient_identifier_type,
                        $field,
                        [
                            'class' => 'cols-full',
                            'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete')
                        ]
                    ); ?>
                </td>
            </tr>
        <?php } ?>
            <tr>
                <td>Usage Type</td>
                <td>
                    <?= \CHtml::activeDropDownList(
                        $patient_identifier_type,
                        'usage_type',
                        ['LOCAL' => 'LOCAL', 'GLOBAL' => 'GLOBAL'],
                        ['class' => 'cols-full']
                    ); ?>
                </td>
            </tr>
        <?php foreach (['validate_regex','value_display_prefix', 'value_display_suffix', 'pad', 'spacing_rule'] as $field) { ?>
            <tr>
                <td><?php echo $patient_identifier_type->getAttributeLabel($field); ?></td>
                <td>
                    <?= \CHtml::activeTextField(
                        $patient_identifier_type,
                        $field,
                        [
                            'class' => 'cols-full',
                            'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete')
                        ]
                    ); ?>
                </td>
            </tr>
        <?php } ?>
            <tr>
                <td>Institution</td>
                <td>
                    <?= \CHtml::activeDropDownList(
                        $patient_identifier_type,
                        'institution_id',
                        CHtml::listData(Institution::model()->findAll(), 'id', 'name'),
                        ['class' => 'cols-full', 'empty' => '-- Empty --']
                    ); ?>
                </td>
            </tr>
            <tr>
                <td>Site</td>
                <td>
                    <?= \CHtml::activeDropDownList(
                        $patient_identifier_type,
                        'site_id',
                        CHtml::listData(Site::model()->findAllByAttributes(['institution_id' => $patient_identifier_type->institution_id]), 'id', 'name'),
                        ['class' => 'cols-full', 'empty' => 'NOT SITE SPECIFIC']
                    ); ?>
                </td>
            </tr>
            <tr>
                <td>PAS Config</td>
                <td>
                    <div style="visibility:hidden" id="json-alert-box" class="alert-box warning">Invalid JSON string</div>
                    <?php
                        $pas_api = $patient_identifier_type->pas_api;
                        $pas_api_json =  $pas_api ? (is_array($pas_api) ? json_encode($pas_api) : $pas_api ) : '';
                        echo \CHtml::textArea('PatientIdentifierType[pas_api]', $pas_api_json, [
                            'class' => 'cols-full autosize',
                            'rows' => 18
                        ]);
                        ?>
                <button id="json-beautify" type="button" class="btn">Beautify JSON</button>
                <button id="json-template" type="button" class="btn">Show template</button>
                </td>
            </tr>
        </tbody>

        <tfoot>
        <tr>
            <td colspan="2">
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
                        'data-uri' => '/Admin/PatientIdentifierType/index',
                        'name' => 'cancel',
                        'id' => 'et_cancel'
                    ]
                ); ?>
            </td>
        </tr>
        </tfoot>
    </table>



    <?php $this->endWidget() ?>
</div>
<script>
    function ready(fn) {
        if (document.readyState != 'loading') {
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    }

    function validateJSON(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }

    function beautifyJSON() {
        const value = document.getElementById('PatientIdentifierType_pas_api').value;

        if (value) {
            // using JSON.stringify pretty print capability:
            var str = JSON.stringify(JSON.parse(value), null, 4);

            // display pretty printed object in text area:
            document.getElementById('PatientIdentifierType_pas_api').value = str;
        }
    }

    ready(function() {

        beautifyJSON();
        validateJSON();

        const el = document.getElementById('PatientIdentifierType_institution_id');
        el.addEventListener('change', function() {
            getInstitutionSites(el.value, $('#PatientIdentifierType_site_id'));
        });

        const pasConfig = document.getElementById('PatientIdentifierType_pas_api');
        pasConfig.addEventListener('keyup', function() {
            const isValid = validateJSON(pasConfig.value);
            document.getElementById('json-alert-box').style.visibility = isValid ? 'hidden' : 'visible';
        });

        const beautifyJSONButton = document.getElementById('json-beautify');
        beautifyJSONButton.addEventListener('click', function() {
            beautifyJSON();
            beautifyJSONButton.blur();
        });

        const templateButton = document.getElementById('json-template');
        templateButton.addEventListener('click', function() {
            const json_string = '{"enabled":false,"class":"DefaultPas","search_params":["hos_num","nhs_num","first_name","last_name","maiden_name"],"allowed_params":[],"url":"http://localhost","curl_timeout":10,"proxy":false,"cache_time":300}';
            new OpenEyes.UI.Dialog({
                title: 'PAS Config template',
                content: $('<textarea>', {value: JSON.stringify(JSON.parse(json_string), null, 4), "class":"cols-full", rows:18}),
                dialogClass: 'js-pas-config-template',
            }).open();
        });


    });
</script>
