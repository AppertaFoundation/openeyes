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
<div class="element-data full-width">
    <?php if (isset($element->entries)) {?>
        <div class = "cols-11">
            <table class = "cols-full last-left">
                <colgroup>
                    <col class="cols-4">
                    <col class="">
                    <col class="">
                    <col class="">
                    <col class="cols-6">
                </colgroup>
                <tbody>
                    <?php foreach ($element->entries as $entry) { ?>
                        <tr>
                            <td><?=\OEModule\OphCiExamination\models\OphCiExamination_Investigation_Codes::model()->findByPk($entry->investigation_code)->name ?></td>
                            <td><span class="oe-date"><?= Helper::convertMySQL2NHS($entry->date) ?></span></td>
                            <td class="nowrap"><small>at </small><?= $entry->time ?></td>
                            <td><i class="oe-i info small pad-right  js-has-tooltip" data-tt-type="basic"
                       data-tooltip-content="by <?=User::model()->findByPk($entry->last_modified_user_id)->getFullNameAndTitle() ?>"></i></td>
                            <td><span class="user-comment"><?= $entry->comments ?></span></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>
    <?php if (!empty($element->description)) { ?>
        <hr class="divider">
        <div><span class="user-comment"><?= OELinebreakReplacer::replace(CHtml::encode($element->description)) ?></span></div>
    <?php } ?>
</div>
