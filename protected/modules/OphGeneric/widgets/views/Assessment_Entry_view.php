<?php
use OEModule\OphGeneric\models\AssessmentEntry;
$medical_retina_open = true;
$medical_retina_entry_values = $entry->getAssessmentEntryRadioButtonValues();
$annotation_items = ['irf', 'srf', 'cysts', 'retinal_thickening', 'ped', 'cmo', 'dmo', 'heamorrhage', 'exudates'];
$no_annotations = true;
foreach ($annotation_items as $item) {
    if (isset($entry->$item) && (int) $entry->$item !== AssessmentEntry::$NONE) {
        $no_annotations = false;
        break;
    }
}

?>

<div class="js-assessment-medical-retina-<?= $eye_side ?> data-group OEModule_OphGeneric_models_Assessment ">
    <table class="cols-full">
        <colgroup>
            <col class="cols-3" span="4">
        </colgroup>
        <tbody>
            <tr>
                <td>
                    <h3>Medical Retina</h3>
                </td>
                <td></td>
                <td></td>
                <td>
                    <?php $this->widget('EyeLateralityWidget', array('eye' => $entry->eye));  ?>
                </td>
            </tr>
        <?php foreach (['crt', 'avg_thickness', 'cst', 'total_vol'] as $item) {?>
            <tr class="no-line">
                <td><?= $entry->getAttributeLabel($item) ?></td>
                <td></td>
                <td></td>
                <td><?= $entry->$item; echo $entry->getMeasurementUnit($item, true)?></td>
            </tr>
        <?php } ?>
            <tr>
                <td colspan="2"></td>
                <td colspan="2">
                    <?php if ($no_annotations) { ?>
                        <span class="fade">No annotations</span>
                    <?php } else { ?>
                        <ul class="dot-list">
                            <?php foreach ($annotation_items as $item) {
                                if ((isset($entry->$item)) && (int) $entry->$item !== AssessmentEntry::$NONE) { ?>
                                    <li>
                                        <span class="highlighter <?= $entry->getEntryColours($entry->$item)?>"><?=  $entry->getAttributeLabel($item) ?>
                                    <?=  ' ' . $entry->getEntryIcon($entry->$item); ?>
                                        </span>
                                    </li>
                                <?php } ?>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                </td>
            </tr>
        </tbody>
    </table>

    <table class="cols-full">
        <colgroup>
            <col class="cols-3" span="4">
        </colgroup>
        <tbody>
            <tr>
                <td>
                    <h3>Glaucoma</h3>
                </td>
                <td></td>
                <td></td>
                <td>
                    <?php $this->widget('EyeLateralityWidget', array('eye' => $entry->eye));  ?>
                </td>
            </tr>
        <?php foreach (['avg_rnfl', 'cct', 'cd_ratio'] as $item) {?>
            <tr class="no-line">
                <td><?= $entry->getAttributeLabel($item) ?></td>
                <td></td>
                <td></td>
                <td><?=  $entry->$item; echo strpos($item, 'cd_ratio') !== false ? '' : ' μm'?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <?php if (isset($entry->comments) && $entry->comments) : ?>
    <hr class="divider">
    <div>
            <i class="oe-i comments-who medium pad-right"></i>
            <span class="user-comment">
                   <?= $entry->textWithLineBreaks('comments') ?>
            </span>
    </div>
    <?php endif; ?>
</div>
