<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * @var \OEModule\PatientTicketing\services\PatientTicketing_QueueSet $queueset
 */
?>
<?php $can_process = $queueset && $qs_svc->isQueueSetPermissionedForUser($queueset, Yii::app()->user->id); ?>
<main class="oe-full-main oe-vc-results">

    <div class="table-sort-order" id="table-sort-order">
        <div class="sort-by">Sort by:
            <span class="sort-options">
                <?= \CHtml::dropDownList(null, \Yii::app()->request->getParam('sort_by', 'date'), [
                        'list' => 'Queue step',
                        'patient' => 'Patient',
                        'priority' => 'Priority (Highest to Lowest)',
                        'date' => 'Date'
                ]);?>
                <span class="direction">
                    <?php $dir = \Yii::app()->request->getParam('sort_by_order') !== 'DESC';?>
                    <label class="inline highlight js-direction-up">
                        <?=\CHtml::radioButton('sort-options', $dir, [
                                'value' => ''
                        ]);?>
                        <i class="oe-i direction-up medium"></i>
                    </label>
                    <label class="inline highlight js-direction-down">
                        <?=\CHtml::radioButton('sort-options', !$dir, [
                            'value' => 'DESC'
                        ]);?>
                        <i class="oe-i direction-down medium"></i>
                    </label>
                </span>
            </span>
        </div>
        <div class="pagination"><?php $this->widget('LinkPager', ['pages' => $pagination]); ?></div>
    </div>

    <?php $flash_message = Yii::app()->user->getFlash('patient-ticketing-' . $queueset->getId()); ?>
    <?php if ($flash_message && $queueset) : ?>
        <div class="alert-box issue"><b>No tickets match that search criteria
                for <?= $queueset ? $queueset->name : $category->name; ?></b></div>
    <?php endif; ?>


    <table class="standard virtual-clinic">
        <colgroup>
            <col class="cols-icon">
            <col class="cols-1">
            <col class="cols-3">
            <col class="cols-2">
            <col class="cols-6">
        </colgroup>
        <thead>
            <tr>
                <th></th>
                <th>Step</th>
                <th>Patient</th>
                <th>Clinic &amp; Site</th>
                <th>Clinic info &amp; Notes</th>
            </tr>
        </thead>
        <tbody id="ticket-list">
        <?php
        foreach ($tickets as $i => $t) {
            $this->renderPartial('_ticketlist_row', array('i' => $i, 'ticket' => $t, 'can_process' => $can_process));
        }
        if (!count($tickets)) {
            echo "<tr><td colspan=8><div class='alert-box issue'>
                No patients found.
            </div></td><td></td></tr>";
        }
        ?>
        </tbody>
        <tfoot class="pagination-container">
        <td colspan="9">
            <?php $this->widget('LinkPager', ['pages' => $pagination]); ?>
        </td>
        </tfoot>
    </table>
</main>
<script type="text/javascript">
    $('document').ready(function(){
        let current_sort_by_value = $('#ticket_sort_by').val();
        let current_sort_by_order_value = $('#ticket_sort_by_order').val();
        let selected_sort = $('*[data-sort="' + current_sort_by_value +'"]');
        if(selected_sort.length) {
            if(current_sort_by_order_value == "DESC") {
                selected_sort.addClass('descend');
            } else {
                selected_sort.addClass('ascend');
            }
        }
    })
</script>
