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

<?php

if ($element->event != null && $element->event->id > 0) {
    $iolRefValues = Element_OphInBiometry_IolRefValues::Model()->findAllByAttributes(
        array(
            'event_id' => $element->event->id,
        ));
} else {
    $iolRefValues = array();
}

if ($eventtype = EventType::model()->find('class_name = "OphCiExamination"')){
    $eventtypeid = $eventtype->id;
}

?>
<?php
$VAdate = " - (Not Recorded)";
$episode = $this->episode;
if ($api = Yii::app()->moduleAPI->get('OphCiExamination')) {

    $chosenVA[] = array('');

    //Get All Events for episode.
    $criteria = new CDbCriteria();
    $criteria->condition = 'episode_id = :e_id AND event_type_id = :e_typeid';
    $criteria->order = ' event_date DESC';
    $criteria->params = array(':e_id' => $episode->id, ':e_typeid' => $eventtypeid);

    //For each event, check if =event_id in _visualacuity.

    if($events = Event::model()->findAll($criteria)){
        for ($i = 0; $i < count($events); ++$i) {
            // Get Most Recent VA
            $vaID = $api->getMostRecentVA($events[$i]->id);
            if($vaID && !$data){
                $data = $api->getMostRecentVAData($vaID->id);
                $chosenVA = $vaID;
                $VAdate = "- (exam date " . date("d M Y", strtotime($events[$i]->event_date)) . ")";
            }
        }
    }

    $rightData = array();
    $leftData = array();

    for ($i = 0; $i < count($data); ++$i) {
        if($data[$i]->side == 0){
            $rightData[] = $data[$i];
        }
        if($data[$i]->side == 1){
            $leftData[] = $data[$i];
        }
    }

    $methodnameRight = array();
    $methodnameLeft = array();

    if($data){
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

<div class="element-fields element-eyes row">
    <div>&nbsp;
    </div>
</div>
<section>
    <header class="element-header">
        <h3 class="element-title">Visual Acuity <?php echo $VAdate; ?></h3>
    </header>
    <div class="element-fields element-eyes row">
        <div class="element-eye right-eye column">
            <?php
                if(count($methodnameRight)){
                    ?>
                    <div class="data-row">
                        <div class="data-value">
                            <?php echo $unitname ?>
                        </div>
                    </div>
                    <div class="data-row">
                        <div class="data-value">
                            <?php
                            for ($i = 0; $i < count($methodnameRight); ++$i) {
                                echo $api->getVAvalue($rightData[$i]->value, $unitId) . " " . $methodnameRight[$i];
                                if ($i != (count($methodnameRight) - 1)) {
                                    echo ", ";
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                } else {
                    ?>
                    <div class="data-row">
                        <div class="data-value">
                            Not recorded
                        </div>
                    </div>
                    <?php
                }
            ?>
        </div>
        <div class="element-eye left-eye column">
            <?php
            if(count($methodnameLeft)){
                ?>
                <div class="data-row">
                    <div class="data-value">
                        <?php echo $unitname ?>
                    </div>
                </div>
                <div class="data-row">
                    <div class="data-value">
                        <?php
                        for ($i = 0; $i < count($methodnameLeft); ++$i) {
                            echo $api->getVAvalue($leftData[$i]->value, $unitId) . " " . $methodnameLeft[$i];
                            if ($i != (count($methodnameLeft) - 1)) {
                                echo ", ";
                            }
                        }
                        ?>
                    </div>
                </div>
                <?php
            } else {
                ?>
                <div class="data-row">
                    <div class="data-value">
                        Not recorded
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</section>

<?php
// Near VA
$NearVAdate = " - (Not Recorded)";
$NearVAFound = false;
if ($api = Yii::app()->moduleAPI->get('OphCiExamination')) {
    for ($i = 0; $i < count($events); ++$i) {
        // Get Most Recent VA
        $vaID = $api->getMostRecentNearVA($events[$i]->id);
        // Loop through $data and separate into different eyes
        if ($vaID && !$NearVAFound) {
            $neardata = $api->getMostRecentNearVAData($vaID->id);
            $chosenNearVA = $vaID;
            $NearVAFound = true;
            $NearVAdate = "- (exam date " . date("d M Y", strtotime($events[$i]->event_date)) . ")";
        }
    }

    $rightNearData = array();
    $leftNearData = array();

    if($NearVAFound){
        for ($i = 0; $i < count($neardata); ++$i) {
            if($neardata[$i]->side == 0){
                $rightNearData[] = $neardata[$i];
            }
            if($neardata[$i]->side == 1){
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

<section>
    <header class="element-header">
        <h3 class="element-title">Near Visual Acuity <?php echo $NearVAdate; ?></h3>
    </header>
    <div class="element-fields element-eyes row">
        <div class="element-eye right-eye column">
            <?php
                if (count($rightNearData)) {
                    ?>
                    <div class="data-row">
                        <div class="data-value">
                            <?php echo $unitname ?>
                        </div>
                    </div>
                    <div class="data-row">
                        <div class="data-value">

                            <?php
                            for ($i = 0; $i < count($rightNearData); ++$i) {
                                echo $api->getVAvalue($rightNearData[$i]->value, $unitId). " " . $methodnameRight[$i];
                                if ($i != (count($rightNearData) - 1)) {
                                    echo ", ";
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                } else {
                    ?>
                    <div class="data-row">
                        <div class="data-value">
                            Not recorded
                        </div>
                    </div>
                    <?php
                }
            ?>
        </div>
        <div class="element-eye left-eye column">
            <?php
            if (count($leftNearData)) {
                ?>
                <div class="data-row">
                    <div class="data-value">
                        <?php echo $unitname ?>
                    </div>
                </div>
                <div class="data-row">
                    <div class="data-value">
                        <?php
                        for ($i = 0; $i < count($leftNearData); ++$i) {
                            echo $api->getVAvalue($leftNearData[$i]->value, $unitId) . " " . $methodnameLeft[$i];
                            if ($i != (count($leftNearData) - 1)) {
                                echo ", ";
                            }
                        }
                        ?>
                    </div>
                </div>
                <?php
            } else {
                ?>
                <div class="data-row">
                    <div class="data-value">
                        Not recorded
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</section>

<?php
// Refraction here
$refractfound = false;

if ($eventid = Event::model()->findAll(array(
    'condition' => 'event_type_id = ' . $eventtypeid . ' AND episode_id = ' . $episode->id,
    'order' => 'event_date DESC',
))){
// Loop through responses, for ones that have RefractionValues
for ($i = 0; $i < count($eventid); ++$i) {
    if ($api->getRefractionValues($eventid[$i]->id)) {
        if (!$refractfound){
            $refractelement = $api->getRefractionValues($eventid[$i]->id);
            $refract_event_date = $eventid[$i]->event_date;
            $refractfound = true;
        }
    }
}

if ($refractfound) {
?>
<section>
    <header class="element-header">
        <h3 class="element-title">Refraction - (exam date <?php echo date("d M Y",
                strtotime($refract_event_date)); ?>)</h3>
    </header>
    <div class="element-fields element-eyes row">
        <div class="element-eye right-eye column">
            <?php if ($refractelement->hasRight()) {
                ?>
                <div class="refraction">
                    <?php $this->renderPartial($element->view_view . '_OEEyeDraw',
                        array('side' => 'right', 'element' => $refractelement));
                    ?>
                </div>
                <?php
            } else {
                ?>
                <div class="data-row">
                    <div class="data-value">
                        Not recorded
                    </div>
                </div>
                <?php
            } ?>
        </div>
        <div class="element-eye left-eye column">
            <?php if ($refractelement->hasLeft()) {
                ?>
                <?php $this->renderPartial($element->view_view . '_OEEyeDraw',
                    array('side' => 'left', 'element' => $refractelement));
                ?>
                <?php
            } else {
                ?>
                <div class="data-row">
                    <div class="data-value">
                        Not recorded
                    </div>
                </div>
                <?php
            } ?>
        </div>
    </div>
    <?php
    } else {?>
    <section>
    <header class="element-header">
        <h3 class="element-title">Refraction - (Not Recorded)</h3>
    </header>
    <div class="element-fields element-eyes row">
        <div class="element-eye right-eye column">
            <div class="data-row">
                <div class="data-value">
                    Not recorded
                </div>
            </div>
        </div>
        <div class="element-eye left-eye column">
            <div class="data-row">
                <div class="data-value">
                    Not recorded
                </div>
            </div>
        </div>
    </div>
        <?php
    }
    }
    ?>
    <br/>
    <section class="element <?php echo $element->elementType->class_name ?>"
             data-element-type-id="<?php echo $element->elementType->id ?>"
             data-element-type-class="<?php echo $element->elementType->class_name ?>"
             data-element-type-name="<?php echo $element->elementType->name ?>"
             data-element-display-order="<?php echo $element->elementType->display_order ?>">
        <div class="element-fields element-eyes row">
            <?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>
            <div id="right-eye-lens"
                 class="element-eye right-eye top-pad left side column  <?php if (!$element->hasRight()) {
                     ?> inactive<?php
                 } ?>" data-side="right">
                <div class="centre">
                    <h4><b>RIGHT</b></h4>
                </div>
                <div class="active-form">
                    <a href="#" class="icon-remove-side remove-side">Remove side</a>
                    <?php echo CHtml::hiddenField('element_id', $element->id, array('class' => 'element_id')); ?>
                </div>
                <div class="inactive-form">
                    <div class="add-side">
                        <a href="#">
                            Add Right side <span class="icon-add-side"></span>
                        </a>
                    </div>
                </div>
                <div class="active-form">
                <?php $this->renderPartial('form_Element_OphInBiometry_Measurement_fields', array(
                    'side' => 'right',
                    'element' => $element,
                    'form' => $form,
                    'data' => $data,
                    'measurementInput' => $iolRefValues,
                )); ?>
                    </div>

            </div>
            <div id="left-eye-lens"
                 class="element-eye left-eye top-pad right side column <?php if (!$element->hasLeft()) {
                     ?> inactive<?php
                 } ?>" data-side="left">
                <div class="centre">
                    <h4><b>LEFT</b></h4>
                </div>
                        <div class="active-form">
                            <a href="#" class="icon-remove-side remove-side">Remove side</a>
                        </div>
                        <div class="inactive-form">
                            <div class="add-side">
                                <a href="#">
                                    Add left side <span class="icon-add-side"></span>
                                </a>
                            </div>
                        </div>
                        <div class="active-form">
                        <?php $this->renderPartial('form_Element_OphInBiometry_Measurement_fields', array(
                            'side' => 'left',
                            'element' => $element,
                            'form' => $form,
                            'data' => $data,
                            'measurementInput' => $iolRefValues,
                        )); ?></div>
                </div>
            </div>
    </section>
</section>
