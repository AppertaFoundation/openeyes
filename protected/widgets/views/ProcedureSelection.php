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
<div class="flex-layout procedure-selection eventDetail<?= $last ? 'eventDetailLast' : '' ?>" id="typeProcedure" style="<?= $hidden ? 'display: none;' : '' ?>">
    <?php if ($label && sizeof($label)) { ?>
        <div class="cols-2">
            <label for="select_procedure_id_<?php echo $identifier; ?>">
                <?php echo $label ?>
            </label>
        </div>
    <?php } ?>
    <?php $totalDuration = 0; ?>

    <table class="cols-10" id="procedureList_<?php echo $identifier ?>" style="<?= empty($selected_procedures) ? 'visibility: hidden;' : '' ?>">
        <thead>
            <tr>
                <th>Procedure</th>
                <?php if ($durations) { ?>
                    <th colspan="2">Duration (adjusted for complexity)</th>
                <?php } ?>
                <th></th>
            </tr>
        </thead>
        <tbody class="body">
            <?php
            if (isset($selected_procedures)) {
                foreach ($selected_procedures as $procedure) :
                    $totalDuration += $this->adjustTimeByComplexity($procedure['default_duration'], $complexity); ?>

                    <tr class="item">
                        <td class="procedure">
                            <span class="field"><?= \CHtml::hiddenField('Procedures_' . $identifier . '[]', $procedure->id, ['class' => 'js-procedure']); ?></span>
                            <span class="value"><?= $procedure->term; ?></span>
                        </td>

                        <?php if ($durations) { ?>
                            <td class="duration">
                                <span data-default-duration="<?= $procedure->default_duration ?>">
                                    <?= $this->adjustTimeByComplexity($procedure->default_duration, $complexity); ?>
                                </span> mins
                            </td>
                        <?php } ?>
                        <td>
                            <span class="removeProcedure"><i class="oe-i trash"></i></span>
                        </td>
                    </tr>

            <?php endforeach;
            } ?>

            <?php
            if (isset($_POST[$class]['total_duration_' . $identifier])) {
                $adjusted_total_duration = $_POST[$class]['total_duration_' . $identifier];
            }
            ?>
        </tbody>

        <?php if ($durations) { ?>
            <tfoot>
                <tr>
                    <td></td>
                    <td>
                        <span id="projected_duration_<?php echo $identifier ?>">
                            <span><?= \CHtml::encode($totalDuration) ?></span> mins
                        </span>
                        <span class="fade">(calculated)</span>
                    </td>
                    <?php if ($showEstimatedDuration) { ?>
                        <td class="align-left">
                            <input type="text" value="<?= $adjusted_total_duration ?>" id="<?= $class ?>_total_duration_<?= $identifier ?>" name="<?= $class ?>[total_duration_<?= $identifier ?>]" style="width:60px" data-total-duration="<?= $total_duration ?>" />
                            <span class="fade">mins (estimated)</span>
                        </td>
                    <?php } ?>
                </tr>
            </tfoot>

        <?php } ?>
    </table>

    <div class="add-data-actions flex-item-bottom">
        <button class="button hint green add-entry" type="button" id="add-procedure-list-btn-<?= $identifier ?>">
            <i class="oe-i plus pro-theme"></i>
        </button>
    </div>

</div>

