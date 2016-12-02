<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
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
?>
<?php
$VAdate = " - (Not Recorded)";
foreach ($this->patient->episodes as $episode) {
//					echo $episode->id;
}
if ($api = Yii::app()->moduleAPI->get('OphCiExamination')) {
    if ($api->getLetterVisualAcuityRight($this->patient) || $api->getLetterVisualAcuityLeft($this->patient)) {

        $VAid = $api->getVAId($this->patient, $episode);
        $unitId = $api->getUnitId($VAid->id, $episode);

        $VAright = $api->getVARight($VAid->id);
        for ($i = 0; $i < count($VAright); ++$i) {
            $VAfinalright = $api->getVAvalue($VAright[$i]->value, $unitId);
        }

        $VAleft = $api->getVALeft($VAid->id);
        for ($i = 0; $i < count($VAright); ++$i) {
            $VAfinalleft = $api->getVAvalue($VAleft[$i]->value, $unitId);
        }
        $VAdate = "- (exam date " . date("d M Y h:ia", strtotime($VAid->last_modified_date)) . ")";

        $methodIdRight = $api->getMethodIdRight($VAid->id, $episode);
        for ($i = 0; $i < count($methodIdRight); ++$i) {
            $methodnameRight[$i] = $api->getMethodName($methodIdRight[$i]->method_id);
        }

        $methodIdLeft = $api->getMethodIdLeft($VAid->id, $episode);
        for ($i = 0; $i < count($methodIdLeft); ++$i) {
            $methodnameLeft[$i] = $api->getMethodName($methodIdLeft[$i]->method_id);
        }

        $unitnameRight = $api->getUnitName($unitId);
        $unitnameLeft = $unitnameRight;
    }
}
?>

<div class="element-fields element-eyes row">
    <div>&nbsp;
    </div>
