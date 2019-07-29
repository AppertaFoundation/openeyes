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
<div class="element-data element-eyes">
    <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) : ?>
      <div class="js-element-eye <?= $eye_side ?>-eye column">
          <?php if ($element->{$eye_side . '_rapd'} === '1') : ?>
              <div class="data-value">
                <span class="large-text">
                    RAPD present
                </span>
              </div>
            <?php endif; ?>
          <div class="data-value">
            <span class="large-text">
             <?php if ($element->hasEye($eye_side) && $element->{$eye_side . '_abnormality'}) {
                    echo $element->{$eye_side . '_abnormality'}->name;
             } else {
                    ?>
               Not recorded
                    <?php
             } ?>
            </span>
          </div>
          <?php if ($element->{$eye_side . '_comments'}) : ?>
            <div class="data-value">
                <?= Yii::app()->format->Ntext($element->{$eye_side . '_comments'}) ?>
            </div>
            <?php endif; ?>
      </div>
    <?php endforeach; ?>
</div>
