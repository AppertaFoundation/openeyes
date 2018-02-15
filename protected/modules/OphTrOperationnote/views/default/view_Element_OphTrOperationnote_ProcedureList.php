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

<section class="element">
  <header class="element-header">
    <h3 class="element-title"><?php echo $element->elementType->name ?></h3>
  </header>
  <div class="element-fields full-width">
    <ul class="data-value highlight important">
        <?php foreach ($element->procedures as $procedure) { ?>
          <li>
              <?php echo $element->eye->adjective ?>
              <?php echo $procedure->term ?>
          </li>
        <?php } ?>
    </ul>
    <div class="cols-6 column">
      <header class="element-header">
        <h3 class="element-title"><?php echo $procedure->getAttributeLabel('opcsCodes.name'); ?></h3>
      </header>

      <section class="element-fields full-width">
          <?php
          $codes = [];
          foreach ($element->procedures as $procedure) {
              $size = count($procedure->opcsCodes) - 1;
              foreach ($procedure->opcsCodes as $code) {
                  $codes[] = $code->name;
              }
          }
          echo implode(', ', $codes);
          ?>
      </section>
  </div>
</section>

<?php $this->renderChildOpenElements($element, 'view', $form, $data) ?>
