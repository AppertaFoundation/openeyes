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

    <?php foreach (['left' => 'right', 'right' => 'left'] as $page_isde => $eye_side) : ?>
      <div class="<?= $eye_side ?>-eye">

          <?php if ($element->{$eye_side . '_field_id'}) :
                $eye_test = $element->{$eye_side . '_field'}; ?>
            <a class="OphInVisualfields_field_image" data-image-id="<?= $eye_test->image_id ?>" href="#">
              <img src="<?php echo '/file/view/' . $eye_test->cropped_image_id . '/400/img.gif'; ?>">
            </a>

            <?php endif; ?>

        <table class="label-value cols-10">
          <tbody>
          <tr>
            <td>
              <div class="data-label">Date</div>
            </td>
            <td>
                <?php if ($element->{$eye_side . '_field_id'}) : ?>
                  <div class="data-value">
                      <?php echo $element->{$eye_side . '_field'}->study_datetime ?>
                  </div>
                <?php endif; ?>
            </td>
          </tr>
          <tr>
            <td>
              <div class="data-label">Strategy</div>
            </td>
            <td>
                <?php if ($element->{$eye_side . '_field_id'}) : ?>
                  <div class="data-value">
                      <?php echo $element->{$eye_side . '_field'}->strategy->name ?>
                  </div>
                <?php endif; ?>
            </td>
          </tr>
          <tr>
            <td>
              <div class="data-label">Test Name</div>
            </td>
            <td>
                <?php if ($element->{$eye_side . '_field_id'}) : ?>
                  <div class="data-value">
                      <?php echo $element->{$eye_side . '_field'}->pattern->name ?>
                  </div>
                <?php endif; ?>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    <?php endforeach; ?>
</div>
