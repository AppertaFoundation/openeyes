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
  <section class="element-fields full-width">

    <div class="eyedraw flex-layout flex-top">
      <div class="eyedraw-canvas">
            <?php
            $this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
              'mode' => 'view',
              'width' => $this->action->id === 'view' ? 200 : 120,
              'height' => $this->action->id === 'view' ? 200 : 120,
              'model' => $element,
              'attribute' => 'eyedraw',
              'scale' => 0.72,
              'idSuffix' => 'GlaucomaTube',
            ));
            ?>
      </div>

      <div class="eyedraw-data cols-5">
        <table class="label-value no-lines last-left">
          <colgroup>
            <col class="cols-5">
          </colgroup>
          <tbody>
          <tr>
            <td>
              <div class="data-label">Incision site:</div>
            </td>
            <td>
              <div class="data-value">Corneal</div>
            </td>
          <tr>
            <td>
              <div class="data-label"><?= CHtml::encode($element->getAttributeLabel('plate_position_id')) ?>:</div>
            </td>
            <td>
              <div class="data-value"><?= $element->plate_position->name ?></div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="data-label"><?= CHtml::encode($element->getAttributeLabel('plate_limbus')) ?></div>
            </td>
            <td>
              <div class="data-value"><?= $element->plate_limbus ?> mm</div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="data-label"><?= CHtml::encode($element->getAttributeLabel('tube_position_id')) ?></div>
            </td>
            <td>
              <div class="data-value"><?= $element->tube_position->name ?></div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="data-label"><?= CHtml::encode($element->getAttributeLabel('stent')) ?></div>
            </td>
            <td>
              <div class="data-value"><?= $element->stent ? 'Yes' : 'No'; ?></div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="data-label"><?= CHtml::encode($element->getAttributeLabel('slit')) ?></div>
            </td>
            <td>
              <div class="data-value"><?= $element->slit ? 'Yes' : 'No'; ?></div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="data-label"><?= CHtml::encode($element->getAttributeLabel('visco_in_ac')) ?></div>
            </td>
            <td>
              <div class="data-value"><?= $element->visco_in_ac ? 'Yes' : 'No'; ?></div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="data-label"><?= CHtml::encode($element->getAttributeLabel('flow_tested')) ?></div>
            </td>
            <td>
              <div class="data-value"><?= $element->flow_tested ? 'Yes' : 'No'; ?></div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="data-label"><?= CHtml::encode($element->getAttributeLabel('description')) ?></div>
            </td>
            <td>
              <div class="data-value"><?= Yii::app()->format->Ntext($element->description) ?></div>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</section>
