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
                        <col class="cols-5">
                        <col class="cols-5">
                    </colgroup>
                    <tbody>
                        <tr>
                            <th>Label</th>
                            <th>Data</th>
                            <th>Comments</th>
                        </tr>
                        <?php for ($i = 0; $i < $max_iter; $i++) :?>
                            <?php if(isset($entries[(string)AllergyEntry::$PRESENT][$i])){?>
                                <tr>
                                    <th>Present</th>
                                    <th><?= $entries[(string)AllergyEntry::$PRESENT][$i]->getDisplayAllergy(); ?></th>
                                    <th><?= $entries[(string)AllergyEntry::$PRESENT][$i]['comments']; ?></th>
                                </tr>
                            <?php } ?>
                            <?php if(isset($entries[(string)AllergyEntry::$NOT_PRESENT][$i])){?>
                                <tr>
                                    <th>Not Present</th>
                                    <th><?= $entries[(string)AllergyEntry::$NOT_PRESENT][$i]->getDisplayAllergy(); ?></th>
                                    <th><?= $entries[(string)AllergyEntry::$NOT_PRESENT][$i]['comments']; ?></th>
                                </tr>
                            <?php } ?>
                        <?php endfor; ?>                            
                    </tbody>                    
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<div class="element-data full-width">
    <div class="flex-layout flex-top">
        <div class="cols-12">
            <hr class="divider">
            <table class="borders cols-12">
                <colgroup>
                    <col class="cols-2">
                    <col class="cols-10">
                </colgroup>
                <tr>
                    <th>Element comments</th>
                    <th>These are general comments for the Event</th>
                </tr>
            </table>
        </div>
    </div>
</div>