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
?>
<?php
// get list of extra procedures
$extraProce = ProcedureExtra::model()->findAll();
$extraProceArray = CHtml::listData($extraProce, 'id', 'term');
$durations = '';
$extraProceJson = CJSON::encode(
    array_map(function ($key, $item) {
        return ['label' => $item, 'id' => $key];
    }, array_keys($procedures), $procedures)
);
echo CHtml::hiddenField(CHtml::modelName($element), 'value', ['id' => 'modelClass']);
?>
<div class="flex-layout procedure-selection eventDetail<?= $last ? 'eventDetailLast' : ''; ?>" id="typeProcedure" style="<?= $hidden ? 'display: none;' : ''; ?>">
    <?php if ($label && sizeof($label)) { ?>
        <div class="cols-2">
            <label for="select_procedure_id_additional">
                <?php echo $label; ?>
            </label>
        </div>
    <?php } ?>
    <?php $totalDuration = 0; ?>

    <table class="cols-10" id="procedureList_additional" style="<?= empty($selected_procedures) ? 'visibility: hidden;' : ''; ?>">
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
                                <span data-default-duration="<?= $procedure->default_duration; ?>">
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
                        <span id="projected_duration_additional">
                            <span><?= \CHtml::encode($totalDuration); ?></span> mins
                        </span>
                        <span class="fade">(calculated)</span>
                    </td>
                    <?php if ($showEstimatedDuration) { ?>
                        <td class="align-left">
                            <input type="text" value="<?= $adjusted_total_duration; ?>" id="<?= $class; ?>_total_duration_<?= $identifier; ?>" name="<?= $class; ?>[total_duration_<?= $identifier; ?>]" style="width:60px" data-total-duration="<?= $total_duration; ?>" />
                            <span class="fade">mins (estimated)</span>
                        </td>
                    <?php } ?>
                </tr>
            </tfoot>

        <?php } ?>
    </table>
    <div class="add-data-actions flex-item-bottom">
        <button class="button hint green add-entry" type="button" id="add-procedure-list-btn-<?= $identifier; ?>">
            <i class="oe-i plus pro-theme"></i>
        </button>
    </div>
</div>

<script type="text/javascript">
    let extraProceJson = <?= $extraProceJson; ?>;
    let classs = $('#modelClass').attr('name');

    function updateProcedureDialog(subsection) {
        if (subsection !== '') {
            $.ajax({
                'url': 'ProcedureExtra/list',
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
            //$subspecialty_procedures = ProcedureSubspecialtyAssignment::model()->getProcedureListFromSubspecialty($subspecialty_id);
            $formatted_procedures = '';
            foreach ($extraProceArray as $proc_id => $subspecialty_procedure) {
                $formatted_procedures .= "<li data-label='$subspecialty_procedure'data-id='$proc_id' class=''>" .
                    "<span class='auto-width'>$subspecialty_procedure</span></li>";
            }
            ?>
            $('.add-options[data-id="select"]').each(function() {
                $(this).html("<?= $formatted_procedures; ?>");
                $(this).show();
            });
        }
    }
    $("input[id*='_complexity_']").on('click', function() {
        let $estimated = $('#Element_OphTrOperationbooking_Operation_total_duration_procs');
        if ($estimated) {
            if (typeof updateTotalDuration === "function") {
                updateTotalDuration('procs');
            }
        }
    });
</script>