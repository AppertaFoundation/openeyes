<?php use OEModule\OphCiExamination\models\AllergyEntry;
use OEModule\OphCiExamination\models\OphCiExaminationAllergy; ?>

<div class="element-data full-width">
    <div class="flex-layout flex-top">
        <div class="cols-12">
            <?php if (!count($element->entries)) : ?>
                <div class="data-value not-recorded">Patient has no allergies (confirmed)</div>
            <?php else : ?>
                <?php
                $entries = [];
                foreach ([(string)AllergyEntry::$NOT_PRESENT, (string)AllergyEntry::$PRESENT, (string)AllergyEntry::$NOT_CHECKED] as $key) {
                    $entries[$key] = array_values(array_filter($element->getSortedEntries(), function ($e) use ($key) {
                        return $e->has_allergy === $key;
                    }));
                }
                $max_iter = max(
                    count($entries[(string)AllergyEntry::$NOT_PRESENT]),
                    count($entries[(string)AllergyEntry::$PRESENT]),
                    count($entries[(string)AllergyEntry::$NOT_CHECKED])
                );
                ?>
                <div class="flex-layout">
                    <div class="cols-2">Present</div>
                    <table class="last-left">
                        <colgroup>
                            <col class="cols-4">
                        </colgroup>
                        <tbody>
                        <?php if (count($entries[(string)AllergyEntry::$PRESENT]) > 0) { ?>
                            <?php for ($i = 0; $i < $max_iter; $i++) : ?>
                                <?php if (isset($entries[(string)AllergyEntry::$PRESENT][$i])) { ?>
                                    <tr>
                                        <td><?= $entries[(string)AllergyEntry::$PRESENT][$i]->getDisplayAllergy(); ?></td>
                                        <td><?= ($entries[(string)AllergyEntry::$PRESENT][$i]['comments'] !== "" ?
                                                $entries[(string)AllergyEntry::$PRESENT][$i]['comments'] :
                                                '<span class="none">None</span>'); ?></td>
                                    </tr>
                                <?php } ?>
                            <?php endfor; ?>
                        <?php } else { ?>
                            <tr>
                                <td>None</td>
                                <td><span class="none">None</span></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div><!-- .flex-layout -->

                <hr class="divider">

                <div class="flex-layout">
                    <div class="cols-2">Unchecked</div>
                    <table class="last-left">
                        <colgroup>
                            <col class="cols-4">
                        </colgroup>
                        <tbody>
                        <?php
                        if (count($entries[(string)AllergyEntry::$NOT_CHECKED]) >0) { ?>
                            <?php for ($i = 0; $i < $max_iter; $i++) :?>
                                <?php if (isset($entries[(string)AllergyEntry::$NOT_CHECKED][$i])) {?>
                                    <tr>
                                        <td><?= $entries[(string)AllergyEntry::$NOT_CHECKED][$i]->getDisplayAllergy(); ?></td>
                                        <td><?= ($entries[(string)AllergyEntry::$NOT_CHECKED][$i]['comments'] !== "" ?
                                                $entries[(string)AllergyEntry::$NOT_CHECKED][$i]['comments'] :
                                                '<span class="none">None</span>'); ?></td>
                                    </tr>
                                <?php } ?>
                            <?php endfor; ?>
                        <?php } else { ?>
                            <tr>
                                <td>None</td>
                                <td><span class="none">None</span></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div><!-- .flex-layout -->

                <hr class="divider">

                <div class="flex-layout">
                    <div class="cols-2">Absent</div>
                    <table class="last-left">
                        <colgroup>
                            <col class="cols-4">
                        </colgroup>
                        <tbody>
                        <?php if (count($entries[(string)AllergyEntry::$NOT_PRESENT]) > 0) { ?>
                            <?php for ($i = 0; $i < $max_iter; $i++) : ?>
                                <?php if (isset($entries[(string)AllergyEntry::$NOT_PRESENT][$i])) { ?>
                                    <tr>
                                        <td><?= $entries[(string)AllergyEntry::$NOT_PRESENT][$i]->getDisplayAllergy(); ?></td>
                                        <td><?= ($entries[(string)AllergyEntry::$NOT_PRESENT][$i]['comments'] !== "" ?
                                                $entries[(string)AllergyEntry::$NOT_PRESENT][$i]['comments'] :
                                                '<span class="none">None</span>'); ?></td>
                                    </tr>
                                <?php } ?>
                            <?php endfor; ?>
                        <?php } else { ?>
                            <tr>
                                <td>None</td>
                                <td><span class="none">None</span></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div><!-- .flex-layout -->
            <?php endif; ?>
        </div>
    </div>
</div>