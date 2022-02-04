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
 * @see http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

if ($element->isNewRecord) {
    if (isset($_REQUEST["type_id"])) {
        $consentFormType = $_REQUEST["type_id"];
    } else {
        $consentFormType = Element_OphTrConsent_Type::TYPE_PATIENT_AGREEMENT_ID;
    }
} else {
    $consentFormType = Element_OphTrConsent_Type::model()->find("event_id = ?", array($element->event_id))->type_id;
}

if ($consentFormType == Element_OphTrConsent_Type::TYPE_UNABLE_TO_CONSENT_ID) {
    $showOnlyForCform4 = 'js-showsecond';
    $toggle_secOp = 'js-showOnlyForCform4';
} else {
    $showOnlyForCform4 = 'js-hideOnlyForCform4';
    $toggle_secOp = 'js-toggle_secOp';
}
?>
<div class="element-fields full-width">
    <div class="cols-10">
        <table class="cols-full last-left">
            <colgroup>
                <col class="cols-6">
            </colgroup>
            <tbody>
                <tr>
                    <td>
                        <?= CHtml::encode($element->getAttributeLabel('name_hp')); ?>
                    </td>
                    <td>
                        <?php $this->widget(
                            'application.widgets.AutoCompleteSearch',
                            ['field_name' => 'fao-search', 'htmlOptions' => ['placeholder' => 'Search for Health Professional']]
                        ); ?>
                        <div id="fao-field">
                        <?php if (strcmp($element->name_hp, "") !== 0) { ?>
                            <ul class="oe-multi-select inline">
                                <li>
                                    <?= $element->name_hp; ?>
                                    <i class="oe-i remove-circle small-icon pad-left fao"></i>
                                </li>
                            </ul>
                            <?php
                                $element->consultant_id = $element->user->id;
                        }
                        ?>
                        </div>
                        <?php
                            echo $form->hiddenField($element, 'name_hp');
                            echo $form->hiddenField($element, 'consultant_id');
                        ?>
                    </td>
                </tr>
                <tr class="<?= $showOnlyForCform4; ?>">
                    <td>
                        Have you sought a second opinion?
                    </td>
                    <td>
                        <fieldset>
                            <?php
                            echo $form->radioButtonList(
                                $element,
                                'second_op',
                                ['1' => 'Yes', '0' => 'No'],
                                $htmlOptions = ['separator' => '']
                            ); ?>
                        </fieldset>
                    </td>
                </tr>
                <tr class=" <?= $toggle_secOp; ?>" hidden>
                    <td>
                        <?= CHtml::encode($element->getAttributeLabel('sec_op_hp')); ?>
                    </td>
                    <td>
                        <?php $this->widget(
                            'application.widgets.AutoCompleteSearch',
                            ['field_name' => 'fao-search2', 'htmlOptions' => ['placeholder' => 'Search for Health Professional']]
                        ); ?>
                        <div id="fao-field2">
                            <?php if ($element->sec_op_hp) { ?>
                                <ul class="oe-multi-select inline">
                                    <li>
                                        <?= $element->sec_op_hp; ?>
                                        <i class="oe-i remove-circle small-icon pad-left fao2"></i>
                                    </li>
                                </ul>
                            <?php } ?>
                        </div>
                        <?php
                        echo $form->hiddenField($element, 'sec_op_hp');
                        ?>
                    </td>
                </tr>
            </div>
        </tbody>
    </table>
</div>
<script>
    <?php if (strcmp($element->name_hp, "") !== 0) { ?>
        $("#fao-search").hide();
    <?php } ?>
    <?php if (strcmp($element->sec_op_hp, "") !== 0) { ?>
        $("#fao-search2").hide();
    <?php } ?>
    OpenEyes.UI.AutoCompleteSearch.init({
        input: $('#fao-search'),
        url: '/user/autocomplete',
        onSelect: function() {
            let AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
            $('#fao-search').hide();
            $('#fao-field').html('<ul class="oe-multi-select inline"><li>' + AutoCompleteResponse.label +
                '<i class="oe-i remove-circle small-icon pad-left fao"></i></li></ul>');
            $('#fao-field').show();
        }
    });
    $(document).on('click', '.oe-i.remove-circle.small-icon.pad-left.fao', function(e) {
        e.preventDefault();
        let hiddenField = $(this).closest('td').children('input');
        let userField = $(this).closest('ul');
        $('#Element_OphTrConsent_Consenttakenby_name_hp').val("");
        $('#fao-search').show();
        $('#fao-field').hide();
    });
    $(document).on('click', '.oe-i.remove-circle.small-icon.pad-left.fao2', function(e) {
        e.preventDefault();
        let hiddenField = $(this).closest('td').children('input');
        let userField = $(this).closest('ul');
        $('#Element_OphTrConsent_Consenttakenby_sec_op_hp').val("");
        $('#fao-search2').show();
        $('#fao-field2').hide();
    });
    OpenEyes.UI.AutoCompleteSearch.init({
        input: $('#fao-search2'),
        url: '/user/autocomplete',
        onSelect: function() {
            let AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
            $('#fao-search2').hide();
            $('#fao-field2').html('<ul class="oe-multi-select inline"><li>' + AutoCompleteResponse.label +
                '<i class="oe-i remove-circle small-icon pad-left fao2"></i></li></ul>');
            $('#fao-field2').show();
        }
    });
    $(document).ready(function() {
        $(".js-showOnlyForCform4").css("display", "contents");
        $(".js-hideOnlyForCform4").css("display", "none");
        // hide and toggle the display for second opinion section
        $(".js-toggle_secOp").css("display", "none");
        let checked = $("#Element_OphTrConsent_Consenttakenby_second_op_0").is(':checked');
        if (checked) {
            $(".js-toggle_secOp").show('fast');
            $(".js-showOnlyForCform4").show('fast');
        } else {
            $(".js-toggle_secOp").hide('fast');
            $(".js-showOnlyForCform4").hide('fast');
        }
        $("#Element_OphTrConsent_Consenttakenby_second_op_1").click(function() {
            $(".js-toggle_secOp").hide('fast');
            $(".js-showOnlyForCform4").hide('fast');
        });
        $("#Element_OphTrConsent_Consenttakenby_second_op_0").click(function() {
            $(".js-toggle_secOp").show('fast');
            $(".js-showOnlyForCform4").show('fast');
        });
    });
    // On element change.
    $('#fao-field').bind('DOMSubtreeModified', function() {
        let nameofhp = $("#fao-field > ul > li").text();
        // set the health professional input
        $('#Element_OphTrConsent_Consenttakenby_name_hp').val(nameofhp);
    });
    // On element change.
    $('#fao-field2').bind('DOMSubtreeModified', function() {
        let sec_op_hp = $("#fao-field2 > ul > li").text();
        // set the health professional input
        $('#Element_OphTrConsent_Consenttakenby_sec_op_hp').val(sec_op_hp);
    });
</script>