<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
 * version. OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details. You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled
 * COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<div class="element-data element-data-inline">
    <?php if ($element->no_risks_date) { ?>
        <div class="data-value">Patient has no known risks.</div>
    <?php } else { ?>
        <?php if ($element->present) { ?>
            <div class="data-label"><strong><?= $element->getAttributeLabel('present') ?>:</strong></div>
            <div class="data-value"><?= $element->getEntriesDisplay('present') ?></div>
        <?php } ?>
        <?php if ($element->not_checked) { ?>
            <div class="data-label"><strong><?= $element->getAttributeLabel('not_checked') ?>:</strong></div>
            <div class="data-value"><?= $element->getEntriesDisplay('not_checked') ?></div>
        <?php } ?>
        <?php if ($element->not_present) { ?>
            <div class="data-label"><strong><?= $element->getAttributeLabel('not_present') ?>:</strong></div>
            <div class="data-value"><?= $element->getEntriesDisplay('not_present') ?></div>
        <?php } ?>
    <?php } ?>
</div>
