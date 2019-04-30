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
<main class="oe-vc-results">

    <?php $flash_message = Yii::app()->user->getFlash('patient-ticketing-' . $queueset->getId()); ?>
    <?php if ($flash_message && $queueset) : ?>
        <div class="alert-box issue"><b>No tickets match that search criteria for <?=$queueset ? $queueset->name : $category->name;?></b></div>
    <?php endif; ?>

  <table class="standard virtual-clinic">
    <colgroup>
      <col>
      <col class="cols-2"> <!-- patient -->
      <col>
      <col class="cols-1">
      <col class="cols-1">
      <col class="cols-2"><!-- clinical info -->
      <col class="cols-4"><!-- referral notes -->
      <col>
    </colgroup>
    <thead>
    <tr>
      <th>List</th>
      <th>Patient</th>
      <th><i class="oe-i arrow-down-bold small pad active"></i></th>
      <th><i class="oe-i arrow-down-bold small pad active"></i></th>
      <th>Context</th>
      <th>Clinic Info</th>
      <th>Notes</th>
      <!--<th>Ticket Owner</th>-->
      <th></th>
    </tr>
    </thead>
    <tbody id="ticket-list">
    <?php foreach ($tickets as $i => $t) {
        $this->renderPartial('_ticketlist_row', array('i' => $i, 'ticket' => $t, 'can_process' => $can_process));
    } ?>
    </tbody>
    <tfoot class="pagination-container">
        <td colspan="9">
            <?php $this->widget('LinkPager', ['pages' => $pagination]); ?>
        </td>
    </tfoot>
  </table>
</main>
