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
/**
 * @var $episodes Episode[]
 */
$episodes = $this->episode->patient->episodes
;?>
<div class="element-data full-width">
    <?php
    if (!$element->id && !$element->no_ophthalmic_diagnoses_date) { ?>
        <div class="data-value not-recorded">
            Nil recorded this examination
        </div>
    <?php } elseif (isset($element->no_ophthalmic_diagnoses_date)){ ?>
        <div class="data-value">
            Patient has no known Ophthalmic Diagnoses
        </div>
    <?php } else { ?>
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
                <?php $this->widget('EyeLateralityWidget', array('eye' => $principal->eye)) ?>
            </td>
            <td><span class="oe-date"><?= $principal->getHTMLformatedDate() ?></span></td>
              <td>
                  <small>at</small>
                  <?= $principal->time ?>
              </td>
          </tr>
            <?php }
            foreach ($episodes as $episode) {
                if ($episode->id != $this->episode->id && $episode->diagnosis ) {
                    ?>
              <tr>
                <td>
                    <?= $episode->diagnosis->term ?>
                  <span class="js-has-tooltip oe-i info small"
                        data-tooltip-content="Principal diagnosis for <?= $episode->getSubspecialtyText(); ?>"></span>
                </td>
                <td>
                    <?php $this->widget('EyeLateralityWidget', array('eye' => $episode->eye)) ?>
                </td>
                    <?php $date = $episode->getDisplayDate(); ?>
                    <?php if ($date) { ?>
                  <td><span class="oe-date"><?= Helper::convertDate2HTML($date) ?></span></td>
                    <?php } else { ?>
                    <td></td>
                    <?php } ?>
                  <td>
                    <?php if ($episode->disorder_time) { ?>
                        <small>at</small>
                        <?= $episode->getDisplayTime() ?>
                    <?php } ?>
                  </td>
              </tr>
                <?php }
            }
            $diagnoses = \OEModule\OphCiExamination\models\OphCiExamination_Diagnosis::model()
            ->findAll(["condition" => "element_diagnoses_id=:id and principal=0", "order" => "date desc", "params" => [":id"=>$element->id] ]);
            foreach ($diagnoses as $diagnosis) { ?>
          <tr>
            <td>
                    <?php echo $diagnosis->disorder->term ?>
            </td>
            <td>
                    <?php $this->widget('EyeLateralityWidget', array('eye' => $diagnosis->eye)) ?>
            </td>
            <td><span class="oe-date"><?= $diagnosis->getHTMLformatedDate() ?></span></td>
              <td>
                  <small>at</small>
                  <?= $diagnosis->time ?>
              </td>
          </tr>
            <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
        <?php } ?>
</div>

