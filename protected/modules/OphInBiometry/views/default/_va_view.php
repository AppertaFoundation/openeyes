<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php
$eventtype = EventType::model()->find('class_name = "OphCiExamination"');
if ($eventtype) {
    $eventtypeid = $eventtype->id;
}
?>
<?php
$VAdate = "";
$VA_data = NULL;
$episode = $this->episode;
$api = Yii::app()->moduleAPI->get('OphCiExamination');
if ($api) {
    $chosenVA[] = array('');
    //Get All Events for episode.
    $criteria = new CDbCriteria();
    $criteria->condition = 'episode_id = :e_id AND event_type_id = :e_typeid';
    $criteria->order = ' event_date DESC';
    $criteria->params = array(':e_id' => $episode->id, ':e_typeid' => $eventtypeid);
    //For each event, check if =event_id in _visualacuity.
    $events = Event::model()->findAll($criteria);
    if ($events) {
        for ($i = 0; $i < count($events); ++$i) {
            // Get Most Recent VA
            $vaID = $api->getMostRecentVA($events[$i]->id);
            if ($vaID && !$VA_data) {
                $VA_data = $api->getMostRecentVAData($vaID->id);
                $chosenVA = $vaID;
                $VAdate = date("d M Y", strtotime($events[$i]->event_date));
            }
        }
    }
    $rightData = array();
    $leftData = array();
    for ($i = 0; $i < count($VA_data); ++$i) {
        if ($VA_data[$i]->side == 0) {
            $rightData[] = $VA_data[$i];
        }
        if ($VA_data[$i]->side == 1) {
            $leftData[] = $VA_data[$i];
        }
    }
    $methodnameRight = array();
    $methodnameLeft = array();
    if ($VA_data) {
        $unitId = $chosenVA->unit_id;
        for ($i = 0; $i < count($rightData); ++$i) {
            $VAfinalright = $api->getVAvalue($rightData[$i]->value, $unitId);
        }
        for ($i = 0; $i < count($leftData); ++$i) {
            $VAfinalleft = $api->getVAvalue($leftData[$i]->value, $unitId);
        }
        $methodIdRight = $api->getMethodIdRight($chosenVA->id, $episode);
        for ($i = 0; $i < count($methodIdRight); ++$i) {
            $methodnameRight[$i] = $api->getMethodName($methodIdRight[$i]->method_id);
        }
        $methodIdLeft = $api->getMethodIdLeft($chosenVA->id, $episode);
        for ($i = 0; $i < count($methodIdLeft); ++$i) {
            $methodnameLeft[$i] = $api->getMethodName($methodIdLeft[$i]->method_id);
        }
        $unitname = $api->getUnitName($unitId);
    }
}
?>
<section class="element full <?php if ($action == 'update') {
    echo 'edit  edit-biometry';
} else if ($action == 'view') {
    echo 'priority';
} ?>
  eye-divider ">
    <header class="element-header">
        <h3 class="element-title">Visual Acuity <?= '<br />' . $VAdate; ?></h3>
    </header>
    <div class="element-fields element-eyes data-group">
        <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) : ?>
        <div class="js-element-eye <?= $eye_side ?>-eye column">
            <?php
            $_method_name = 'methodname' . ucfirst($eye_side);
            $method_name = ${$_method_name};
            $_data = $eye_side . 'Data';
            $data = ${$_data};
            if (count($method_name)) {
            ?>
            <div class="data-value">
                <?= $unitname ?>
            </div>
            <div class="data-group">
                <div class="data-value">
                    <?php
                    for ($i = 0; $i < count($method_name); ++$i) {
                        echo $api->getVAvalue($data[$i]->value, $unitId) . " " . $method_name[$i];
                        if ($i != (count($method_name) - 1)) {
                            echo ", ";
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
        } else { ?>
        <div class="data-value not-recorded">
            Not recorded
        </div>
    </div>
    <?php }
    endforeach; ?>
    </div>
</section>
<?php
// Near VA
$NearVAdate = "";
$NearVAFound = false;
if ($api) {
    for ($i = 0; $i < count($events); ++$i) {
        // Get Most Recent VA
        $vaID = $api->getMostRecentNearVA($events[$i]->id);
        // Loop through $data and separate into different eyes
        if ($vaID && !$NearVAFound) {
            $neardata = $api->getMostRecentNearVAData($vaID->id);
            $chosenNearVA = $vaID;
            $NearVAFound = true;
            $NearVAdate = date("d M Y", strtotime($events[$i]->event_date));
        }
    }
    $rightNearData = array();
    $leftNearData = array();
    if ($NearVAFound) {
        for ($i = 0; $i < count($neardata); ++$i) {
            if ($neardata[$i]->side == 0) {
                $rightNearData[] = $neardata[$i];
            }
            if ($neardata[$i]->side == 1) {
                $leftNearData[] = $neardata[$i];
            }
        }
        $unitId = $chosenNearVA->unit_id;
        for ($i = 0; $i < count($rightNearData); ++$i) {
            $VAfinalright = $api->getVAvalue($rightNearData[$i]->value, $unitId);
        }
        for ($i = 0; $i < count($leftNearData); ++$i) {
            $VAfinalleft = $api->getVAvalue($leftNearData[$i]->value, $unitId);
        }
        $methodIdRight = $api->getMethodIdNearRight($chosenNearVA->id);
        for ($i = 0; $i < count($rightNearData); ++$i) {
            $methodnameRight[$i] = $api->getMethodName($rightNearData[$i]->method_id);
        }
        $methodIdLeft = $api->getMethodIdNearLeft($chosenNearVA->id);
        for ($i = 0; $i < count($leftNearData); ++$i) {
            $methodnameLeft[$i] = $api->getMethodName($leftNearData[$i]->method_id);
        }
        $unitname = $api->getUnitName($unitId);
    }
}
?>
<section class="element full <?php if ($action == 'update') {
    echo 'edit  edit-biometry';
} else if ($action == 'view') {
    echo 'priority';
} ?>
  eye-divider ">
    <header class="element-header">
        <h2 class="element-title">Near Visual Acuity <?= '<br />' . $NearVAdate; ?></h2>
    </header>
    <div class="element-fields element-eyes data-group">
        <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) : ?>
            <div class="js-element-eye <?= $eye_side ?>-eye column">
                <?php
                $_method_name = 'methodname' . ucfirst($eye_side);
                $method_name = ${$_method_name};
                $data = ${$eye_side . 'NearData'};
                if (count($data)) { ?>
                    <div class="data-value">
                        <?= $unitname ?>
                    </div>
                    <div class="data-value">
                        <?php
                        for ($i = 0; $i < count($data); ++$i) {
                            echo $api->getVAvalue($data[$i]->value, $unitId) . " " . $method_name[$i];
                            if ($i != (count($data) - 1)) {
                                echo ", ";
                            }
                        } ?>
                    </div>
                    <?php
                } else { ?>
                    <div class="data-value not-recorded">
                        Not recorded
                    </div>
                <?php } ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php
