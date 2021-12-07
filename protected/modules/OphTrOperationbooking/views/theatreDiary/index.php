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

<div class="oe-full-header">
    <div class="title wordcaps">Theatre Diaries</div>
    <?php if ($this->checkAccess('OprnPrint')) { ?>
        <div class="options-right">
            <button id="btn_print_diary" class="button header-tab">Print</button>
            <button id="btn_print_diary_list" class="button header-tab">Print list</button>
        </div>
    <?php } ?>
</div>

<div class="oe-full-content oe-theatre-diaries subgrid">
    <?php $this->beginWidget('CActiveForm', array(
        'id' => 'theatre-filter',
        'htmlOptions' => array(
            'class' => 'data-group',
        ),
        'enableAjaxValidation' => false,
    )) ?>


    <?php $this->renderPartial('side_panel', [
        'theatres' => $theatres,
        'wards' => $wards
    ]); ?>


    <?php $this->endWidget() ?>

    <main class="oe-full-main">
        <?php $this->renderPartial('//base/_messages'); ?>

        <!--<div class="alert-box info"><strong><span id="result_count">99</span> Results</strong></div>-->
        <div id="theatreList" class="theatre-diary-list"></div>
        <div class="printable" id="printable"></div>
        <div id="iframeprintholder" style="display: none;"></div>

    </main>

</div>

<?php
$assetManager = Yii::app()->getAssetManager();
$widgetPath = $assetManager->publish('protected/widgets/js');
Yii::app()->clientScript->registerScriptFile($widgetPath . '/PatientPanelPopupMulti.js');
?>

<style>
    .printable {
        display: none;
    }

    .printable * {
        font-size: 9pt;
    }

    @media print {
        .printable {
            display: block !important;
            width: 1050px !important;
        }
    }
</style>

