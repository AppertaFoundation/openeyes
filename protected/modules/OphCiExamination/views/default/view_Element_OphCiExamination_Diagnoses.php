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
use OEModule\OphCiExamination\components\ExaminationHelper;

?>
<?php
$principals = $this->episode->patient->episodes;
 ?>
<div class="element-data">
  <div class="data-value">
    <div class="tile-data-overflow">
      <table>
        <colgroup>
          <col>
          <col width="55px">
          <col width="85px">
        </colgroup>
        <tbody>
        <?php
        $principal = OEModule\OphCiExamination\models\OphCiExamination_Diagnosis::model()
            ->find('element_diagnoses_id=? and principal=1', array($element->id));
        if ($principal) {
            ?>
          <tr>
            <td>
              <strong>
                  <?php echo $principal->disorder->term ?>
              </strong>
            </td>
            <td>
              <i class="oe-i laterality <?php echo in_array($principal->eye_id, array(\Eye::RIGHT, \Eye::BOTH)) ? 'R': 'NA' ?> small pad"></i>
              <i class="oe-i laterality <?php echo in_array($principal->eye_id, array(\Eye::LEFT, \Eye::BOTH)) ? 'L': 'NA' ?> small pad"></i>
            </td>
            <td></td>
          </tr>
        <?php }
        foreach ($principals as $principal) {
            if ($principal->id != $this->episode->id && $principal->diagnosis ) {
                ?>
              <tr>
                <td>
                    <?= $principal->diagnosis->term ?>
                  <span class="js-has-tooltip oe-i info"
                        data-tooltip-content="Principal diagnosis for <?= $principal->getSubspecialtyText(); ?>"></span>
                </td>
                <td>
                  <i class="oe-i laterality <?php echo in_array($principal->eye_id, array(\Eye::RIGHT, \Eye::BOTH)) ? 'R': 'NA' ?> small pad"></i>
                  <i class="oe-i laterality <?php echo in_array($principal->eye_id, array(\Eye::LEFT, \Eye::BOTH)) ? 'L': 'NA' ?> small pad"></i>
                </td>
                <td>
                    <?php echo $principal->NHSDate('start_date'); ?>
                </td>
              </tr>
            <?php }
        }
        $diagnoses = \OEModule\OphCiExamination\models\OphCiExamination_Diagnosis::model()
            ->findAll('element_diagnoses_id=? and principal=0 ', array($element->id));
        foreach ($diagnoses as $diagnosis) { ?>
          <tr>
            <td>
                <?php echo $diagnosis->disorder->term ?>
            </td>
            <td>
              <i class="oe-i laterality <?php echo in_array($diagnosis->eye_id, array(\Eye::RIGHT, \Eye::BOTH)) ? 'R': 'NA' ?> small pad"></i>
              <i class="oe-i laterality <?php echo in_array($diagnosis->eye_id, array(\Eye::LEFT, \Eye::BOTH)) ? 'L': 'NA' ?> small pad"></i>
            </td>
            <td></td>
          </tr>
        <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

