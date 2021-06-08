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
                    <?= CHtml::dropDownList('firm-id', Yii::app()->request->getPost('firm-id', 'All'),
                        ['All' => 'All ' . Firm::model()->contextLabel() . 's'] + Firm::model()->getList($subspecialty_id), array(
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
            <td>Emergency list</td>
            <td>
                <label class="inline highlight ">
                    <?= CHtml::checkBox('emergency_list', ($emergency_list == 1)) ?>
                    Emergencies
                </label>
            </td>
        </tr>
        <?php if (Yii::app()->moduleAPI->get('OphInMehPac')) : ?>
        <tr>
            <td>PAC Outcome</td>
            <td>
                <label for="fit-for-surgery-checkbox">
                    <span class="pac-state-icon fit js-has-tooltip" data-tooltip-content="PAC<br/>Patient is fit for surgery" >PAC</span>
                </label>
                <input id="fit-for-surgery-checkbox" name="patient-is-fit-for-surgery" type="checkbox" class="fit-for-surgery-checkbox pac-outcome-checkbox">

                <label for="reschedule-surgery-checkbox">
                    <span class="pac-state-icon reschedule js-has-tooltip" data-tooltip-content="PAC<br/>Re-schedule date - patient is fit for surgery">PAC</span>
                </label>
                <input id="reschedule-surgery-checkbox" name="reschedule-surgery-date" type="checkbox" class="reschedule-surgery-checkbox pac-outcome-checkbox">

                <label for="hold-for-outstanding">
                    <span class="pac-state-icon hold js-has-tooltip" data-tooltip-content="PAC<br/>Hold for outstanding actions">PAC</span>
                </label>
                <input id="hold-for-outstanding" name="hold-for-outstanding-actions" type="checkbox" class="hold-for-outstanding-checkbox pac-outcome-checkbox">

                <label for="the-patient-is-not-fit">
                    <span class="pac-state-icon not-fit js-has-tooltip" data-tooltip-content="PAC<br/>Patient is not fit/ready for surgery">PAC</span>
                </label>
                <input id="the-patient-is-not-fit" name="the-patient-is-not-fit" type="checkbox" class="the-patient-is-not-fit-checkbox pac-outcome-checkbox">
            </td>
        </tr>
        <?php endif; ?>
        </tbody>
    </table>


    <h3>Filter by Date</h3>
    <div class="flex">
        <!-- use date for test data: value="28 Jul 2015" -->
        <input type="text" id="date-start" name="date-start"  class="date js-filter-date-from" placeholder="from">
        <input style="margin-right:90px" type="text" id="date-end" name="date-end" class="date js-filter-date-to" placeholder="to">
<!--        <label class="inline highlight ">-->
<!--            <input value="Next 12 wks" name="next-12-wks" type="checkbox"> Next 12 wks-->
<!--        </label>-->
    </div>

    <div id="theatre-diaries-date-ranges" class="fast-date-range">
        <div class="selectors">
            <div class="range" data-range="yesterday">Yesterday</div>
            <div class="range" data-range="today">Today</div>
            <div class="range" data-range="tomorrow">Tomorrow</div>
        </div>
        <div class="selectors">
            <div class="range" data-range="last-week">Last week</div>
            <div class="range" data-range="this-week">This week</div>
            <div class="range" data-range="next-week">Next week</div>
        </div>
        <div class="selectors">
            <div class="range" data-range="last-month">Last month</div>
            <div class="range" data-range="this-month">This month</div>
            <div class="range" data-range="next-month">Next month</div>
        </div>
    </div>

    <div class="row button-stack">
        <i class="spinner" style="display:none"></i>
        <button class="green hint cols-full" id="search_button" type="submit">Search</button>
    </div>
</nav>
