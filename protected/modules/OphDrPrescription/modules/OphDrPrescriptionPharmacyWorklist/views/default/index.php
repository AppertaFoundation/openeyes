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
/** @var $institutions */
/** @var $sites */
/** @var $firms */
/** @var $dynamic_filters */
/** @var Element_OphDrPrescription_Esign $element */
?>
<style>
    #searchResults table tr:hover {
        cursor: pointer;
    }
    #searchResults table tr td {
        vertical-align: bottom;
    }

    #searchResults table th.event-date {
        min-width: 100px;
    }

    .js-oe-patient {
        display: inline-block;
    }
</style>
<div class="oe-full-header flex-layout">
    <div class="title wordcaps">Pharmacy Worklist</div>
    <div>
        <!-- no header buttons -->
    </div>
</div>
<div class="oe-full-content subgrid">
    <form method="post" id="pharmacy-worklist-filter" class="clearfix">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?=Yii::app()->request->csrfToken ?>"/>
        <?= $this->renderPartial('_filters', [
                'filters' => $filters,
                'dynamic_filters' => $filters['dynamic_filters'] ?? [],
                'institutions' => $institutions,
                'sites' => $sites,
                'firms' => $firms,
                'adder_itemset' => $adder_itemset,
                'dispense_location' => $dispense_location,
                'dispense_condition' => $dispense_condition,
        ]);?>

        <div id="search-loading-msg" class="large-12 column hidden">
            <div class="alert-box">
                <img src="<?= Yii::app()->assetManager->createUrl('img/ajax-loader.gif');?>" class="spinner" /> <strong>Searching, please wait...</strong>
            </div>
        </div>
    </form>
    <main id="searchResults" class="oe-full-main">
        <table class="standard highlight-rows">
            <colgroup>
                <col class="cols-icon">
            </colgroup>
            <thead>
                <tr>
                    <th></th>
                    <th class="event-date">Event date</th>
                    <th>Patient</th>
                    <th>Signatories</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data_provider->getData() as $step => $element):?>
                <?php $patient = $element->event->episode->patient; ?>
                <tr data-redirect-to="<?=$element->event->getViewLink()?>">
                    <td><i class="oe-i-e small i-DrPrescription"></i></td>
                    <td><?=\Helper::convertMySQL2NHS($element->event->event_date) ?></td>
                    <td><?= $patient->fullName ?></td>
                    <td>
                        <?php
                        foreach ($element->getViewSignatures() as $signature) {
                            $class = $signature->isSigned() ? 'good' : 'subtle-invert';
                            $has_tooltip = $signature->isSigned() ? 'js-has-tooltip ' : '';
                            $tooltip_content = $signature->isSigned()
                                ? "data-tooltip-content='{$signature->signedUser->fullNameAndTitle}' "
                                : '';

                            echo "<span $tooltip_content
                                    class='{$has_tooltip}highlighter $class inline'>
                            {$signature->signatory_role}</span> ";
                        }
                        ?>
                    </td>
                    <td><i class="oe-i large direction-right-circle"></i></td>
                </tr>
                <?php endforeach;?>
                <tr>
                    <td colspan="5">
                        <?php $this->widget('LinkPager', ['pages' => $data_provider->getPagination()]); ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <script>
            const table = document.querySelector(`#searchResults table tbody`);
            OpenEyes.UI.CommonEventListeners.onClickTableRow(table);
        </script>
    </main>
</div>
