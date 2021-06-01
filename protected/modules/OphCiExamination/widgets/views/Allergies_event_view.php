<?php use OEModule\OphCiExamination\models\AllergyEntry; ?>

<div class="element-data full-width">
    <div class="flex-layout flex-top">
        <div class="cols-11">
            <?php if ($element->no_allergies_date) {
                echo 'Patient has no allergies (confirmed)';
            } else { ?>
                <?php if (!count($element->entries)) : ?>
                <div class="data-value not-recorded left" style="text-align: left;">
                    <?= 'No entries recorded' ?>
                </div>
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

              <div id="js-listview-allergies-pro" class="cols-full listview-pro">
                    <table class="last-left">
                        <tbody>
                            <tr>
                                <td class="nowrap fade">Present</td>
                                <td>
                                    <?php if (count($entries[(string)AllergyEntry::$PRESENT]) > 0) {?>
                                        <ul class="dot-list">
                                            <?php foreach ($entries[(string)AllergyEntry::$PRESENT] as $entry) : ?>
                                                <li>
                                                    <?= $entry->getDisplayAllergy(); ?>
                                                    <span class="fade"><?= ' ' . $entry->getReactionString(); ?></span>
                                                    <?php if ($entry['comments'] != "") {?>
                                                        <i class="oe-i comments-added small pad js-has-tooltip" data-tooltip-content="<?= $entry['comments']; ?>" pro"="" list="" mode"=""></i>
                                                    <?php } ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php } else { ?>
                                        <span class="none">None</span>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="nowrap fade">Unchecked</td>
                                <td>
                                    <?php if (count($entries[(string)AllergyEntry::$NOT_CHECKED]) > 0) {?>
                                        <ul class="dot-list">
                                            <?php foreach ($entries[(string)AllergyEntry::$NOT_CHECKED] as $entry) : ?>
                                                <li>
                                                    <?= $entry->getDisplayAllergy(); ?>
                                                    <?php if ($entry['comments'] != "") {?>
                                                        <i class="oe-i comments-added small pad js-has-tooltip" data-tooltip-content="<?= $entry['comments']; ?>" pro"="" list="" mode"=""></i>
                                                    <?php } ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php } else { ?>
                                        <span class="none">None</span>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="nowrap fade">Absent</td>
                                <td>
                                    <?php if (count($entries[(string)AllergyEntry::$NOT_PRESENT]) > 0) {?>
                                        <ul class="dot-list">
                                            <?php foreach ($entries[(string)AllergyEntry::$NOT_PRESENT] as $entry) : ?>
                                                <li>
                                                    <?= $entry->getDisplayAllergy(); ?>
                                                    <?php if ($entry['comments'] !== "") {?>
                                                        <i class="oe-i comments-added small pad js-has-tooltip" data-tooltip-content="<?= $entry['comments']; ?>" pro"="" list="" mode"=""></i>
                                                    <?php } ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php } else { ?>
                                        <span class="none">None</span>
                                    <?php } ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div id="js-listview-allergies-full" class="cols-full listview-full" style="display: none;">

                    <div class="flex-layout">

                        <div class="cols-2">Present</div>

                        <table class="last-left">
                          <colgroup>
                            <col class="cols-4">
                          </colgroup>
                        <tbody>
                            <?php if (count($entries[(string)AllergyEntry::$PRESENT]) >0) { ?>
                                <?php for ($i = 0; $i < $max_iter; $i++) :?>
                                    <?php if (isset($entries[(string)AllergyEntry::$PRESENT][$i])) {?>
                                        <tr>
                                            <td><?= $entries[(string)AllergyEntry::$PRESENT][$i]->getDisplayAllergy(); ?></td>
                                            <td><?= ($entries[(string)AllergyEntry::$PRESENT][$i]['comments'] !== "" ? $entries[(string)AllergyEntry::$PRESENT][$i]['comments'] : '<span class="none">None</span>'); ?></td>
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
                            <?php if (count($entries[(string)AllergyEntry::$NOT_CHECKED]) >0) { ?>
                                <?php for ($i = 0; $i < $max_iter; $i++) :?>
                                    <?php if (isset($entries[(string)AllergyEntry::$NOT_CHECKED][$i])) {?>
                                        <tr>
                                            <td><?= $entries[(string)AllergyEntry::$NOT_CHECKED][$i]->getDisplayAllergy(); ?></td>
                                            <td><?= ($entries[(string)AllergyEntry::$NOT_CHECKED][$i]['comments'] !== "" ? $entries[(string)AllergyEntry::$NOT_CHECKED][$i]['comments'] : '<span class="none">None</span>'); ?></td>
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
                            <?php if (count($entries[(string)AllergyEntry::$NOT_PRESENT]) >0) { ?>
                                <?php for ($i = 0; $i < $max_iter; $i++) :?>
                                    <?php if (isset($entries[(string)AllergyEntry::$NOT_PRESENT][$i])) {?>
                                        <tr>
                                            <td><?= $entries[(string)AllergyEntry::$NOT_PRESENT][$i]->getDisplayAllergy(); ?></td>
                                            <td><?= ($entries[(string)AllergyEntry::$NOT_PRESENT][$i]['comments'] !== "" ? $entries[(string)AllergyEntry::$NOT_PRESENT][$i]['comments'] : '<span class="none">None</span>'); ?></td>
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

                </div>
                <?php endif; ?>
        </div>
                <?php if (count($element->entries)) : ?>
            <div>
                <i class="oe-i small js-listview-expand-btn expand" data-list="allergies"></i>
            </div>
                <?php endif; ?>
            <?php } ?>
    </div>
</div>