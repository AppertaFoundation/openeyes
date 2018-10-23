<?php use OEModule\OphCiExamination\models\AllergyEntry; ?>

<div class="element-data full-width">
    <div class="flex-layout flex-top">
        <div class="cols-12">
            <?php if (!count($element->entries)) : ?>
            <div class="data-value not-recorded">
                No diagnoses recorded during this encounter
            </div>
            <?php else : ?>
                <?php
                $entries = [];
                foreach ([(string)AllergyEntry::$NOT_PRESENT, (string)AllergyEntry::$PRESENT] as $key) {
                    $entries[$key] = array_values(array_filter($element->getSortedEntries(), function ($e) use ($key) {
                        return $e->has_allergy === $key;
                    }));
                }
                $max_iter = max(
                    count($entries[(string)AllergyEntry::$NOT_PRESENT]),
                    count($entries[(string)AllergyEntry::$PRESENT])
                );
                ?>
            <div class="flex-layout">
                <table class="borders cols-12">
                    <colgroup>
                        <col class="cols-2">
                        <col class="cols-4">
                        <col class="cols-2">
                        <col class="cols-4">
                    </colgroup>
                    <tbody>
                        <tr>
                            <th>Present</th>
                            <th>
                                <ul>
                                <?php for ($i = 0; $i < $max_iter; $i++) {?>
                                    <li><?= isset($entries[(string)AllergyEntry::$PRESENT][$i]) ? $entries[(string)AllergyEntry::$PRESENT][$i]->getDisplayAllergy() : '' ?></li>
                                <?php } ?>
                                </ul>
                            </th>
                            <th>Not Present</th>
                            <th>
                                <?php for ($i = 0; $i < $max_iter; $i++) {?>
                                <?= isset($entries[(string)AllergyEntry::$NOT_PRESENT][$i]) ? $entries[(string)AllergyEntry::$NOT_PRESENT][$i]->getDisplayAllergy() : '' ?>
                                <?php } ?>
                            </th>
                        </tr>
                    </tbody>
                </table>
            </div>
              
            <?php endif; ?>
        </div>
    </div>
</div>