// Refraction here
$refractfound = false;
$eventid = Event::model()->findAll(array(
    'condition' => 'event_type_id = ' . $eventtypeid . ' AND episode_id = ' . $episode->id,
    'order' => 'event_date DESC',
));
if ($eventid){
// Loop through responses, for ones that have RefractionValues
for ($i = 0; $i < count($eventid); ++$i) {
    $refraction_values = $api->getRefractionValues($eventid[$i]->id);
    if ($refraction_values) {
        if (!$refractfound) {
            $refractelement = $refraction_values;
            $refract_event_date = $eventid[$i]->event_date;
            $refractfound = true;
        }
    }
}
if ($refractfound) {
    ?>
    <section class="element full <?php if ($action == 'update') {
        echo 'edit  edit-biometry';
    } else if ($action == 'view') {
        echo 'priority';
    } ?>
  eye-divider ">
        <header class="element-header">
            <h3 class="element-title">Refraction <?= '<br />' . \Helper::convertDate2NHS($refract_event_date); ?></h3>
        </header>
        <div class="element-fields element-eyes data-group">
            <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) : ?>
                <div class="js-element-eye <?= $eye_side ?>-eye column">
                    <?php if ($refractelement->hasEye($eye_side)) {
                        ?>
                        <div class="refraction">
                            <?php $this->renderPartial('view_Element_OphInBiometry_Measurement_OEEyeDraw',
                                array('side' => $eye_side, 'element' => $refractelement));
                            ?>
                        </div>
                        <?php
                    } else { ?>
                        <div class="data-value not-recorded">
                            Not recorded
                        </div>
                    <?php } ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php
} else { ?>
<section class="element full <?php if ($action == 'update') {
    echo 'edit  edit-biometry';
} else if ($action == 'view') {
    echo 'priority';
} ?>
  eye-divider ">
    <header class="element-header">
        <h3 class="element-title">Refraction</h3>
    </header>
    <div class="element-fields element-eyes data-group">
        <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) : ?>
            <div class="js-element-eye <?= $eye_side; ?>-eye column">
                <div class="data-value not-recorded">
                    Not recorded
                </div>
            </div> <?php endforeach; ?>
    </div>
    <?php }
    } ?>
</section>