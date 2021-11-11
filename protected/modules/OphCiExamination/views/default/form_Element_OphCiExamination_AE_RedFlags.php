<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

// Yii::app()->clientScript->registerScriptFile("{$this->assetPath}/js/RedFlags.js", CClientScript::POS_HEAD);


$flag_options = $element->getMyFlagOptions();
$current_flags = $element->getMyCurrentFlagOptionIDs();
?>


<div class="element-fields flex-layout full-width" id="AE_RedFlags_element">
    <div class="data-group cols-10">
        <div class="cols-5 align-left">
            <?= $form->checkBox($element, 'nrf_check', array('nowrapper' => true, 'class' => 'js-AE-RedFlags-nrf-check')) ?>
        </div>
        <table id="AE_RedFlags_entry_table" class="cols-full">
            <tbody>
                <tr>
                    <td>
                        <ul class="dot-list" id="red-flag-assignment">
                            <?php foreach ($element->flags as $tempFlag) {
                                if($tempFlag->flag) {?>
                                    <li>
                                        <input type="hidden" name="OEModule_OphCiExamination_models_Element_OphCiExamination_AE_RedFlags[flags][]" value="<?= $tempFlag->flag->id ?>">
                                        <span><?= $tempFlag->flag->name ?></span>
                                    </li>
                            <?php }
                            } ?>
                        </ul>
                        <span class="data-value large-text"></span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="add-data-actions flex-item-bottom" id="add-ae-redflags-popup" <?= $element->nrf_check?'style="display: none;"':'' ?>>
        <button id="show-add-rf-popup" class="button hint green js-add-select-search" type="button">
            <i class="oe-i plus pro-theme"></i>
        </button>
    </div>
    <script type="x-tmpl-mustache" id="add-redflag-template">
        <li>
            <input type="hidden" name="OEModule_OphCiExamination_models_Element_OphCiExamination_AE_RedFlags[flags][]" value="{{redflag_id}}">
            <span>{{redflag_name}}</span>
        </li>
    </script>
    <script type="text/javascript">
        function addRedFlag(redflag_id, redflag_name) {
            let data = {
                'redflag_id': redflag_id,
                'redflag_name': redflag_name,
            };
            return Mustache.render($('#add-redflag-template').text(), data);
        }

        new OpenEyes.UI.AdderDialog({
            openButton: $('#add-ae-redflags-popup'),
            deselectOnReturn: false,
            itemSets: [new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(array_map(function ($flag) use ($current_flags) {
                return ['label' =>  $flag->name, 'id' => $flag->id, 'selected' => in_array($flag->id, $current_flags)];
                                                           }, $flag_options, $current_flags)) ?>, {
                multiSelect: true,
                id: "red_flag_list"
            }), ],
            onOpen: function(adderDialog) {
                adderDialog.popup.find('li').each(function() {
                    let risk_id = $(this).data('id');
                });
            },
            onReturn: function(adderDialog, selectedItems) {
                let $red_flag_list = $('#red-flag-assignment');
                $red_flag_list.empty();
                for (let i = 0; i < selectedItems.length; ++i) {
                    $red_flag_list.append(addRedFlag(selectedItems[i].id, selectedItems[i].label));
                };
            },
        });
        $('.js-AE-RedFlags-nrf-check').change(function() {
            if (this.checked == true) {
                $('#add-ae-redflags-popup').hide();
                $('#red-flag-assignment').empty();
            } else {
                $('#add-ae-redflags-popup').show();
            }
        });
    </script>
</div>