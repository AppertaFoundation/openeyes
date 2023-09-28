<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2023, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/** @var $filters */
?>
<nav class="oe-full-side-panel audit-filters">
    <input type="hidden" id="page" name="page" value="1" />
    <div class="row">
        <table class="standard last-right">
            <colgroup>
                <col class="cols-3">
            </colgroup>
            <tr>
                <td>Site</td>
                <td>
                    <?=\CHtml::dropDownList('filters[site_id]', $filters['site_id'], $sites, [
                            'class' => 'cols-full',
                            'empty' => 'All sites',
                            'data-test' => 'filter-site-id'
                    ])?>
                </td>
            </tr>
            <tr>
                <td>Context</td>
                <td>
                    <?=\CHtml::dropDownList('filters[firm_id]', $filters['firm_id'], $firms, [
                        'empty' => 'All firms',
                        'class' => 'cols-full',
                        'data-test' => 'filter-firm-id'
                    ])?>
                </td>
            </tr>
        </table>
    </div>
    <h4>Dispense condition</h4>
    <?=\CHtml::dropDownList(
            'filters[dispense_condition_id]',
            $filters['dispense_condition_id'],
            \CHtml::listData($dispense_condition, 'id', 'name'),
            ['empty' => 'All', 'class' => 'cols-full', 'data-test' => 'filter-dispense-condition-id'])?>

    <h4>Dispense location</h4>
    <?=\CHtml::dropDownList(
        'filters[dispense_location_id]',
        $filters['dispense_location_id'],
        \CHtml::listData($dispense_location, 'id', 'name'),
        ['empty' => 'All', 'class' => 'cols-full', 'data-test' => 'filter-dispense-location-id'])?>

    <?php $this->widget('application.modules.OphDrPrescription.widgets.DynamicFiltersWidget', [
            'title' => 'Secondary Signatories',
            'preselected' => $dynamic_filters,
            'adder_itemset' => $adder_itemset,
    ]); ?>

    <div class="row">
        <img class="loader hidden" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif');?>" alt="loading..." style="margin-right:10px" />
        <button type="submit" class="green hint cols-full" data-test="submit-filter">Search</button>
    </div>
</nav>
