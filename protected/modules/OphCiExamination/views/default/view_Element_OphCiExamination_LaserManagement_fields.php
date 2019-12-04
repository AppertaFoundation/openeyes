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
<table class="cols-full">
  <tbody>
  <tr>
    <td>
      <div class="data-label">
            <?= CHtml::encode($element->getAttributeLabel($eye . '_laser_status_id')) ?>
      </div>
    </td>
    <td>
      <div class="data-value">
            <?= $element->{$eye . '_laser_status'} ?>
      </div>
    </td>
  </tr>
    <?php if ($element->{$eye . '_laser_status'}->deferred) : ?>
    <tr>
      <td>
        <div class="data-label">
            <?= CHtml::encode($element->getAttributeLabel($eye . '_laser_deferralreason_id')) ?>
        </div>
      </td>
      <td>
        <div class="data-value">
            <?= Yii::app()->format->Ntext($element->getLaserDeferralReasonForSide($eye)) ?>
        </div>
      </td>
    </tr>
    <?php elseif ($element->{$eye . '_laser_status'}->book || $element->{$eye . '_laser_status'}->event) : ?>
  <tr>
    <td>
      <div class="data-label">
          <?= $element->getAttributeLabel($eye . '_lasertype_id') ?>:
      </div>
    </td>
    <td>
      <div class="data-value">
          <?= Yii::app()->format->Ntext($element->getLaserTypeStringForSide($eye)) ?>
      </div>
    </td>
  </tr>
  <tr></tr>
  </tbody>
  <tbody>
  <tr>
    <td>
      <div class="data-label">
          <?= $element->getAttributeLabel($eye . '_comments') ?>:
      </div>
    </td>
    <td>
      <div class="data-value">
          <?= $element->{$eye . '_comments'} ? Yii::app()->format->Ntext($element->{$eye . '_comments'}) : 'None'; ?>
      </div>
    </td>
  </tr>
    <?php endif; ?>
  </tbody>
</table>
