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
<style>
    .readonly-div {
        height: auto;
        border: 1px solid #6b8aaa;
        background-color: #dddddd;
    }
</style>
<?php
$model_name = CHtml::modelName($element);
$exam_api = Yii::app()->moduleAPI->get('OphCiExamination');

$target_refraction_values = $exam_api->getLatestCataractSurgicalManagementSidedTargetRefractionValues($this->patient);
$latest_cataract_surgical_management_event_link = $exam_api->getLatestCataractSurgicalManagementEventLink($this->patient);
?>

<div class="element-fields element-eyes" id="<?= $model_name ?>_element">
    <?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>
    <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) : ?>
        <div id="<?php echo $eye_side ?>-eye-calculation"
             class="js-element-eye <?php echo $eye_side ?>-eye <?php echo $page_side ?> column <?php if (!$element->hasEye($eye_side)) {
                    ?> inactive<?php
                                   } ?>"
             data-side="<?php echo $eye_side ?>" style="display: <?= $this->action->id === "create" ? "none" : "" ?>">
            <div class="active-form" style="<?= !$element->hasEye($eye_side) ? 'display: none;' : '' ?>">
                <?php $this->renderPartial(
                    'form_Element_OphInBiometry_Calculation_fields',
                    ['side' => $eye_side, 'element' => $element, 'form' => $form, 'data' => $data,
                        'refraction_target' => $target_refraction_values[$eye_side] ?? null,
                        'latest_cataract_surgical_management_event_link' => $latest_cataract_surgical_management_event_link]
                ); ?>
            </div>
            <div class="inactive-form" style="<?= $element->hasEye($eye_side) ? 'display: none;' : '' ?>">
                <div class="add-side">
                    Set <?php echo $eye_side ?> side lens type
                </div>
            </div>
        </div>
        <?php if ($this->action->id === "create") { ?>
            <div class="js-element-eye <?= $eye_side ?>-eye column">
                Calculation editing is not available.
            </div>
        <?php } ?>
    <?php endforeach; ?>
</div>
<div class="element-fields element-eyes">
    <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) : ?>
        <div id="<?php echo $eye_side ?>-eye-comments"
             class="js-element-eye <?php echo $eye_side ?>-eye <?php echo $page_side ?> disabled"
             data-side="<?php echo $eye_side ?>" style="display: <?= $this->action->id === "create" ? "none" : "" ?>">
            <div id="biometry-<?= $eye_side ?>-comments" class="active-form js-comment-container"
                 style="<?= !$element->hasEye($eye_side) || !$element->{'comments_' . $eye_side} ? 'display: none;' : '' ?>"
                 data-comment-button="#biometry-<?= $eye_side ?>-comment-button">
                <?= \CHtml::activeTextArea($element, 'comments_' . $eye_side,
                    [
                        'rows' => 1,
                        'placeholder' => 'General Comments',
                        'class' => 'autosize cols-full js-comment-field',
                    ]
                ) ?>
            </div>
        </div>
    <?php endforeach; ?>

</div>

<div id="comments" style="background-color: inherit">
    <span class="field-info large-12" style="display: <?= $this->action->id === "create" ? "none" : "" ?>">
        <?php
        if ($this->is_auto) {
            if (!$this->getAutoBiometryEventData($this->event->id)[0]->is700() || $element->{'comments'}) {
                echo 'Device Comments:';
                echo '<div class="readonly-box">' . $element->{'comments'} . '<br></div>';
            }
        } else {
            ?>
            <span class="field-info">Comments:</span>
            <?php
            echo $form->textField($element, 'comments', array('style' => 'width:1027px;', 'nowrapper' => true), null);
        }
        ?>
    </span>
</div>
<script>
    ready(function () {
        const targetRefractionValues = <?=CJSON::encode($target_refraction_values)?>;
        const elementContainerId = "<?= $model_name ?>_element";
        const elementContainer = document.getElementById(elementContainerId);
        const changeEvent = new Event('change');

        elementContainer.querySelectorAll('.js-target-refraction').forEach(function (targetRefractionInput) {
            targetRefractionInput.addEventListener('change', function () {
                const warningContainer = document.getElementById(this.dataset.side + '_refractive_target_warning_container');
                const side = this.dataset.side;

                if (targetRefractionValues[side] !== null &&
                    targetRefractionValues[side] !== this.value) {
                    warningContainer.style.display = '';
                } else {
                    warningContainer.style.display = 'none';
                }

                showOrHidePredictedRefractionWarning(side);
            });

            targetRefractionInput.dispatchEvent(changeEvent);
        });
    })
</script>
