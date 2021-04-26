<?php

/**
 * @var \OEModule\OphCiExamination\models\Element_OphCiExamination_ColourVision $element
 * @var \OEModule\OphCiExamination\widgets\ColourVision $this
 */
?>

<div class="element-data element-eyes flex-layout">
    <div class="js-element-eye right-eye cols-6">
        <div class="data-group">
            <?php if ($element->hasRight()) { ?>
                <table class="cols-10 last-left">
                    <thead>
                    <tr>
                        <th><?= $this->getReadingAttributeLabel('method_id') ?></th>
                        <th><?= $this->getReadingAttributeLabel('value_id') ?></th>
                        <th><?= $this->getReadingAttributeLabel('correctiontype_id') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($element->right_readings as $reading) {
                        ?>
                        <tr>
                            <td><?= $reading->method ?></td>
                            <td><?= $reading->value ?></td>
                            <td><?= $reading->correctiontype ?? "-" ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <div class="data-value not-recorded">None given</div>
            <?php } ?>
        </div>
    </div>
    <div class="js-element-eye left-eye cols-6">
        <div class="data-group">
            <?php if ($element->hasLeft()) { ?>
                <table class="cols-10 last-left">
                    <thead>
                    <tr>
                        <th><?= $this->getReadingAttributeLabel('method_id') ?></th>
                        <th><?= $this->getReadingAttributeLabel('value_id') ?></th>
                        <th><?= $this->getReadingAttributeLabel('correctiontype_id') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($element->left_readings as $reading) {
                        ?>
                        <tr>
                            <td><?= $reading->method ?></td>
                            <td><?= $reading->value ?></td>
                            <td><?= $reading->correctiontype ?? "-" ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
                <?php
            } else {
                ?>
                <div class="data-value not-recorded">None given</div>
            <?php }
            ?>
        </div>
    </div>
</div>
