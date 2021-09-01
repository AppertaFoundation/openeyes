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
<div class="element-data full-width">
    <!-- Group in Annotation mode, when adding a template this the default state -->
    <?php foreach ($element->entries as $k => $entry) : ?>
        <div class="freedraw-group">
            <div class="flex-t col-gap">

                <div class="cols-6">
                    <img src="<?= $entry->protected_file->getDownloadURL() ?>" width="100%">
                </div>


                <div class="cols-6">
                    <table class="cols-full last-left">
                        <tbody>
                        <tr>
                            <td><?= $entry->protected_file->name ?></td>
                        </tr>
                        <tr>
                            <td>
                                <?php if ($entry->comments): ?>
                                <i class="oe-i comments-who small pad-right js-has-tooltip"
                                   data-tooltip-content="User comment"
                                ></i><span
                                        class="user-comment"><?= \CHtml::encode($entry->comments); ?></span>

                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?= $entry->element->user->fullName; ?>, <?= \Helper::convertMySQL2NHS($entry->last_modified_date); ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php if(count($element->entries) > ($k+1)):?>
            <hr class="divider">
        <?php endif;?>
    <?php endforeach; ?>
</div>
