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
$widgetPath = $assetManager->publish('protected/widgets/js', true);
Yii::app()->clientScript->registerScriptFile($widgetPath . '/MultiSelectList.js');
?>
<h2><?= $title ?></h2>
<div class="cols-half">
<form id="admin_patient_ticketing">
    <div class="row">
        <table class="standard last-right">
            <tbody>
            <?php foreach ($queuesets as $i => $set) {
                $this->renderPartial('queue_nav_item', array('queueset' => $set));
            } ?>
            </tbody>
        </table>
    </div>
    <hr class="divider">
    <button id="add-queueset" type="button" class="hint green cols-half">Add Queue Set</button>
    <div class="alert-box info" style="display: none;" id="message-box">
    </div>
    <hr>
    <div id="chart" class="column large-8 end orgChart" style="color:white;overflow-y: auto;"></div>
</form>
</div>
<script type="text/javascript" src="<?= Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.widgets.js') . '/AutoCompleteSearch.js', false, -1); ?>"></script>