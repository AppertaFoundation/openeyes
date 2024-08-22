<?php

/**
 * (C) OpenEyes Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>

<?php
$emergency_list = Yii::app()->request->getParam('emergency_list');
$subspecialty_id = Yii::app()->request->getPost('subspecialty-id', 'All');
$date_filter = Yii::app()->request->getPost('date-filter', '');
?>

<nav class="oe-full-side-panel">

    <h2>Search schedules</h2>
    <h4>Theatre schedule filters</h4>

    <!-- search options -->
    <table class="standard normal-text last-right">
        <tbody>
        <tr>
            <td>Site</td>
            <td>
                <?= CHtml::dropDownList('site-id', Yii::app()->request->getPost('site-id', 'All'),
                    ['All' => 'All sites'] + Site::model()->getListForCurrentInstitution(), array(
                        'disabled' => ($emergency_list == 1 ? 'disabled' : ''),
                        'class' => 'cols-full'
                    )
                ) ?>
            </td>
        </tr>
        <tr>
            <td>Theatre</td>
            <td>
                <?= CHtml::dropDownList('theatre-id', Yii::app()->request->getPost('theatre-id', 'All'),
                    ['All' => 'All theatres'] + $theatres, array(
                        'disabled' => ($emergency_list == 1 ? 'disabled' : ''),
                        'class' => 'cols-full'
                    )) ?>
            </td>
        </tr>
        <tr>
            <td>Subspeciality</td>
            <td>
                <?= CHtml::dropDownList('subspecialty-id', Yii::app()->request->getPost('subspecialty-id', 'All'),
                    ['All' => 'All specialties'] + Subspecialty::model()->getList(), array(
                        'disabled' => ($emergency_list == 1 ? 'disabled' : ''),
                        'class' => 'cols-full'
                    )) ?>
            </td>

        </tr>
        <tr>
            <td><?= Firm::contextLabel() ?></td>
            <td>

                <?php if ($subspecialty_id === '') { ?>
                    <?= CHtml::dropDownList('firm-id', 'All', ['All' => 'All ' . Firm::model()->contextLabel() . 's'],
                        array('disabled' => 'disabled', 'class' => 'cols-full')) ?>
                <?php } else { ?>
                    <?php $institution_id = Yii::app()->session['selected_institution_id']; ?>
                    <?= CHtml::dropDownList('firm-id', Yii::app()->request->getPost('firm-id', 'All'),
                        ['All' => 'All ' . Firm::model()->contextLabel() . 's'] + Firm::model()->getList($institution_id, $subspecialty_id), array(
                            'disabled' => ($emergency_list == 1 ? 'disabled' : ''),
                            'class' => 'cols-full'
                        )) ?>
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td>Ward</td>
            <td>
                <?= CHtml::dropDownList('ward-id', Yii::app()->request->getPost('ward-id', 'All'),
                    ['All' => 'All wards'] + $wards, array(
                        'disabled' => ($emergency_list == 1 ? 'disabled' : ''),
                        'class' => 'cols-full'
                    )) ?>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <label class="inline highlight ">
                    <input value="Include lists with no bookings" name="include_no_booking_lists" type="checkbox">
                    Include lists with no bookings
                </label>
            </td>
        </tr>
        <tr>
            <td>Emergency list</td>
            <td>
                <label class="inline highlight ">
                    <?= CHtml::checkBox('emergency_list', ($emergency_list == 1)) ?>
                    Emergencies
                </label>
            </td>
        </tr>
        </tbody>
    </table>


    <h3>Filter by Date</h3>
    <div class="set-date-range">
    <div class="date-inputs">
        <!-- use date for test data: value="28 Jul 2015" -->
        <input type="text" size="11" id="date-start" name="date-start"  class="date js-filter-date-from" placeholder="from">
        <input type="text" size="11" id="date-end" name="date-end" class="date js-filter-date-to" placeholder="to">
<!--        <label class="inline highlight ">-->
<!--            <input value="Next 12 wks" name="next-12-wks" type="checkbox"> Next 12 wks-->
<!--        </label>-->
    </div>

    <div id="theatre-diaries-date-ranges" class="fast-date-range">
        <div class="selectors">
            <label>
                <input type="radio" value="+4days" name="quick-selector">
                <div class="btn" data-range="+4days">+ 4 days</div>
            </label>
            <label>
                <input type="radio" value="+7days" name="quick-selector">
                <div class="btn" data-range="+7days">+ 7 days</div>
            </label>
            <label>
                <input type="radio" value="+12days" name="quick-selector">
                <div class="btn" data-range="+12days">+ 12 days</div>
            </label>
        </div>
        <div class="selectors">
            <label>
                <input type="radio" value="yesterday" name="quick-selector">
                <div class="btn" data-range="yesterday">Yesterday</div>
            </label>
            <label>
                <input type="radio" value="today" name="quick-selector">
                <div class="btn" data-range="today">Today</div>
            </label>
            <label><input type="radio" value="tomorrow" name="quick-selector">
                <div class="btn" data-range="tomorrow">Tomorrow</div>
            </label>
        </div>
        <div class="selectors">
            <label>
                <input type="radio" value="last-week" name="quick-selector">
                <div class="btn" data-range="last-week">Last week</div>
            </label>
            <label>
                <input type="radio" value="this-week" name="quick-selector">
                <div class="btn" data-range="this-week">This week</div>
            </label>
            <label>
                <input type="radio" value="next-week" name="quick-selector">
                <div class="btn" data-range="next-week">Next week</div>
            </label>
        </div>
        <div class="selectors">
            <label>
                <input type="radio" value="last-month" name="quick-selector">
                <div class="btn" data-range="last-month">Last month</div>
            </label>
            <label>
                <input type="radio" value="this-month" name="quick-selector">
                <div class="btn" data-range="this-month">This month</div>
            </label>
            <label>
                <input type="radio" value="next-month" name="quick-selector">
                <div class="btn" data-range="next-month">Next month</div>
            </label>
        </div>
    </div>
    </div>
    <div class="row button-stack">
        <i class="spinner" style="display:none"></i>
        <button class="green hint cols-full" id="search_button" type="submit">Search</button>
    </div>
</nav>
