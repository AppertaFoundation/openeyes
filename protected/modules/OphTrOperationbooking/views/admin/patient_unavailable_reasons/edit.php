<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<div class="cols-5">
    <div class="row divider">
        <h2><?= $reason->id ? 'Edit ' : 'Add '?>Patient Unavailable Reason</h2>
    </div>

    <?php
    $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => 'adminform',
        'enableAjaxValidation' => false,
        'focus' => '#OphTrOperationbooking_ScheduleOperation_PatientUnavailableReason_name',
        'layoutColumns' => array(
            'label' => 2,
            'field' => 5,
        ),
    ))?>

    <?php echo $form->errorSummary($reason); ?>

    <table class="standard cols-full" id="finding-table">
        <colgroup>
            <col class="cols-1">
            <col class="cols-3">
        </colgroup>
        <tbody>
        <tr>
            <td><?=$reason->getAttributeLabel('name')?></td>
            <td>
                <?=\CHtml::activeTextField(
                    $reason,
                    'name',
                    ['class' => 'cols-full']
                ); ?>
            </td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="2">
                <?php echo $form->formActions(); ?>
            </td>
        </tr>
        </tfoot>
    </table>

    <?php $this->endWidget() ?>

</div>

<script type="text/javascript">
    handleButton($('#et_cancel'),function(e) {
        e.preventDefault();
        window.location.href = baseUrl+'/OphTrOperationbooking/admin/viewPatientUnavailableReasons';
    });
    handleButton($('#et_save'),function(e) {
        $('#adminform').submit();
    });
</script>
