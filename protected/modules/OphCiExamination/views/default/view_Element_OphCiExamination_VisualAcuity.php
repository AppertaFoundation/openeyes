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
<?php

$all_units = $element->getUnits($element->unit->id, false);
$va_tooltip_right = "";
$va_tooltip_left = "";
foreach($all_units as $unit) {
    $va_tooltip_right.='<b>'.$unit->name.'</b>:<br/> '.$element->getCombined('right', $unit->id).'<br/>';
    $va_tooltip_left.='<b>'.$unit->name.'</b>:<br/> '.$element->getCombined('left', $unit->id).'<br/>';
}


$cvi_api = Yii::app()->moduleAPI->get('OphCoCvi');
if ($cvi_api) {
    echo $cvi_api->renderAlertForVA($this->patient, $element, true);
}

?>

<?php echo CHtml::hiddenField('element_id', $element->id, array('class' => 'element_id')); ?>

<div class="element-data element-eyes">
    <?php foreach (array('left' => 'right', 'right' => 'left') as $page_side => $eye_side): ?>
      <div class="element-eye <?= $eye_side ?>-eye">
          <?php if ($element->hasEye($eye_side)) {
              ?>
              <?php if ($element->getCombined($eye_side)) {
                  ?>
              <div class="data-row">
                <div class="data-value">
                  <span class="priority-text">
                    <?php echo $element->getCombined($eye_side) ?>
                  </span>
                </div>
              </div>

                  <?php
              } else {
                  ?>
              <div class="data-row">
                <div class="data-value">
                  Not recorded
                    <?php if ($element->{$eye_side . '_unable_to_assess'}) {
                        ?>
                      (Unable to assess<?php if ($element->{$eye_side . '_eye_missing'}) {
                            ?>, eye missing<?php
                        }
                        ?>)
                        <?php
                    } elseif ($element->{$eye_side . '_eye_missing'}) {
                        ?>
                      (Eye missing)
                        <?php
                    }
                    ?>
                </div>
              </div>
                  <?php
              }
              ?>
              <?php
          } else {
              ?>
            <div class="data-row">
              <div class="data-value">
                Not recorded
              </div>
            </div>
              <?php
          } ?>
      </div>
    <?php endforeach; ?>
</div>
