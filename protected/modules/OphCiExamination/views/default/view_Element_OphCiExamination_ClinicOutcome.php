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
<div class="element-data full-width">
    <div class="data-group">
      <span class="large-text">
      <?php if ($element->status->name !== 'Follow-up') {
          echo $element->status;
      } ?>

          <?php if ($element->status && $element->status->followup) { ?>
              <?php if ($element->status->name !== 'Follow-up'): ?>in<?php endif; ?>
              <?php echo $element->getFollowUp() ?>
              with
              <?php echo $element->role->name ?>
              <?php if ($element->role_comments) { ?>
                  (<?= Yii::app()->format->Ntext($element->role_comments) ?>)
              <?php } ?>
          <?php } ?>
          <?php if ($api = Yii::app()->moduleAPI->get('PatientTicketing')) {
              if ($element->status && $element->status->patientticket &&
                  $ticket = $api->getTicketForEvent($this->event)
              ) {
                  ?>
                  <div class="cols-7">
                <?php $this->widget($api::$TICKET_SUMMARY_WIDGET, array('ticket' => $ticket)); ?>
            </div>
                  <?php
              }
          } ?>
      </span>
    </div>
    <?php if ($element->description) { ?>
        <div class="data-group">
            <span class="large-text">
                <?= $element->getAttributeLabel('description') ?>:
                <?= Yii::app()->format->Ntext($element->description); ?>
            </span>
        </div>
    <?php } ?>

</div>
