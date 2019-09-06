<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<div class="element-data">
    <div class="data-group">
      <table class="cols-full">
        <tbody>
            <?php foreach (array(
                             'occupation_id' => 'displayoccupation',
                             'driving_statuses' => 'displaydrivingstatuses',
                             'smoking_status_id' => 'smoking_status',
                             'accommodation_id' => 'accommodation',
                             'comments' => 'comments',
                             'carer_id' => 'carer',
                             'alcohol_intake' => 'displayalcoholintake',
                             'substance_misuse_id' => 'substance_misuse'
                         ) as $id => $source) :?>
            <?php if ($element->$source) :?>
              <tr>
                <td><?=$element->getAttributeLabel($id)?></td>
                <td><?=$element->$source?></td>
              </tr>
            <?php endif;?>
            <?php endforeach;?>
          </tbody>
      </table>
    </div>
</div>
