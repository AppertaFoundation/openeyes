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
?>
<input type="radio" value="1" name="choose-option-radio" id="choose-option-radio"> Create new set
<input type="radio" value="2" name="choose-option-radio" id="choose-option-radio"> Use an existing automatic set

<div class="choose-option-list-box" style="display: none; height: 250px;">
    <select class="choose-option-list" name="choose-option-list">
        <?php foreach ($admin->autosets as $autoset) : ?>
            <option value="<?=$autoset->id?>"><?=$autoset->name?></option>
        <?php endforeach; ?>
    </select>
</div>

<button class="button large save-option" type="button">Add</button>

<?php
$assetManager = Yii::app()->getAssetManager();
$baseAssetsPath = Yii::getPathOfAlias('application.assets');
$assetManager->publish($baseAssetsPath.'/components/chosen/');

Yii::app()->clientScript->registerScriptFile($assetManager->getPublishedUrl($baseAssetsPath.'/components/chosen/').'/chosen.jquery.min.js');
Yii::app()->clientScript->registerCssFile($assetManager->getPublishedUrl($baseAssetsPath.'/components/chosen/').'/chosen.min.css');
?>