<script type="text/javascript">
    $(document).ready(function() {
        const low_complexity = "0";
        const high_complexity = "10";
        const high_percentage = typeof op_booking_inc_time_high_complexity !== "undefined" ? parseInt(window.op_booking_inc_time_high_complexity) : 20;
        const low_percentage = typeof op_booking_decrease_time_low_complexity !== "undefined" ? parseInt(window.op_booking_decrease_time_low_complexity) : 10;
        const identifier = "<?= $identifier ?>";
        const $projected_duration = $('#projected_duration_' + identifier + ' span');

        // Note: Removed_stack is probably not the best name for this. Selected procedures is more accurate.
        // It is used to suppress procedures from the add a procedure inputs
        var removed_stack_<?php echo $identifier ?> = [<?php echo implode(',', $removed_stack); ?>];

        function getComplexity() {
            let $checked = $('input[name="Element_OphTrOperationbooking_Operation[complexity]"]:checked');
            return $checked ? $checked.val() : null;
        }

        function calculateDurationByComplexity(duration, complexity) {
            let adjusted_duration = duration;
            if (complexity === high_complexity) {
                adjusted_duration = (1 + (high_percentage / 100)) * duration;
            } else if (complexity === low_complexity) {
                adjusted_duration = (1 - (low_percentage / 100)) * duration;
            }

            return Math.ceil(adjusted_duration);
        }

        function updateTotalDuration(identifier) {

            // update total duration
            let totalDuration = 0;
            let adjustedTotalDuration = 0;
            $('#procedureList_' + identifier).find('.item').map(function() {
                let $span = $(this).find('.duration span');
                if ($span.length > 0) {
                    let duration = parseInt($span.data('default-duration'));
                    let adjustedDuration;

                    if ($('input[name=\"<?= $class ?>[eye_id]\"]:checked').val() == 3) {
                        totalDuration *= 2;
                    }
                    adjustedDuration = calculateDurationByComplexity(duration, getComplexity());

                    totalDuration += duration;
                    adjustedTotalDuration += adjustedDuration;

                    $span.text(adjustedDuration);
                }
            });

            if (parseInt($projected_duration.text()) === parseInt($('#<?php echo $class ?>_total_duration_' + identifier).val()) ||
                $('#<?php echo $class ?>_total_duration_' + identifier).val() === '') {
                $('#<?php echo $class ?>_total_duration_' + identifier).val(adjustedTotalDuration);
                $('#<?php echo $class ?>_total_duration_' + identifier).data('total-duration', totalDuration);
            }
            $projected_duration.text(adjustedTotalDuration);
        }

        let list_selector = '';
        if (typeof moduleName !== 'undefined' && moduleName === "OphTrConsent") {
            list_selector = 'td #typeProcedure'
        } else {
            list_selector = '#typeProcedure'
        }

        $(list_selector).on('click', '.removeProcedure', function() {
            let $table = $(this).closest("[id^='procedureList_']");
            if ($table) {
                let identifier = $table.attr('id').match(/^procedureList_(.*?)$/);
                removeProcedure($(this).closest('tr'), identifier[1]);
            }

            return false;
        });

        function removeProcedure($table_row, identifier) {
            var length = $table_row.siblings('tr').length;
            var procedure_id = $table_row.find('input[type="hidden"]:first').val();
            const $procedure_list = $('#procedureList_' + identifier);

            $table_row.remove();

            $projected_duration.text(0);
            $('#<?php echo $class ?>_total_duration_' + identifier).val(0);

            <?php if ($durations) { ?>
                updateTotalDuration(identifier);
            <?php } ?>

            if (length < 1) {
                $procedure_list.css('visibility', 'hidden');
                <?php if ($durations) { ?>
                    $procedure_list.find('.durations').hide();
                <?php } ?>
            }

            if (typeof(window.callbackRemoveProcedure) === 'function') {
                callbackRemoveProcedure(procedure_id);
            }

            // Remove removed procedure from the removed_stack
            var stack = [];
            var popped = null;
            $.each(removed_stack_<?php echo $identifier ?>, function(key, value) {
                if (value["id"] != procedure_id) {
                    stack.push(value);
                } else {
                    popped = value;
                }
            });
            removed_stack_<?php echo $identifier ?> = stack;

            // Refresh the current procedure select box in case the removed procedure came from there
            if ($('#subsection_id_' + identifier).length) {
                // Procedures are in subsections, so fetch a clean list via ajax (easier than trying to work out if it's the right list)
                updateProcedureSelect(identifier);
            } else if (popped) {

                let popID = popped["id"];
                let popName = popped["name"];
                let proc_exist = ($(`li[data-id="${popID}"]`).length > 0);
                if (!proc_exist){
                    // No subsections, so we should be safe to just push it back into the list
                    $('ul.add-options[data-id="select"]').append(
                        `<li data-label="${popName}" data-id="${popID}">
                        <span class="auto-width">${popName}</span>
                        </li>`
                    ).removeAttr('disabled');
                    sort_ul($('ul.add-options[data-id="select"]'));
                }
            }

            return false;
        }

        function sort_ul(element) {
            let rootItem = element.children('li:first').text();
            element.append(element.children('li').sort(selectSortSuper(rootItem)));
        }

        function selectSortSuper(rootItem) {
            return function selectSort(a, b) {
                if (a.innerHTML == rootItem) {
                    return -1;
                } else if (b.innerHTML == rootItem) {
                    return 1;
                }
                return (a.innerHTML > b.innerHTML) ? 1 : -1;
            };
        }

        $('select[id^=subsection_id]').unbind('change').change(function() {
            var m = $(this).attr('id').match(/^subsection_id_(.*)$/);
            updateProcedureSelect(m[1]);
        });

        function initialiseProcedureAdder() {
            $('.add-options[data-id="subsections"]').on('click', 'li', function() {
                let id = $(this).attr('class') === 'selected' ? '' : $(this).data('id');
                updateProcedureDialog(id);
            });
            if ($('.add-options[data-id="subsections"] > li').length === 0) {
                $('.add-options[data-id="subsections"]').hide();
            }

            if ($('.add-options[data-id="select"] > li').length === 0) {
                $('.add-options[data-id="select"]').hide();
            }

            // Set select dialog to show defaults when first loading
            updateProcedureDialog('');
        }

        function updateProcedureDialog(subsection) {
            if (subsection !== '') {
                $.ajax({
                    'url': '<?php echo Yii::app()->createUrl('procedure/list') ?>',
                    'type': 'POST',
                    'data': {
                        'subsection': subsection,
                        'dialog': true,
                        'YII_CSRF_TOKEN': YII_CSRF_TOKEN
                    },
                    'success': function(data) {
                        $('.add-options[data-id="select"]').each(function() {
                            $(this).html(data).find('li').find('span').removeClass('auto-width').addClass('restrict-width extended');
                            $(this).show();
                        });
                    }
                });
            } else {
                <?php
                $firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
                $subspecialty_id = $firm->serviceSubspecialtyAssignment ? $firm->serviceSubspecialtyAssignment->subspecialty_id : null;
                $subspecialty_procedures = ProcedureSubspecialtyAssignment::model()->getProcedureListFromSubspecialty($subspecialty_id);
                $formatted_procedures = "";
                foreach ($subspecialty_procedures as $proc_id => $subspecialty_procedure) {
                    $formatted_procedures .= "<li data-label='$subspecialty_procedure'data-id='$proc_id' class=''>" .
                        "<span class='auto-width'>$subspecialty_procedure</span></li>";
                }
                ?>
                $('.add-options[data-id="select"]').each(function() {
                    $(this).html("<?= $formatted_procedures ?>");
                    $(this).show();
                });
            }
        }

        function updateProcedureSelect(identifier) {
            let subsection_field = $('select[id=subsection_id_' + identifier + ']');
            let subsection = subsection_field.val();
            if (subsection !== '') {
                $.ajax({
                    'url': '<?php echo Yii::app()->createUrl('procedure/list') ?>',
                    'type': 'POST',
                    'data': {
                        'subsection': subsection,
                        'YII_CSRF_TOKEN': YII_CSRF_TOKEN
                    },
                    'success': function(data) {
                        $('select[name=select_procedure_id_' + identifier + ']').attr('disabled', false);
                        $('select[name=select_procedure_id_' + identifier + ']').html(data);

                        // remove any items in the removed_stack
                        $('select[name=select_procedure_id_' + identifier + '] option').map(function() {
                            var obj = $(this);

                            $.each(removed_stack_<?php echo $identifier ?>, function(key, value) {
                                if (value["id"] == obj.val()) {
                                    obj.remove();
                                }
                            });
                        });

                        $('select[name=select_procedure_id_' + identifier + ']').parent().css('visibility', 'visible');
                    }
                });
            } else {
                $('select[name=select_procedure_id_' + identifier + ']').parent().hide();
            }
        }

        $('select[id^="select_procedure_id"]').unbind('change').change(function() {
            var m = $(this).attr('id').match(/^select_procedure_id_(.*)$/);
            var identifier = m[1];
            var select = $(this);
            var procedure = $('select[name=select_procedure_id_' + m[1] + '] option:selected').text();
            if (procedure != 'Select a commonly used procedure') {

                if (typeof(window.callbackVerifyAddProcedure) == 'function') {
                    window.callbackVerifyAddProcedure(procedure, "<?php echo $durations ? '1' : '0'; ?>", function(result) {
                        if (result != true) {
                            select.val('');
                            return;
                        }

                        if (typeof(window.callbackAddProcedure) == 'function') {
                            var procedure_id = $('select[name=select_procedure_id_' + identifier + '] option:selected').val();
                            callbackAddProcedure(procedure_id);
                        }

                        ProcedureSelectionSelectByName(procedure, false, m[1]);
                    });
                } else {
                    if (typeof(window.callbackAddProcedure) == 'function') {
                        var procedure_id = $('select[name=select_procedure_id_' + identifier + '] option:selected').val();
                        callbackAddProcedure(procedure_id);
                    }

                    ProcedureSelectionSelectByName(procedure, false, m[1]);
                }
            }
            return false;
        });

        <?php if ($durations) : ?>
            $(document).ready(function() {
                if ($('input[name="<?php echo $class ?>[eye_id]"]:checked').val() == 3) {
                    $('#projected_duration_<?php echo $identifier ?> span').html(parseInt($('#projected_duration_<?php echo $identifier ?> span').html()));
                }
                $('input[name="<?php echo $class ?>[eye_id]"]').click(function() {
                    updateTotalDuration('<?php echo $identifier ?>');
                });
            });
        <?php endif ?>

        function ProcedureSelectionSelectByName(name, callback, identifier, procedure_id) {
            $.ajax({
                'url': baseUrl + '/procedure/details?durations=<?php echo $durations ? '1' : '0' ?>&identifier=' + identifier,
                'type': 'GET',
                'data': {
                    'name': name
                },
                'success': function(data) {
                    let enableDurations = <?php echo $durations ? 'true' : 'false' ?>;

                    // append duration of the procedure
                    $('#procedureList_' + identifier + ' span.value:contains(' + name + ')').each(function () {
                        if ($(this).text() === name) {
                            $(this).parents('td.procedure').after(data);
                        }
                    });
                    $('#procedureList_' + identifier).css('visibility', 'visible');

                    if (enableDurations) {
                        updateTotalDuration(identifier);
                        $('#procedureList_' + identifier).find('.durations').show();
                    }

                    // clear out text field
                    $('.js-search-autocomplete').val('');

                    // remove selection from the filter box
                    if ($('ul.add-options.js-search-results').children().length > 0) {
                        m = data.match(/<span class="value">(.*?)<\/span>/);

                        $('ul.add-options.js-search-results').children().each(function() {
                            if ($(this).text() == m[1]) {
                                let id = $(this).val();
                                let name = $(this).text();

                                removed_stack_<?php echo $identifier ?>.push({
                                    name: name,
                                    id: id
                                });

                                $(this).remove();
                            }
                        });
                    }

                    if (callback && typeof(window.callbackAddProcedure) === 'function') {
                        if (typeof procedure_id == "undefined") {
                            let m = data.match(/<input class="js-procedure" type=\"hidden\" value=\"([0-9]+)\"/);
                            procedure_id = m[1];
                        }
                        callbackAddProcedure(procedure_id);
                    }
                }
            });
        }

        $(document).ready(function() {
            new OpenEyes.UI.AdderDialog({
                id: 'procedure_popup_<?= $identifier ?: ''; ?>',
                openButton: $('#add-procedure-list-btn-<?= $identifier ?>'),
                itemSets: [
                    new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
                                                            array_map(
                                                                function ($key, $item) {
                                                                    return ['label' => $item, 'id' => $key, 'source' => 'subsections'];
                                                                },
                                                                array_keys($subsections),
                                                                $subsections
                                                            )
                                                        ) ?>, {
                        'id': 'subsections'
                    }),
                    new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
                                                            array_map(function ($key, $item) {
                                                                return ['label' => $item, 'id' => $key];
                                                            }, array_keys($procedures), $procedures)
                                                        ) ?>, {
                        'id': 'select',
                        'multiSelect': true,
                        'liClass': ' restrict-width extended'
                    })
                ],
                liClass: 'restrict-width extended',
                popupClass: 'oe-add-select-search',
                onReturn: function(adderDialog, selectedItems) {
                    //on multiselect: sort selected items alphabetically as the list could have a different display order
                    if (selectedItems.length > 1) {
                        selectedItems.sort(function(a, b) {
                            let label_a = a.label.toUpperCase();
                            let label_b = b.label.toUpperCase();

                            if (label_a > label_b) {
                                return 1;
                            } else if (label_a < label_b) {
                                return -1;
                            }

                            return 0;
                        });
                    }

                    for (let index = 0; index < selectedItems.length; index++) {
                        if (selectedItems[index]['source'] === 'subsections') {
                            continue;
                        }
                        var existingProc = document.querySelector('#procedureList_' + identifier + ' .body input[value="' + selectedItems[index]['id'] + '"]');
                        if (existingProc) {
                            continue;
                        }
                        // append selection into procedure list
                        $('#procedureList_' + identifier).find('.body').append("<tr class='item'><td class='procedure'><span class='field'><input class='js-procedure' type='hidden' value='" + selectedItems[index]['id'] + "' name='Procedures_<?= $identifier ?>[]' id='Procedures_procs'></span><span class='value'>" + selectedItems[index]['label'] + "</span></td></tr>");
                        ProcedureSelectionSelectByName(selectedItems[index]['label'], true, '<?= $identifier ?>', selectedItems[index]['id']);
                    }
                    return true;
                },
                onOpen: function() {
                    $('#procedure_popup_<?= $identifier ?: ''; ?>').find('li').each(function() {
                        let procedureId = $(this).data('id');
                        let alreadyUsed = $('#procedureList_<?= $identifier ?: ''; ?>')
                            .find('.js-procedure[value="' + procedureId + '"]').length > 0;
                        $(this).toggle(!alreadyUsed);
                    });
                },
                searchOptions: {
                    searchSource: '/procedure/autocomplete',
                    resultsFilter: function(results) {
                        let items = [];
                        $(results).each(function(index, result) {
                            let procedureMatchArray = $('#procedureList_<?= $identifier ?: ''; ?>')
                                .find('span:contains(' + result.label + ')').filter(function() {
                                    return $(this).text() === result.label;
                                });

                            if (procedureMatchArray.length === 0) {
                                items.push(result);
                            }
                        });
                        return items;
                    }
                }

            });
            initialiseProcedureAdder();
        });

        $("input[id*='_complexity_']").on('click', function() {
            let $estimated = $('#Element_OphTrOperationbooking_Operation_total_duration_procs');
            if ($estimated) {
                if (typeof updateTotalDuration === "function") {
                    updateTotalDuration('procs');
                }
            }
        });
    });
</script>