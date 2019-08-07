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
<div class="element-data element-eyes">
    <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) : ?>
      <div class="js-element-eye <?= $eye_side ?>-eye">
        <div class="data-group">
            <?php if ($element->hasEye($eye_side)) : ?>
              <div class="eyedraw flex-layout flex-top anterior-segment">
                <div class="eyedraw-canvas">
                    <?php
                    $this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
                        'idSuffix' => $eye_side . '_' . $element->elementType->id,
                        'side' => ($eye_side === 'right') ? 'R' : 'L',
                        'mode' => 'view',
                        'width' => 200,
                        'height' => 200,
                        'model' => $element,
                        'attribute' => $eye_side . '_eyedraw',
                    )); ?>
                </div>
              </div>
            <?php else : ?>
              <div class="data-value not-recorded">Not recorded</div>
            <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
</div>