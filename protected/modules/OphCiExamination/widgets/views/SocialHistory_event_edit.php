<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<script type="text/javascript" src="<?=$this->getJsPublishedPath("SocialHistory.js")?>"></script>
<div class="element-fields flex-layout full-width">
  <table class="cols-11">
    <thead>
      <tr>
        <th>Employment</th>
        <th>Driving Status</th>
        <th>Smoking Status</th>
        <th>Accommodations</th>
        <th>Comments</th>
        <th class="cols-1">Carer</th>
        <th>Alcohol Intake</th>
        <th>Substance Misuse</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <?php if (isset($element)) { ?>
        <td><?php echo $element->getDisplayOccupation(); ?></td>
        <td><?php echo $element->getDisplayDrivingStatuses(); ?></td>
        <td><?php echo $element->smoking_status? $element->smoking_status->name:''; ?></td>
        <td><?php echo $element->accommodation ? $element->accommodation->name: '';  ?></td>
        <td><?php echo $element->comments ? $element->comments:'';  ?></td>
        <td><?php echo $element->carer? $element->carer->name:''; ?></td>
        <td><?php echo $element->getDisplayAlcoholIntake(); ?></td>
        <td><?php echo $element->substance_misuse ? $element->substance_misuse->name:''; ?></td>
        <?php } ?>
      </tr>
    </tbody>
  </table>
  <div class="flex-item-bottom">
    <button class="button hint green js-add-select-search" type="button">
      <i class="oe-i plus pro-theme"></i>
    </button>
    <div id="add-to-social-history" class="oe-add-select-search auto-width" style="bottom: 61px; display: none;">
      <div class="close-icon-btn"><i class="oe-i remove-circle medium"></i></div>
      <div class="select-icon-btn"><i class="oe-i menu selected"></i></div>
      <button class="button hint green add-icon-btn"><i class="oe-i plus pro-theme"></i></button>
      <table class="select-options">
        <tr>
          <td>
            <div class="flex-layout flex-top flex-left">
              <ul class="add-options" data-multi="true" data-clickadd="false">
                  <?php
                  $occup_list = $element->occupation_options;
                  foreach ($occup_list as $occup_item) {
                      ?>
                    <li data-str="<?php echo $occup_item->name; ?>">
                      <span class="restrict-width"><?php echo $occup_item->name; ?></span>
                    </li>
                  <?php } ?>
              </ul>
            </div>
            <!-- flex layout -->
          </td>
          <td>
            <div class="flex-layout flex-top flex-left">
              <ul class="add-options" data-multi="true" data-clickadd="false">
                  <?php
                  $driving_status_list = $element->driving_statuses_options;
                  foreach ($driving_status_list as $driving_status) {
                      ?>
                    <li data-str="<?php echo $driving_status->name; ?>">
                      <span class="restrict-width"><?php echo $driving_status->name; ?></span>
                    </li>
                  <?php } ?>
              </ul>
            </div>
            <!-- flex layout -->
          </td>
          <td>
            <div class="flex-layout flex-top flex-left">
              <ul class="add-options" data-multi="true" data-clickadd="false">
                  <?php
                  $smoking_status_list = $element->smoking_status_options;
                  foreach ($smoking_status_list as $smoking_status) {
                      ?>
                    <li data-str="<?php echo $smoking_status->name; ?>">
                      <span class="restrict-width"><?php echo $smoking_status->name; ?></span>
                    </li>
                  <?php } ?>
              </ul>
            </div>
            <!-- flex layout -->
          </td>
          <td>
            <div class="flex-layout flex-top flex-left">
              <ul class="add-options" data-multi="true" data-clickadd="false">
                  <?php
                  $accommodation_options = $element->accommodation_options;
                  foreach ($accommodation_options as $accommodation_item) { ?>
                    <li data-str="<?php echo $accommodation_item->name; ?>">
                      <span class="restrict-width"><?php echo $accommodation_item->name; ?></span>
                    </li>
                  <?php } ?>
              </ul>
            </div>
            <!-- flex layout -->
          </td>
          <td>
            <div class="flex-layout flex-top flex-left">
              <ul class="add-options" data-multi="true" data-clickadd="false">
                  <?php
                  $carer_options = $element->carer_options;
                  foreach ($carer_options as $carer_item) { ?>
                    <li data-str="<?php echo $carer_item->name; ?>">
                      <span class="restrict-width"><?php echo $carer_item->name; ?></span>
                    </li>
                  <?php } ?>
              </ul>
            </div>
            <!-- flex layout -->
          </td>
          <td>
            <div class="flex-layout flex-top flex-left">
              <ul class="add-options" data-multi="true" data-clickadd="false">
                  <?php
                  $substance_misuse_options = $element->substance_misuse_options;
                  foreach ($substance_misuse_options as $substance_item) { ?>
                    <li data-str="<?php echo $substance_item->name; ?>">
                      <span class="restrict-width"><?php echo $substance_item->name; ?></span>
                    </li>
                  <?php } ?>
              </ul>
            </div>
            <!-- flex layout -->
          </td>
        </tr>
      </table>
      <div class="search-icon-btn"><i class="oe-i search"></i></div>
      <div class="search-options" style="display:none;">
        <input type="text" class="cols-full js-search-autocomplete" placeholder="search for option (type 'auto-complete' to demo)">
        <!-- ajax auto-complete results, height is limited -->
      </div>
    </div>
  </div>
</div>
