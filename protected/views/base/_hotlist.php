<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

$openHotlistItems = UserHotlistItem::model()->getHotlistItems(1);
$closedHotlistItems = UserHotlistItem::model()->getHotlistItems(0, date('Y-m-d'));

?>
<div class="oe-hotlist-panel" id="js-hotlist-panel">
    <div class="patient-activity">
        <div class="patients-open">
            <div class="overview">
                <h3>Open
                    <small class="count"><?= count($openHotlistItems) ?></small>
                </h3>
            </div>

            <!-- Open Items -->
            <table class="activity-list open" style="table-layout: fixed;">
                <colgroup>
                    <col style="width: 50px;">
                    <col style="width: 40%;">
                    <col style="width: 30%">
                    <col style="width: 50px;">
                </colgroup>
                <tbody>

                <?php foreach ($openHotlistItems as $hotlistItem) : ?>
                    <?php echo $this->renderPartial('//base/_hotlist_item', array('hotlistItem' => $hotlistItem)); ?>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Closed Items. users can select date to view all patients closed on that date -->
        <div class="patients-closed">

            <div class="overview flex-layout">
                <h3>Closed: <span class="for-date" id="js-pickmeup-closed-date">Today</span>
                    <small class="count"><?= count($closedHotlistItems) ?></small>
                </h3>
                <div class="closed-search">
                    <span class="closed-date" id="js-hotlist-closed-today">Today</span>
                    <span class="closed-date" id="js-hotlist-closed-select">Select date</span>
                    <div id="js-pickmeup-datepicker" style="display: none;"></div>
                </div>
            </div>

            <table class="activity-list closed">
                <tbody>
                <?php foreach ($closedHotlistItems as $hotlistItem) : ?>
                    <?php echo $this->renderPartial('//base/_hotlist_item', array('hotlistItem' => $hotlistItem)); ?>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div id="hotlist-quicklook" class="hotlist-quicklook"></div>
    </div>
</div>
