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

use OEModule\OphCiExamination\models\SystemicDiagnoses_Diagnosis;

/** @var \OEModule\OphCiExamination\models\SystemicDiagnoses $element */
?>

<script type="text/javascript" src="<?= $this->getJsPublishedPath('SystemicDiagnoses.js') ?>"></script>
<script type="text/javascript" src="<?= $this->getJsPublishedPath('OpenEyes.UI.DiagnosesSearch.js', true) ?>"></script>
<script type="text/javascript" src="<?= $this->getPublishedPath('../widgets/js', 'EyeSelector.js', true) ?>"></script>

<?php
$model_name = CHtml::modelName($element);
$missing_req_diagnoses = $this->getMissingRequiredSystemicDiagnoses();
$required_diagnoses_ids = array_map(function ($r) {
    return $r->id;
}, $this->getRequiredSystemicDiagnoses($element->event ? $element->event->firm_id : null));
?>

<div class="element-fields flex-layout full-width" id="<?= CHtml::modelName($element); ?>_element">
    <input type="hidden" name="<?= $model_name ?>[present]" value="1"/>
    <input type="hidden" name="diabetic_diagnoses[]"/>
    <div class="cols-1 align-left <?= $model_name ?>_no_systemic_diagnoses_wrapper">
        <label class="inline highlight" for="<?= $model_name ?>_no_systemic_diagnoses">
            <?= \CHtml::checkBox(
                $model_name . '[no_systemic_diagnoses]',
                $element->no_systemic_diagnoses_date ? true : false,
                array('class' => $model_name . '_no_systemic_diagnoses')
            ); ?>
            No systemic diagnoses
        </label>
    </div>
    <div class="data-group cols-10">
    <table class="cols-full" id="<?= $model_name ?>_diagnoses_table">
        <colgroup>
            <col class="cols-3">
            <col class="cols-3">
            <col class="cols-1">
            <col class="cols-1">
            <col class="cols-1">
        </colgroup>
        <tbody>
        <?php
        $row_count = 0;
        foreach ($missing_req_diagnoses as $diagnosis) {
            $this->render(
                'SystemicDiagnosesEntry_event_edit',
                array(
                    'diagnosis' => $diagnosis,
                    'form' => $form,
                    'model_name' => CHtml::modelName($element),
                    'row_count' => $row_count,
                    'field_prefix' => $model_name . "[entries][$row_count]",
                    'removable' => false,
                    'posted_not_checked' => $element->widget->postedNotChecked($row_count),
                )
            );
            $row_count++;
        } ?>
        <?php
        foreach (array_merge($element->diagnoses, $this->getCheckedRequiredSystemicDiagnoses()) as $diagnosis) {
            $this->render(
                'SystemicDiagnosesEntry_event_edit',
                array(
                    'diagnosis' => $diagnosis,
                    'form' => $form,
                    'model_name' => CHtml::modelName($element),
                    'row_count' => $row_count,
                    'field_prefix' => $model_name . "[entries][$row_count]",
                    'removable' => !in_array($diagnosis->disorder_id, $required_diagnoses_ids),
                    'posted_not_checked' => $element->widget->postedNotChecked($row_count),
                )
            );
            $row_count++;
        }
        ?>
        </tbody>
    </table>
    </div>
    <div class="add-data-actions flex-item-bottom" id="systemic-diagnoses-popup" data-test="systemic-diagnoses-popup"
         style="display: <?php echo $element->no_systemic_diagnoses_date ? 'none' : ''; ?>">
        <button class="button hint green js-add-select-search" type="button" id="add-history-systemic-diagnoses" data-test="add-systemic-diagnoses-button">
            <i class="oe-i plus pro-theme"></i>
        </button>
    </div>
</div>
<script type="text/template" class="entry-template hidden" id="<?= CHtml::modelName($element) . '_template' ?>">
    <?php
    $empty_entry = new \OEModule\OphCiExamination\models\SystemicDiagnoses_Diagnosis();
    $this->render(
        'SystemicDiagnosesEntry_event_edit',
        array(
            'diagnosis' => $empty_entry,
            'form' => $form,
            'model_name' => $model_name,
            'field_prefix' => $model_name . '[entries][{{row_count}}]',
            'row_count' => '{{row_count}}',
            'removable' => true,
            'posted_not_checked' => false,
            'has_disorder' => false,
            'values' => array(
                'id' => '',
                'disorder_id' => '{{disorder_id}}',
                'disorder_display' => '{{disorder_display}}',
                'is_diabetes' => '{{is_diabetes}}',
                'side_id' => (string)EyeSelector::$NOT_CHECKED,
                'side_display' => '{{side_display}}',
                'date' => '{{date}}',
                'date_display' => '{{date_display}}',
                'row_count' => '{{row_count}}',
                'has_disorder' => SystemicDiagnoses_Diagnosis::$PRESENT,
            ),
        )
    );
    ?>