</div>
<section>
    <header class="sub-element-header">
        <h3 class="element-title">Visual Acuity <?php echo $VAdate; ?></h3>
    </header>
    <div class="element-fields element-eyes row">
        <div class="element-eye right-eye column">
            <?php if ($element->hasRight()) {
                if ($api->getLetterVisualAcuityRight($this->patient)) {
                    ?>
                    <div class="data-row">
                        <div class="data-value">
                            <?php echo $unitnameRight ?>
                        </div>
                    </div>
                    <div class="data-row">
                        <div class="data-value">
                            <?php
                            for ($i = 0; $i < count($methodnameRight); ++$i) {
                                echo $api->getVAvalue($VAright[$i]->value, $unitId) . " " . $methodnameRight[$i];
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
            } ?>
        </div>
        <div class="element-eye left-eye column">
            <?php
            if ($api->getLetterVisualAcuityLeft($this->patient)) {
                ?>
                <div class="data-row">
                    <div class="data-value">
                        <?php echo $unitnameLeft ?>
                    </div>
                </div>
                <div class="data-row">
                    <div class="data-value">
                        <?php
                        for ($i = 0; $i < count($methodnameLeft); ++$i) {
                            echo $api->getVAvalue($VAleft[$i]->value, $unitId) . " " . $methodnameLeft[$i];
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
if ($api = Yii::app()->moduleAPI->get('OphCiExamination')) {
    if ($api->getBestNearVisualAcuity($this->patient, $episode,
            'right') || $api->getBestNearVisualAcuity($this->patient, $episode, 'left')
    ) {

        $VAid = $api->getNearVAId($this->patient, $episode);
        $unitId = $api->getNearUnitId($VAid, $episode);

        $VAright = $api->getNearVARight($VAid);
        for ($i = 0; $i < count($VAright); ++$i) {
            $VAfinalright = $api->getVAvalue($VAright[$i]->value, $unitId);
        }

        $VAleft = $api->getNearVALeft($VAid);
        for ($i = 0; $i < count($VAright); ++$i) {
            $VAfinalleft = $api->getVAvalue($VAleft[$i]->value, $unitId);
        }

        $methodIdRight = $api->getMethodIdNearRight($VAid);
        for ($i = 0; $i < count($methodIdRight); ++$i) {
            $methodnameRight[$i] = $api->getMethodName($methodIdRight[$i]->method_id);
        }

        $methodIdLeft = $api->getMethodIdNearLeft($VAid);
        for ($i = 0; $i < count($methodIdLeft); ++$i) {
            $methodnameLeft[$i] = $api->getMethodName($methodIdLeft[$i]->method_id);
        }

        $unitnameRight = $api->getUnitName($unitId);
        $unitnameLeft = $unitnameRight;
    }
}
?>

<section>
    <header class="sub-element-header">
        <h3 class="element-title">Near Visual Acuity</h3>
    </header>
    <div class="element-fields element-eyes row">
        <div class="element-eye right-eye column">
            <?php if ($element->hasRight()) {
                if ($api->getBestNearVisualAcuity($this->patient, $episode, 'left')) {
                    ?>
                    <div class="data-row">
                        <div class="data-value">
                            <?php echo $unitnameRight ?>
                        </div>
                    </div>
                    <div class="data-row">
                        <div class="data-value">
                            <?php
                            for ($i = 0; $i < count($methodnameRight); ++$i) {
                                echo $api->getVAvalue($VAright[$i]->value, $unitId) . " " . $methodnameRight[$i];
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
            } ?>
        </div>
        <div class="element-eye left-eye column">
            <?php
            if ($api->getBestNearVisualAcuity($this->patient, $episode, 'right')) {
                ?>
                <div class="data-row">
                    <div class="data-value">
                        <?php echo $unitnameLeft ?>
                    </div>
                </div>
                <div class="data-row">
                    <div class="data-value">
                        <?php
                        for ($i = 0; $i < count($methodnameLeft); ++$i) {
                            echo $api->getVAvalue($VAleft[$i]->value, $unitId) . " " . $methodnameLeft[$i];
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
// Refraction here
if ($eventtype = EventType::model()->find('class_name = "OphCiExamination"')){
    $eventtypeid = $eventtype->id;
}

if ($eventid = Event::model()->find('event_type_id = ' . $eventtypeid . ' AND episode_id = ' . $episode->id)){
if ($refractelement = $api->getRefractionValues($eventid->id)) {
?>
<section>
    <header class="sub-element-header">
        <h3 class="element-title">Refraction - (exam date <?php echo date("d M Y h:ia",
                strtotime($refractelement->last_modified_date)); ?>)</h3>
    </header>
    <div class="element-fields element-eyes row">
        <div class="element-eye right-eye column">
            <?php if ($refractelement->hasRight()) {
                ?>
                <?php $this->renderPartial($element->view_view . '_OEEyeDraw',
                    array('side' => 'right', 'element' => $refractelement));
                ?>
                <?php
            } else {
                ?>
                Not recorded
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
                Not recorded
                <?php
            } ?>
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
                 } ?>" onClick="switchSides($(this));" data-side="right">
                <div class="element-header right-side">
                    <h4><b>RIGHT</b></h4>
                </div>
                <div class="active-form">
                    <a href="#" class="icon-remove-side remove-side">Remove side</a>
                    <?php echo CHtml::hiddenField('element_id', $element->id, array('class' => 'element_id')); ?>
                    <?php $this->renderPartial('form_Element_OphInBiometry_Measurement_fields', array(
                        'side' => 'right',
                        'element' => $element,
                        'form' => $form,
                        'data' => $data,
                        'measurementInput' => $iolRefValues,
                    )); ?>
                </div>
                <div class="inactive-form">
                    <div class="add-side">
                        <a href="#">
                            Add Right side <span class="icon-add-side"></span>
                        </a>
                    </div>
                </div>
            </div>
            <div id="left-eye-lens"
                 class="element-eye left-eye top-pad right side column <?php if (!$element->hasLeft()) {
                     ?> inactive<?php
                 } ?>" onClick="switchSides($(this));" data-side="left">
                <div class="element-header left-side">
                    <h4><b>LEFT</b></h4>
                </div>
                <div class="active-form">
                    <?php if ($element->hasLeft()) {
                        ?>
                        <div class="active-form">
                            <a href="#" class="icon-remove-side remove-side">Remove side</a>
                            <?php $this->renderPartial('form_Element_OphInBiometry_Measurement_fields', array(
                                'side' => 'left',
                                'element' => $element,
                                'form' => $form,
                                'data' => $data,
                                'measurementInput' => $iolRefValues,
                            )); ?>
                        </div>
                        <div class="inactive-form">
                            <div class="add-side">
                                <a href="#">
                                    Add left side <span class="icon-add-side"></span>
                                </a>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
    </section>
    <script type="text/javascript">
        function switchSides(element) {
            // swith from right active to left
            if ($(element).hasClass('left-eye')) {
                $('#right-eye-lens').addClass('disabled').removeClass('highlighted-lens');
                $('#right-eye-selection').addClass('disabled').removeClass('highlighted-selection');
                $('#right-eye-calculation').addClass('disabled').removeClass('highlighted-calculation');
                $('#right-eye-comments').addClass('disabled').removeClass('highlighted-comments');

                $('#left-eye-lens').removeClass('disabled').addClass('highlighted-lens');
                $('#left-eye-selection').removeClass('disabled').addClass('highlighted-selection');
                $('#left-eye-calculation').removeClass('disabled').addClass('highlighted-calculation');
                $('#left-eye-comments').removeClass('disabled').addClass('highlighted-comments');

            } else if ($(element).hasClass('right-eye')) {
                $('#left-eye-lens').addClass('disabled').removeClass('highlighted-lens');
                $('#left-eye-selection').addClass('disabled').removeClass('highlighted-selection');
                $('#left-eye-calculation').addClass('disabled').removeClass('highlighted-calculation');
                $('#left-eye-comments').addClass('disabled').removeClass('highlighted-comments');

                $('#right-eye-lens').removeClass('disabled').addClass('highlighted-lens');
                $('#right-eye-selection').removeClass('disabled').addClass('highlighted-selection');
                $('#right-eye-calculation').removeClass('disabled').addClass('highlighted-calculation');
                $('#right-eye-comments').removeClass('disabled').addClass('highlighted-comments');
            }
        }
    </script>
</section>
