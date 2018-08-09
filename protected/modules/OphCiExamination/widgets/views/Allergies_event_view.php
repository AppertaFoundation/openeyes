<?php use OEModule\OphCiExamination\models\AllergyEntry; ?>

<div class="element-data full-width">
    <div class="flex-layout flex-top">
        <div class="cols-11">
            <?php if (!count($element->entries)) : ?>
                <div class="data-value not-recorded">
                    No diagnoses recorded during this encounter
                </div>
            <?php else : ?>
                <div id="js-listview-risks-pro">
                    <ul class="dslash-list large">
                        <?php foreach ($element->getSortedEntries() as $entry) : ?>
                            <li><?= $entry ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="" id="js-listview-risks-full" style="display: none;">
                    <table class="last-left large">
                        <colgroup>
                            <col class="cols-4" span="3">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>Present</th>
                            <th>Not Present</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php

                        $entries = [];
                        foreach ([(string)AllergyEntry::$NOT_PRESENT, (string)AllergyEntry::$PRESENT] as $key) {
                            $entries[$key] = array_filter($element->getSortedEntries(), function ($e) use ($key) {
                                return $e->has_allergy === $key;
                            });
                        }

                        $max_iter = max(
                            count($entries[(string)AllergyEntry::$NOT_PRESENT]),
                            count($entries[(string)AllergyEntry::$PRESENT])
                        );
                        ?>

                        <?php for ($i = 0; $i < $max_iter; $i++) : ?>
                            <tr>
                                <td>
                                    <?= isset($entries[(string)AllergyEntry::$PRESENT][$i]) ?
                                        $entries[(string)AllergyEntry::$PRESENT][$i]->getDisplayAllergy() : '' ?>
                                </td>
                                <td>
                                    <?= isset($entries[(string)AllergyEntry::$NOT_PRESENT][$i]) ?
                                        $entries[(string)AllergyEntry::$NOT_PRESENT][$i]->getDisplayAllergy() : '' ?>
                                </td>
                            </tr>
                        <?php endfor; ?>

                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        <?php if (count($element->entries)) : ?>
            <div>
                <i class="oe-i small js-listview-expand-btn expand" data-list="risks"></i>
            </div>
        <?php endif; ?>
    </div>
</div>