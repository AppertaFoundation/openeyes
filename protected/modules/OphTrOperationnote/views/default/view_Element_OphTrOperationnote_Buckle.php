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
        <?php
        $this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
          'side' => $element->eye->getShortName(),
          'mode' => 'view',
          'width' => 200,
          'height' => 200,
          'model' => $element,
          'attribute' => 'eyedraw',
          'idSuffix' => 'Buckle',
        ));
        ?>
      <div class="eyedraw-data cols-9">
        <table class="label-value no-lines last-left">
          <colgroup>
            <col class="cols-5">
          </colgroup>
          <tbody>
          <tr>
            <td>
              <div class="data-label">
                    <?=\CHtml::encode($element->getAttributeLabel('drainage_type_id')) ?>
              </div>
            </td>
            <td>
              <div class="data-value">
                    <?php echo $element->drainage_type->name ?>
              </div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="data-label">
                    <?=\CHtml::encode($element->getAttributeLabel('drain_haem')) ?>
              </div>
            </td>
            <td>
              <div class="data-value">
                    <?php echo $element->drain_haem ? 'Yes' : 'No' ?>
              </div>
            </td>
          </tr>

          <tr>
            <td>
              <div class="data-label">
                    <?=\CHtml::encode($element->getAttributeLabel('deep_suture')) ?>
              </div>
            </td>
            <td>
              <div class="data-value">
                    <?php echo $element->deep_suture ? 'Yes' : 'No' ?>
              </div>
            </td>
          </tr>

          <tr>
            <td>
              <div class="data-label">
                    <?=\CHtml::encode($element->getAttributeLabel('report')) ?>
              </div>
            </td>
            <td>
              <div class="data-value">
                    <?=\CHtml::encode($element->report) ?>
              </div>
            </td>
          </tr>

          <tr>
            <td>
              <div class="data-label">
                    <?=\CHtml::encode($element->getAttributeLabel('comments')) ?>
              </div>
            </td>
            <td>
              <div class="data-value">
                    <?php echo Yii::app()->format->Ntext($element->comments) ?>
              </div>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</section>
