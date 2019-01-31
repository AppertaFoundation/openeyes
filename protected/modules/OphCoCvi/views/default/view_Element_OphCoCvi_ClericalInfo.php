<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>

<div class="element-data">
  <div class="element-fields">
    <div class="cols-12 column">
      <table>
        <thead>
        <tr>
          <th>Other relevant factors about the patient</th>
          <th>Y/N</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $model = OEModule\OphCoCvi\models\Element_OphCoCvi_ClericalInfo::model();
        ?>
        <?php foreach ($model->patientFactorList($element->id) as $factor) {
            $is_factor = $factor['is_factor'];
            $comments = $factor['comments']; ?>
          <tr>
            <td><?=\CHtml::encode($factor['name']) ?>
                <?php if ($factor['is_comments'] == 1) { ?>
                  <div class="data-group"><br/>
                    <div class="cols-4 column large-push-1"
                         style="font-style: italic;"><?=\CHtml::encode($factor['label']) ?></div>
                    <div class="cols-6 column large-push-1 end"><?php echo $comments; ?></div>
                  </div>
                <?php } ?>
            </td>
            <td><?php if (isset($is_factor) && $is_factor == 1) {
                    echo "Y";
                } elseif (isset($is_factor) && $is_factor == 0) {
                    echo "N";
                } elseif (isset($is_factor) && $is_factor == 2) {
                    echo "Unknown";
                } else {
                    echo "None";
                } ?></td>
          </tr>
        <?php } ?>
        <tr>
          <td>
              <?=\CHtml::encode($element->getAttributeLabel('employment_status_id')) ?>
          </td>
          <td>
              <?php echo $element->employment_status ? $element->employment_status->name : 'None' ?>
          </td>
        </tr>
        <tr>
          <td>
              <?=\CHtml::encode($element->getAttributeLabel('preferred_info_fmt_id')) ?>
          </td>
          <td>
              <?php echo $element->preferred_info_fmt ? $element->preferred_info_fmt->name : 'None' ?>
          </td>
        </tr>
        <?php
        $preferredInfoFormatEmail = OEModule\OphCoCvi\models\OphCoCvi_ClericalInfo_PreferredInfoFmt::model()->findAll('`require_email` = ?',
            array(1));
        if (sizeof($preferredInfoFormatEmail) == 1) {
            ?>
          <tr>
            <td>
              <div class="data-label"><?=\CHtml::encode($element->getAttributeLabel('info_email')) ?></div>
            </td>
            <td>
              <div class="data-value"><?=\CHtml::encode($element->info_email) ?></div>
            </td>
          </tr>
        <?php } ?>
        <tr>
          <td>
              <?=\CHtml::encode($element->getAttributeLabel('contact_urgency_id')) ?>
          </td>
          <td>
              <?php echo $element->contact_urgency ? $element->contact_urgency->name : 'None' ?>
          </td>
        </tr>
        <tr>
          <td>
              <?=\CHtml::encode($element->getAttributeLabel('preferred_language_id')) ?>
          </td>
          <td>
              <?php
              if ($element->preferred_language_text) {
                  echo $element->preferred_language_text;
              } else {
                  echo $element->preferred_language ? $element->preferred_language->name : 'None';
              }
              ?>
          </td>
        </tr>
        <tr>
          <td>
              <?=\CHtml::encode($element->getAttributeLabel('social_service_comments')) ?>
          </td>
          <td>
              <?=\CHtml::encode($element->social_service_comments) ?>
          </td>
        </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

