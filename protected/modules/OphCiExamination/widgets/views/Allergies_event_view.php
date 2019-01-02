<?php use OEModule\OphCiExamination\models\AllergyEntry; ?>

<div class="element-data full-width">
    <div class="flex-layout flex-top">
        <div class="cols-11">
            <?php if($element->no_allergies_date){
            echo 'Patient has no allergies (confirmed)';
            } else { ?>
            <?php if (!count($element->entries)) : ?>
                <div class="data-value not-recorded left" style="text-align: left;">
                 <?= 'No entries recorded' ?>
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
              <div id="js-listview-allergies-pro">
                <?php if(count($entries[(string)AllergyEntry::$PRESENT]) > 0) {?>
                  <ul class="dot-list large">
                    <li>Present:</li>
                      <?php foreach ($entries[(string)AllergyEntry::$PRESENT] as $entry) : ?>
                        <li><?= $entry->getDisplayAllergy(); ?></li>
                      <?php endforeach; ?>
                  </ul>
                <?php } ?>
                <?php if (count($entries[(string)AllergyEntry::$NOT_PRESENT]) > 0 ) { ?>
                  <ul class="dot-list large">
                    <li>Not Present:</li>
                      <?php foreach ($entries[(string)AllergyEntry::$NOT_PRESENT] as $entry) : ?>
                        <li><?= $entry->getDisplayAllergy(); ?></li>
                      <?php endforeach; ?>
                  </ul>
                <?php } ?>
              </div>
                <div class="" id="js-listview-allergies-full" style="display: none;">
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
                        <?php for ($i = 0; $i < $max_iter; $i++) :?>
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
                <i class="oe-i small js-listview-expand-btn expand" data-list="allergies"></i>
            </div>
        <?php endif; ?>
        <?php } ?>
    </div>
</div>