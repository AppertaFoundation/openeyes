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
    <h3 class="element-title"><?php echo $element->elementType->name ?></h3>
  </header>
  <section class="element-data full-width">
    <div class="data-value flex-layout flex-top">
        <table class="last-left large">
          <colgroup>
            <col class="cols-fifth" span="4">
          </colgroup>
          <thead>
          <tr>
            <th><?= CHtml::encode($element->getAttributeLabel('application_type_id')) ?></th>
            <th><?= CHtml::encode($element->getAttributeLabel('concentration_id')) ?></th>
                <?php if ($element->application_type_id == OphTrOperationnote_Antimetabolite_Application_Type::SPONGE) : ?>
                <th><?= CHtml::encode($element->getAttributeLabel('duration')) ?></th>
                <th><?= CHtml::encode($element->getAttributeLabel('number')) ?></th>
              <th><?= CHtml::encode($element->getAttributeLabel('washed')) ?></th>
                <?php else : ?>
              <th><?= CHtml::encode($element->getAttributeLabel('volume_id')) ?></th>
              <th><?= CHtml::encode($element->getAttributeLabel('dose')) ?></th>
                <?php endif; ?>
          </tr>
          </thead>
          <tbody>
          <tr>
            <td><?= $element->application_type->name ?></td>
            <td><?= $element->concentration->value ?></td>
                <?php if ($element->application_type_id == OphTrOperationnote_Antimetabolite_Application_Type::SPONGE) : ?>
                <td><?= $element->duration ?></td>
                <td><?= $element->number ?></td>
                <td><?= $element->washed ? 'Yes' : 'No' ?></td>
                <?php else : ?>
                <td><?= $element->volume->value ?></td>
                <td><?= $element->dose ?></td>
                <?php endif; ?>
          </tr>
          </tbody>
        </table>
      </div>
  </section>
</section>
