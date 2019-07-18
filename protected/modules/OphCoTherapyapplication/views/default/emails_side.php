<?php
/**
 * (C) OpenEyes Foundation, 2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php if ($emails): ?>
    <?php foreach ($emails as $email): ?>
        <?php if (($files = $email->attachments)): ?>
            <div class="data-group">
                <div class="cols-4 column">
                    <div class="data-label">Application files:</div>
                </div>
                <div class="cols-8 column">
                    <div class="data-value">
                        <ul class="application-files">
                            <?php foreach ($files as $file): ?>
                                <li><a href="<?= $file->getDownloadURL() ?>"><?php echo $file->name; ?></a></li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif ?>
        <div class="metadata">
            <span class="info">
                Application sent by <span class="user"><?= $email->user->fullname ?></span> on <?= Helper::convertMySQL2NHS($email->created_date) ?> at <?= date('H:i', strtotime($email->created_date)) ?>
            </span>
        </div>
    <?php endforeach ?>
<?php else: ?>
    <div class="data-value">N/A</div>
<?php endif ?>
