<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<div class="align-right cvi-alert alert-box issue"
     data-alert="CVI may be applicable"
     data-threshold="<?= $threshold ?>"
     style="<?php if (!$visible): ?>display: none;<?php endif; ?>">
  This patient may be eligible for a CVI
  <i class="oe-i info pad-left small js-has-tooltip"
     data-tooltip-content="You'll be able to create a CVI after saving this Examination"></i>
    <?php if ($show_create): ?>
      <a class="button create-cvi hint green"
         href="<?php echo Yii::app()->createUrl('/OphCoCvi/Default/create',
             array('patient_id' => $this->patient->id)); ?>">
        Create CVI
      </a>
    <?php endif; ?>
  <i class="oe-i remove-circle small dismiss_cva_alert dismiss right"></i>
</div>

