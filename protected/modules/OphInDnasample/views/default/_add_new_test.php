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
<div class="title">
    <strong>Select the type of test to add:</strong>
</div>
<ul class="events">
    <?php foreach ($children as $eventType) {
        if (file_exists(Yii::getPathOfAlias('application.modules.'.$eventType->class_name.'.assets.img'))) {
            $assetpath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.'.$eventType->class_name.'.assets.img').'/').'/';
        } else {
            $assetpath = '/assets/';
        }

        if ($this->checkAccess('OprnCreateEvent', $this->firm, Episode::getCurrentEpisodeByFirm($this->patient->id, $this->firm), $eventType)) {
            ?>
            <li>
                <?=\CHtml::link('<img src="'.$assetpath.'small.png" alt="operation" /> - <strong>'.$eventType->name.'</strong>', Yii::app()->createUrl($eventType->class_name.'/Default/create').'?patient_id='.$patient->id.'&parent_event_id='.$parent_event_id)?>
            </li>
            <?php
        } else {
            ?>
            <li id="<?php echo $eventType->class_name?>_disabled" class="add_event_disabled" title="<?php echo $eventType->disabled ? $eventType->disabled_title : 'You do not have permission to add '.$eventType->name ?>">
                <?=\CHtml::link('<img src="'.$assetpath.'small.png" alt="operation" /> - <strong>'.$eventType->name.'</strong>', '#')?>
            </li>
            <?php
        }
        ?>
        <?php
    }?>
</ul>
