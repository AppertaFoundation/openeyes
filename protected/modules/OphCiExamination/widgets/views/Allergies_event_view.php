<div class="element-data full-width">
    <div class="flex-layout flex-top">
        <div class="cols-11">
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
                        $entries = $this->getEntriesByStatus();
                        $max_iter = max(count($entries["0"]), count($entries["1"]));
                    ?>
                    <?php for ($i = 0; $i < $max_iter; $i++) : ?>
                    <tr>
                        <td><?= isset($entries["1"][$i]) ? $entries["1"][$i]->getDisplayAllergy() : ''?></td>
                        <td><?= isset($entries["0"][$i]) ? $entries["0"][$i]->getDisplayAllergy() : ''?></td>
                    </tr>
                    <?php endfor; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div>
            <i class="oe-i small js-listview-expand-btn expand" data-list="risks"></i>
        </div>
    </div>
</div>