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
    <h3 class="element-title"><?= $element->elementType->name ?></h3>
  </header>
  <section class="element-fields full-width">
    <div class="eyedraw flex-layout flex-top flex-left">
      <div class="eyedraw-canvas">
          <?php
          $this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
              'side' => $element->eye->getShortName(),
              'mode' => 'view',
              'width' => $this->action->id === 'view' ? 200 : 120,
              'height' => $this->action->id === 'view' ? 200 : 120,
              'model' => $element,
              'attribute' => 'eyedraw',
              'idSuffix' => 'Vitrectomy',
          ));
          ?>
      </div>

      <div class="eyedraw-data">
        <table class="label-value no-lines last-left">
          <colgroup>
            <col class="cols-5">
          </colgroup>
          <tbody>
          <tr>
            <td>
              <div class="data-label">
                  <?= CHtml::encode($element->getAttributeLabel('gauge_id')) ?>
              </div>
            </td>
            <td>
              <div class="data-value">
                  <?= $element->gauge->value ?>
              </div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="data-label">
                  <?= CHtml::encode($element->getAttributeLabel('pvd_induced')) ?>
              </div>
            </td>
            <td>
              <div class="data-value">
                  <?= $element->pvd_induced ? 'Yes' : 'No'; ?>
              </div>
            </td>
          </tr>
          <?php if (strlen($element->comments) > 0) { ?>
            <tr>
              <td>
                <div class="data-label">
                    <?= CHtml::encode($element->getAttributeLabel('comments')) ?>
                </div>
              </td>
              <td>
                <div class="data-value">
                    <?= Yii::app()->format->Ntext($element->comments) ?>
                </div>
              </td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</section>
