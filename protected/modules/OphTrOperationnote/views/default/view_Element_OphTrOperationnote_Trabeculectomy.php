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

<section class="element view">
  <header class="element-header">
    <h3 class="element-title"><?php echo $element->elementType->name ?></h3>
  </header>
  <div class="element-fields full-width">

    <div class="eyedraw flex-layout">
        <?php
        $this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
          'idSuffix' => 'Trabeculectomy',
          'side' => $element->eye->getShortName(),
          'mode' => 'view',
          'width' => 250,
          'height' => 250,
          'scale' => 0.72,
          'model' => $element,
          'attribute' => 'eyedraw',
          'idSuffix' => 'Trabeculectomy',
        ));
        ?>
      <div class="eyedraw-data">
        <table class="label-value no-lines last-left">
          <colgroup>
            <col class="cols-4">
          </colgroup>
          <tbody>
          <tr>
            <td>
              <div class="data-label">
                    <?=\CHtml::encode($element->getAttributeLabel('conjunctival_flap_type_id')) ?>
              </div>
            </td>
            <td>
              <div class="data-value"><?php echo $element->conjunctival_flap_type->name ?></div>
            </td>
          </tr>

          <tr>
            <td>
              <div class="data-label"><?=\CHtml::encode($element->getAttributeLabel('stay_suture')) ?>:</div>
            </td>
            <td>
              <div class="data-value"><?php echo $element->stay_suture ? 'Yes' : 'No' ?></div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="data-label"><?=\CHtml::encode($element->getAttributeLabel('site_id')) ?>:</div>
            </td>
            <td>
              <div class="data-value"><?php echo $element->site ? $element->site->name : 'None' ?></div>
            </td>
          </tr>

          <tr>
            <td>
              <div class="data-label"><?=\CHtml::encode($element->getAttributeLabel('size_id')) ?></div>
            </td>
            <td>
              <div class="data-value"><?php echo $element->size ? $element->size->name : 'None' ?></div>
            </td>
          </tr>

          <tr>
            <td>
              <div class="data-label">
                    <?=\CHtml::encode($element->getAttributeLabel('sclerostomy_type_id')) ?>
              </div>
            </td>
            <td>
              <div class="data-value">
                    <?php echo $element->sclerostomy_type ? $element->sclerostomy_type->name : 'None' ?>
              </div>
            </td>
          </tr>

          <tr>
            <td>
              <div class="data-label">
                    <?=\CHtml::encode($element->getAttributeLabel('viscoelastic_type_id')) ?>
              </div>
            </td>
            <td>
              <div class="data-value">
                    <?php echo $element->viscoelastic_type ? $element->viscoelastic_type->name : 'None' ?>
              </div>
            </td>
          </tr>

          <tr>
            <td>
              <div class="data-label">
                    <?=\CHtml::encode($element->getAttributeLabel('viscoelastic_removed')) ?>
              </div>
            </td>
            <td>
              <div class="data-value"><?php echo $element->viscoelastic_removed ? 'Yes' : 'No' ?></div>
            </td>
          </tr>

          <tr>
            <td>
              <div class="data-label">
                    <?=\CHtml::encode($element->getAttributeLabel('viscoelastic_flow_id')) ?>
              </div>
            </td>
            <td>
              <div class="data-value">
                    <?php echo $element->viscoelastic_flow ? $element->viscoelastic_flow->name : 'None' ?>
              </div>
            </td>
          </tr>

          <tr>
            <td>
              <div class="data-label">Trabeculectomy report</div>
            </td>
            <td>
              <div class="data-value">
                    <?php foreach (explode(chr(10), CHtml::encode($element->report)) as $line) { ?>
                        <?php echo $line ?>
                    <?php } ?>
              </div>
            </td>
          </tr>

          <tr>
              <td>
                  <div class="data-label">Comments</div>
              </td>
              <td>
                  <div class="data-value">
                      <?= CHtml::encode($element->comments); ?>
                  </div>
              </td>
          </tr>

          <tr>
            <td>
              <div class="data-label">Difficulties</div>
            </td>
            <td>
              <div class="data-value">
                    <?php if (!$element->difficulties) { ?>
                    None
                    <?php } else { ?>
                        <?php foreach ($element->difficulties as $difficulty) { ?>
                            <?php if ($difficulty->name == 'Other') {?>
                                <?php echo preg_replace("/[\n]/", ', ', $element->difficulty_other) ?>
                            <?php } else { ?>
                                <?php echo $difficulty->name?>,
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
              </div>
            </td>
          </tr>

          <tr>
            <td>
              <div class="data-label">Complications</div>
            </td>
            <td>
              <div class="data-value">
                    <?php if (!$element->complications) { ?>
                    None
                    <?php } else { ?>
                        <?php foreach ($element->complications as $complication) { ?>
                            <?php if ($complication->name == 'Other') { ?>
                                <?php echo preg_replace("/[\n]/", ', ', $element->complication_other) ?>
                            <?php } else { ?>
                                <?php echo $complication->name ?>,
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
              </div>
            </td>
          </tr>

          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>
