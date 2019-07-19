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

<section class="element view full">
  <header class="element-header">
    <h3 class="element-title">Personnel</h3>
  </header>
  <div class="element-data full-width">

    <div class="data-group">
      <div class="data-value flex-layout flex-top">
        <div class="cols-11">
          <div class="cols-11" id="js-listview-info-pro" style="">
            <ul class="dot-list large-text">
              <li><?php echo $element->surgeon->fullNameAndTitle ?></li>
              <li>
                <span class="fade">
                    <?=\CHtml::encode($element->getAttributeLabel('assistant_id')) ?>
                  : <?php echo $element->assistant ? $element->assistant->fullNameAndTitle : 'None' ?>
                </span>
              </li>
              <li>
                <span class="fade">
                    <?=\CHtml::encode($element->getAttributeLabel('supervising_surgeon_id')) ?>
                  : <?php echo $element->supervising_surgeon ? $element->supervising_surgeon->fullNameAndTitle : 'None' ?>
                </span>
              </li>
            </ul>
          </div>

          <div class="col-6 data-group" id="js-listview-info-full" style="display: none">
            <table class="last-left">
              <colgroup>
                <col class="cols-4">
                <col class="cols-4">
                <col class="cols-4">
              </colgroup>
              <thead>
              <tr>
                <th><?=\CHtml::encode($element->getAttributeLabel('surgeon_id')) ?></th>
                <th><?=\CHtml::encode($element->getAttributeLabel('assistant_id')) ?></th>
                <th><?=\CHtml::encode($element->getAttributeLabel('supervising_surgeon_id')) ?></th>
              </tr>
              </thead>
              <tbody>
              <tr>
                <td>
                  <span class="large-text">
                        <?php echo $element->surgeon->fullNameAndTitle ?>
                  </span>
                </td>
                <td>
                  <span class="large-text">
                        <?php echo $element->assistant ? $element->assistant->fullNameAndTitle : 'None' ?>
                  </span>
                </td>
                <td>
                  <span class="large-text">
                        <?php echo $element->supervising_surgeon ? $element->supervising_surgeon->fullNameAndTitle : 'None' ?>
                  </span>
                </td>
              </tr>
              </tbody>
            </table>
          </div>

        </div>

        <div>
          <i class="oe-i small expand js-listview-expand-btn" data-list="info"></i>
        </div>

      </div>
    </div>
  </div>
</section>