<?php if ($element->currentOrderedEntries) { ?>
    <ul class="comma-list">
        <?php foreach ($element->currentOrderedEntries as $entry) { ?>
            <li><span class="simple"><?= $entry->getMedicationDisplay() ?></span>
                <span class="detail" style="display: none;"><strong><?= $entry->getMedicationDisplay() ?></strong><?= $entry->getAdministrationDisplay() ? ', ' . $entry->getAdministrationDisplay() : ''?><?= $entry->getDatesDisplay() ? ', ' . $entry->getDatesDisplay() : ''?></span></li>
        <?php } ?>
    </ul>
<?php } else { ?>
    No current medications recorded.
<?php } ?>
