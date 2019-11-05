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
$thisMonth = date('Y-m-d', $date);
$lastMonth = mktime(0, 0, 0, date('m', $date) - 1, 1, date('Y', $date));
$nextMonth = mktime(0, 0, 0, date('m', $date) + 1, 1, date('Y', $date));
$nextYear = mktime(0, 0, 0, date('m'), 1, date('Y') + 1);
?>

<div class="element-fields full-width flex-layout flex-top">
    <div class="cols-9">
        <div class="flex-layout data-group">
            <?=\CHtml::link('<button class="large">
                <i class="oe-i arrow-left-bold medium pad"></i>
                Previous month</button>',
                array('booking/'.($operation->booking ? 're' : '').'schedule/'.$operation->event_id.'?firm_id='.($firm->id ? $firm->id : 'EMG').'&date='.date('Ym', $lastMonth)),
                array('class' => 'large')
            )?>
            <h3><?php echo date('F Y', $date)?></h3>
            <?php if ($nextMonth > $nextYear) {
                echo '<button href="#" class="large" id="next_month">Next month <i class="oe-i arrow-right-bold medium pad"></i></button>';
            } else {?>
                <?=\CHtml::link('<button class="large">Next month <i class="oe-i arrow-right-bold medium pad"></i></button>',
                    array('booking/'.($operation->booking ? 're' : '').'schedule/'.$operation->event_id.'?firm_id='.($firm->id ? $firm->id : 'EMG').'&date='.date('Ym', $nextMonth)),
                    array('class' => 'large')
                )?>
            <?php }?>
        </div>
        <table id="calendar" class="calendar">
            <?php
            foreach ($sessions as $weekday => $list) {?>
                <tr>
                    <td><?php echo $weekday?></td>
                    <?php foreach ($list as $date => $session) {?>
                        <?php if ($session['status'] == 'blank') {?>
                            <td></td>
                        <?php } else {?>
                            <td class="<?php if ($date == $selectedDate) {
                                ?> selected-date<?php
                                       } else echo $session['status'] ?>">
                                <?php echo date('j', strtotime($date))?>
                            </td>
                        <?php }?>
                    <?php }?>
                </tr>
            <?php }?>
        </table>
    </div>
    <div class="cols-2">
        <table class="calendar key">
            <tbody>
            <tr>
                <td class="">
                    Day of the week
                </td>
            </tr>
            <tr>
                <td class="available">
                    Slots Available
                </td>
            </tr>
            <tr>
                <td class="limited">
                    Limited Slots
                </td>
            </tr>
            <tr>
                <td class="full">
                    Full
                </td>
            </tr>
            <tr>
                <td class="closed">
                    Theatre Closed
                </td>
            </tr>
            <tr>
                <td class="selected-date">
                    Selected Date
                </td>
            </tr>
                    <?php if ($operation->getRTTBreach()) {?>
                        <tr>
                        <td>
                        Outside RTT
                        </td>
                        </tr>
                    <?php } ?>
            <tr>
                <td class="patient-unavailable">
                    Patient Unavailable
                </td>
            </tr>
            </tbody>
        </table>
    </div>


</div>

