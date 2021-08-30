<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<table class="cols-8">
  <tbody>
  <tr>
    <td><?=\CHtml::encode($element->getAttributeLabel('eye_id')) ?>:</td>
    <td><?= $element->eye ? $element->eye->name : 'None' ?></td>
    <td></td>
  </tr>
  <tr>
    <td>
        <?=\CHtml::encode($element->getAttributeLabel('procedures')) ?>:
    </td>
    <td>
        <?php if (!$element->procedures) { ?>
          <h4>None</h4>
        <?php } else { ?>
          <h4>
              <?php foreach ($element->procedures as $item) {
                    echo $item->term ?><br/>
                <?php } ?>
          </h4>
        <?php } ?>
    </td>
    <td></td>
  </tr>
  <tr>
    <td>
        <?=\CHtml::encode($element->getAttributeLabel('anaesthetic_type_id')) ?>:
    </td>
    <td>
        <?php $text = '';
        foreach ($element->anaesthetic_type as $anaesthetic_type) {
            if (!empty($text)) {
                $text .= ', ';
            }
            $text .= $anaesthetic_type->name;
        }
        echo $text;
        ?>
    </td>
    <td></td>
  </tr>
  <tr>
    <td>
        <?=\CHtml::encode($element->getAttributeLabel('add_procs')) ?>:
    </td>
    <td>
      <ul>
            <?php if (!$element->additional_procedures) { ?>
            <li>None</li>
            <?php } else { ?>
            <li>
                <?php foreach ($element->additional_procedures as $item) {
                    echo $item->term ?><br/>
                <?php } ?>
            </li>
            <?php } ?>
      </ul>
    </td>
    <td></td>
  </tr>
  </tbody>
</table>
