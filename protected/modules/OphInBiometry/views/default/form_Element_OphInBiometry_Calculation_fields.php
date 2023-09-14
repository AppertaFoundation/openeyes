<div class="data-group">
    <div id="<?= $side ?>_refractive_target_warning_container" data-test="<?= $side ?>-refractive-target-warning"
         class="alert-box issue" style="display: none">
        <?= Element_OphInBiometry_Calculation::getTargetDiffersFromCataractSurgicalManagementTargetWarning($refraction_target); ?>
        <a href="<?= $latest_cataract_surgical_management_event_link ?>"><i
                class="oe-i pro-theme direction-right-circle small pad"></i></a>
    </div>
    <table class="cols-11 last-left">
        <colgroup>
            <col class="cols-4">
            <col class="cols-4">
            <col class="cols-4">
        </colgroup>
        <tbody>
        <tr>
            <td>
                Target Refraction:
            </td>
            <td>
                <?php
                if (($element->{'target_refraction_' . $side} === "" || is_null($element->{'target_refraction_' . $side}))
                    && !Yii::app()->request->isPostRequest) {
                    $element->{'target_refraction_' . $side} = $refraction_target;
                }
                ?>
                <?php echo $form->textField(
                    $element,
                    'target_refraction_' . $side,
                    [
                        'class' => 'js-target-refraction',
                        'nowrapper' => true,
                        'data-side' => $side,
                        'data-test' => $side . '-target-refraction',
                    ],
                    null,
                    array('label' => 4, 'field' => 2)
                ) ?>
            </td>
            <td>
                <button id="biometry-<?= $side ?>-comment-button"
                        class="button js-add-comments"
                        data-comment-container="#biometry-<?= $side ?>-comments"
                        type="button" style="<?= $element->{'comments_' . $side} ? 'visibility: hidden;' : '' ?>"
                >
                    <i class="oe-i comments small-icon"></i>
                </button>
            </td>
        </tr>
        </tbody>
    </table>
</div>
