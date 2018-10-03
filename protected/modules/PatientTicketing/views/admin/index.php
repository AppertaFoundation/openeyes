<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php
$assetManager = Yii::app()->getAssetManager();
$widgetPath = $assetManager->publish('protected/widgets/js');
Yii::app()->clientScript->registerScriptFile($widgetPath . '/MultiSelectList.js');
?>

<div class="cols-5">

<div class="row divider">
    <?php echo $title ?></h2>
</div>

<form id="admin_patient_ticketing">
    <table class="standard">
        <tbody>
        <?php foreach ($queuesets as $i => $set) : ?>
            <tr>
                <td>
                    <?php $this->renderPartial('queue_nav_item', array('queueset' => $set)); ?>
                </td>
            </tr>

        <?php endforeach; ?>
        </tbody>
        <tfooter class="pagination-container">
            <tr>
                <td>
                    <button id="add-queueset" type="button" class="secondary small">Add Queue Set</button>
                </td>
            </tr>
        </tfooter>
    </table>
    <div class="alert-box info" style="display: none;" id="message-box">
    </div>

    <div id="chart" class="column large-8 end orgChart" style="color:white;overflow-y: auto;"></div>
</form>
</div>