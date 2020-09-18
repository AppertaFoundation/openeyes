<table id="<?= $id ?>">
    <colgroup>
        <col class="cols-8">
        <col>
    </colgroup>
    <thead style="display:none;">
        <!-- These hidden headers are required for Katalon / automated tests to find corect columns -->
        <tr>
            <th>Drug</th>
            <th>Tooltip</th>
            <th>Date</th>
            <th>Link</th>
        </tr>
    </thead>
    <tbody>
        <?php $index = 0; ?>
        <?php foreach ($entries as $entry) : ?>
            <tr data-key="<?= $index ?>">
                <td>
                    <?= $entry->getMedicationDisplay() ?>
                    <?php
                    $comments = $entry->getComments();
                    if (!empty($comments)) { ?>
                        <i class="oe-i comments-who small pad js-has-tooltip" data-tt-type="basic" data-tooltip-content="<em><?= $comments ?></em>">
                        </i> <?php
                            }
                                ?>
                </td>
                <td>
                    <?php
                    $info_box = new MedicationInfoBox();
                    $info_box->medication_id = $entry->medication->id;
                    $info_box->init();

                    $tooltip_content = $entry->getTooltipContent() . "<br />" . $info_box->getAppendLabel();
                    if (!empty($tooltip_content)) { ?>
                        <i class="oe-i <?= $info_box->getIcon(); ?> small <?= $pro_theme ?> js-has-tooltip pad-right" data-tooltip-content="<?= $tooltip_content ?>">
                        </i>
                    <?php } ?>

                </td>
                <td>
                    <?php $earliest_entry = $entry->getEarliestEntry($entry); ?>
                    <span class="oe-date"><?= $current ? $earliest_entry->getStartDateDisplay() : $entry->getEndDateDisplay() ?></span>
                </td>
                <td>
                    <?php if ($show_link) { ?>
                        <?php
                        if (($entry->prescription_item_id && isset($entry->prescription_item->prescription->event))) {
                            $link = $this->getPrescriptionLink($entry->prescription_item);
                        } else {
                            $link = $entry->isPrescription() ? $this->getPrescriptionLink($entry) : $this->getExaminationLink($entry);
                        }
                        $tooltip_content = 'View' . (strpos(strtolower($link), 'prescription') ? ' prescription' : ' examination'); ?>
                        <a href="<?= $link ?>"><span class="js-has-tooltip fa oe-i direction-right-circle small pad <?= $pro_theme ?>" data-tooltip-content="<?= $tooltip_content ?>"></span></a>
                    <?php } else { ?>
                        <i class="oe-i"></i>
                    <?php } ?>
                </td>
            </tr>
            <?php $index++; ?>
        <?php endforeach; ?>
    </tbody>
</table>