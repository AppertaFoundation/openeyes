<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php if ($chart->hasData()): ?>


    <div class="row">
        <div class="column large-12">
            <div id="keratometry-history-right-chart" class="chart" width="100%" height="500" style="width: 100%; height: 500px"></div>
        </div>
    </div>
    <?= $chart->run(); ?>
<?php else: ?>
    <div class="row">
        <div class="large-12 column">
            <div class="data-row">
                <div class="data-value">(no data)</div>
            </div>
        </div>
    </div>
<?php endif; ?>