</script>
<script type="text/javascript">
    $(document).ready(function () {

        function refreshDate(e, event_type) {
            let inp = null;
            let ISOdate = '';
            let hidden_target = null;
            let errors = [];
            let UKdate;

            if (event_type === 'pickmeup') {
                inp = $(e.target);
                UKdate = inp.val();
                hidden_target = $(inp.data('hidden-input-selector'));
                let dateObject = e.originalEvent.detail.date;
                ISOdate = $.datepicker.formatDate('yy-mm-dd',dateObject);
            } else {
                inp = $(e.currentTarget);
                let dateArray = inp.val().split(" ");
                UKdate = inp.val();
                if(UKdate.length === 0){
                    $(hidden_target).val('');
                    return false;
                }
                hidden_target = $(inp.data('hidden-input-selector'));

                switch(dateArray.length) {
                    case 3:
                        try {
                            var dateObject = new Date(UKdate);
                            $.datepicker.parseDate( 'dd M yy', UKdate );
                            ISOdate = $.datepicker.formatDate('yy-mm-dd',dateObject);
                        } catch (e) {
                            errors.push('Invalid date: '+UKdate);
                        }
                        break;
                    case 2:
                        try {
                            createdDate = '01 '+UKdate;
                            $.datepicker.parseDate( 'dd M yy', createdDate );
                            ISOdate = $.datepicker.formatDate('yy-mm', new Date( createdDate ));
                        } catch (e) {
                            errors.push('Invalid date: '+UKdate);
                        }
                        break;
                    case 1:
                        if(dateArray[0] > 1970){
                            ISOdate = dateArray[0];
                        } else {
                            errors.push('Invalid date: '+UKdate);
                        }
                        break;
                    default:
                        errors.push('Invalid date: '+UKdate);
                        break;
                }
            }

            if(errors.length > 0){
                new OpenEyes.UI.Dialog.Alert({
                    content: errors.join(', ')
                }).open();
                return false;
            } else {
                $(hidden_target).val(ISOdate);
            }

        }

        function addEventListenerToPickMeUp() {
            $('.systemic-diagnoses-date').on('pickmeup-change', function (e) {
                refreshDate(e, 'pickmeup')
            });
            $('.systemic-diagnoses-date').on('change', function (e) {
                refreshDate(e);
            })
        }

        let systemic_diagnoses_controller = new OpenEyes.OphCiExamination.SystemicDiagnosesController({
            element: $('#<?=$model_name?>_element')
        });

        new OpenEyes.UI.AdderDialog({
            openButton: $('#add-history-systemic-diagnoses'),
            itemSets: [
                <?php
                $criteria = new CDbCriteria();
                $criteria->addCondition('id IN (SELECT DISTINCT group_id FROM `common_systemic_disorder` WHERE group_id IS NOT NULL)');
                $valid_common_systemic_disorder_groups = CommonSystemicDisorderGroup::model()->findAllAtLevels(
                    ReferenceData::LEVEL_ALL,
                    $criteria
                );
                if (!empty($valid_common_systemic_disorder_groups)) { ?>
                new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
                    array_map(function ($disorder_group) {
                        return [
                            'label' => $disorder_group->name,
                            'filter-value' => $disorder_group->id,
                            'is_filter' => true,
                        ];
                    },
                        $valid_common_systemic_disorder_groups)
                                                    ) ?>, {
                    'header': 'Disorder Group',
                    'id': 'disorder-group-filter',
                    'deselectOnReturn': false,
                }),
                <?php } ?>
                new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
                    array_map(function ($disorder) {
                        return ['label' => $disorder['term'], 'id' => $disorder['id'], 'is_diabetes' => $disorder['is_diabetes'], 'filter_value' => $disorder['group_id'],];
                    }, CommonSystemicDisorder::getDisordersWithDiabetesInformation())
                ) ?>, {
                    'id': 'disorder-list',
                    'header': 'Disorder',
                    'multiSelect': true,
                })
            ],
            onReturn: function (adder_dialog, selected_items) {
                for (let i in selected_items) {
                    let item = selected_items[i];
                    if (!item.is_filter) {
                        systemic_diagnoses_controller.addEntry(item);
                        adder_dialog.popup.find('li[data-id=' + item.id + ']').addClass('js-already-used');
                        addEventListenerToPickMeUp();
                    }
                }
                return true;
            },
            onOpen: function (adder_dialog) {
                let filters = $('ul.category-filter').find('li.selected');
                if (filters.length > 0) {
                    systemic_diagnoses_controller.$popup.find('li').each(function () {
                        let already_used = $(this).hasClass('js-already-used');
                        if (($(this).data('filter_value') !== $(filters[0]).data('filter-value') || already_used) && !$(this).data('filter-value')) {
                            $(this).hide();
                        } else {
                            $(this).show();
                        }

                    });
                } else {
                    adder_dialog.popup.find('li').each(function () {
                        let already_used = $(this).hasClass('js-already-used');
                        $(this).toggle(!already_used);
                    });
                }

            },
            searchOptions: {
                searchSource: systemic_diagnoses_controller.options.searchSource,
                code: systemic_diagnoses_controller.options.code,
                resultsFilter: function (results) {
                    return $(results).filter(function (key, disorder) {
                        return systemic_diagnoses_controller.$table
                            .find('input[type="hidden"][name*="disorder_id"][value="' + disorder.id + '"]').length === 0;
                    });
                }
            },
            listFilter: true,
            filterListId: "disorder-group-filter",
            listForFilterId: "disorder-list",
            liClass: "restrict-width extended",
        });

        systemic_diagnoses_controller.$table.find("input[name$='[disorder_id]']").each(function () {
            systemic_diagnoses_controller.$popup.find('li[data-id=' + $(this).val() + ']').addClass('js-already-used');
        });

        addEventListenerToPickMeUp();
    });
</script>
