<?php

/**
 * @var $visit WorklistPatient
 * @var $patient Patient
 * @var $partial int
 */
?>
<div class="slide-open">
    <div class="patient"><?= strtoupper($patient->last_name) . ', ' . $patient->first_name . ' (' . $patient->title . ')'?></div>
    <h3 class="title">Pathway notes</h3>
    <div class="step-content">
        <table class="notes">
            <colgroup>
                <col class="cols-4">
            </colgroup>
            <tbody>
            <?php if ($visit->pathway) {
                foreach ($visit->pathway->steps as $step) {
                    if ($step->comment) { ?>
                    <tr>
                        <th>
                            <?= $step->comment->doctor->getFullNameAndTitle() ?>
                            <div>(<?= $step->long_name ?>)</div>
                        </th>
                        <td><?= $step->comment->comment ?></td>
                    </tr>
                <?php }
                }
            } ?>
            </tbody>
        </table>
    </div>
    <hr class="divider">
    <?php
        $this->renderPartial(
            'step_components/_comment',
            array(
                'partial' => $partial,
                'model' => $visit->pathway,
                'visit' => $visit,
            )
        );
        ?>
</div>
<?php if (!$partial) { ?>
<div class="close-icon-btn">
    <i class="oe-i remove-circle medium-icon"></i>
</div>
<?php } ?>