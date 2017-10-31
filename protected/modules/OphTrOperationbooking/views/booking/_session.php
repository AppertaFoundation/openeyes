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
if (!empty($operation->booking)) {
    $session = $operation->booking->session;
    ?>
  <div class="data">

      <span style="display:inline-block; width:160px;">Firm:</span><strong><?php echo CHtml::encode($session->FirmName); ?></strong><br>
      <span style="display:inline-block; width:160px;">Location:</span><strong><?php echo CHtml::encode($session->TheatreName); ?></strong><br>
      <span style="display:inline-block; width:160px;">Date of operation:</span><strong><?php echo $session->NHSDate('date'); ?></strong><br>
      <span style="display:inline-block; width:160px;">Session time:</span><strong><?php echo substr($session->start_time, 0, 5).' - '.substr($session->end_time, 0, 5); ?></strong><br>
      <span style="display:inline-block; width:160px;">Admission time:</span><strong><?php echo substr($operation->booking->admission_time, 0, 5); ?></strong> <br>

      <span style="display:inline-block; width:160px;">Duration of operation:</span><strong><?php echo $operation->total_duration.' minutes'; ?></strong>
  </div>
<?php

}
?>
