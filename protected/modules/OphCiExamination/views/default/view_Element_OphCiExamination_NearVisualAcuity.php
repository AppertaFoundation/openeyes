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
      <div class="js-element-eye <?= $eye_side ?>-eye column" data-test="near-visual-acuity-<?= $eye_side ?>-eye">
          <?php if ($element->hasEye($eye_side)) : ?>
            <div class="data-value">
                <?php if ($element->getCombined($eye_side)) :
                    echo $element->unit->name ?>
              <span class="priority-text" data-test="combined-near-visual-acuity-data">
                    <?php echo $element->getCombined($eye_side) ?>
              </span>
                    <?php echo $this->renderPartial(
                        '_visual_acuity_tooltip',
                        array('element' => $element, 'side' => $eye_side, 'is_near' => true)
                    ); ?>
                <?php else : ?>
                  Not recorded
                    <?php if ($element->{$eye_side . '_unable_to_assess'}) : ?>
                      (Unable to assess<?php if ($element->{$eye_side . '_eye_missing'}) :
                            ?>, eye missing<?php
                                       endif; ?>)
                    <?php elseif ($element->{$eye_side . '_eye_missing'}) : ?>
                      (Eye missing)
                    <?php endif; ?>
                <?php endif; ?>
            </div>
              <div>
                  <?php if ($element->{$eye_side . '_notes'}) : ?>
                        <?= $element->textWithLineBreaks($eye_side . '_notes') ?>
                    <?php endif; ?>
              </div>
            <?php else : ?>
            <div class="data-value not-recorded">
              Not recorded
            </div>
            <?php endif; ?>
      </div>
    <?php endforeach; ?>
</div>