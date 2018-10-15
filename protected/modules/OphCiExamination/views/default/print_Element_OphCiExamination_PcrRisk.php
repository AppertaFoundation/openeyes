<div class="element-data element-eyes flex-layout">
    <?php
    $pcr = new PcrRisk();
    foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) {
    ?>
        <div class="cols-6 <?= $eye_side ?>-eye <?= $page_side ?>" data-side="<?= $eye_side ?>">
            <?php if ($element->{$eye_side . '_glaucoma'}) { ?>
            <div class="cols-12">
                <table>
                    <tbody>
                        <tr>
                            <td class="pcr-risk-div">
                                <span class="highlighter large-text">PCR Risk
                                    <span class="pcr-span"><?php echo $element->{$eye_side . '_pcr_risk'} ? : 'N/A' ?></span>%
                                </span>
                            </td>
                            <td>
                                <span>
                                    Risk compared  to average eye <span
                                        class="pcr-erisk highlighter large-text">x<?php echo $element->{$eye_side . '_excess_risk'} ?></span>
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div id="js-listview-pcr-risk-<?= $page_side ?>-full">
                    <table>
                        <tbody>
                            <tr style="text-align: left;">
                                <td>
                                    <div class="data-label">
                                        <?php echo $element->getAttributeLabel($eye_side . '_glaucoma') ?>:
                                    </div>
                                </td>
                                <td>
                                    <div class="data-value">
                                        <?php echo $pcr->displayValues($element->{$eye_side . '_glaucoma'}, 'glaucoma') ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="data-label">
                                        <?php echo $element->getAttributeLabel($eye_side . '_pxf') ?>:
                                    </div>
                                </td>
                                <td>
                                    <div class="data-value">
                                        <?php echo $pcr->displayValues($element->{$eye_side . '_pxf'}) ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="data-label">
                                        <?php echo $element->getAttributeLabel($eye_side . '_diabetic') ?>:
                                    </div>
                                </td>
                                <td>
                                    <div class="data-value">
                                        <?php echo $pcr->displayValues($element->{$eye_side . '_diabetic'}, 'diabetic') ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="data-label">
                                        <?php echo $element->getAttributeLabel($eye_side . '_pupil_size') ?>:
                                    </div>
                                </td>
                                <td>
                                    <div class="data-value">
                                        <?php echo $element->{$eye_side . '_pupil_size'} ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="data-label">
                                        <?php echo $element->getAttributeLabel($eye_side . '_no_fundal_view') ?>:
                                    </div>
                                </td>
                                <td>
                                    <div class="data-value">
                                        <?php echo $pcr->displayValues($element->{$eye_side . '_no_fundal_view'}) ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="data-label">
                                        <?php echo $element->getAttributeLabel($eye_side . '_axial_length_group') ?>:
                                    </div>
                                </td>
                                <td>
                                    <div class="data-value">
                                        <?php echo $pcr->displayValues($element->{$eye_side . '_axial_length_group'}, 'axial') ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div
                                        class="data-label"><?php echo $element->getAttributeLabel($eye_side . '_brunescent_white_cataract') ?>
                                        :
                                    </div>
                                </td>
                                <td>
                                    <div class="data-value">
                                        <?php echo $pcr->displayValues($element->{$eye_side . '_brunescent_white_cataract'}) ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="data-label">
                                        <?php echo $element->getAttributeLabel($eye_side . '_alpha_receptor_blocker') ?>:
                                    </div>
                                </td>
                                <td>
                                    <div class="data-value">
                                        <?php echo $pcr->displayValues($element->{$eye_side . '_alpha_receptor_blocker'}) ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="data-label">
                                        <?php echo $element->getAttributeLabel($eye_side . '_doctor_grade_id') ?>:
                                    </div>
                                </td>
                                <td>
                                    <div class="data-value">
                                        <?php
                                        if ($element->{$eye_side . '_doctor'}) {
                                            echo $element->{$eye_side . '_doctor'}->grade;
                                        }
                                        ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="data-label"><?php echo $element->getAttributeLabel($eye_side . '_can_lie_flat') ?>:</div>
                                </td>
                                <td>
                                    <div class="data-value"><?php echo $pcr->displayValues($element->{$eye_side . '_can_lie_flat'}) ?></div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php } else { ?>
                Not recorded
            <?php } ?>
        </div>
    <?php } ?>
</div>