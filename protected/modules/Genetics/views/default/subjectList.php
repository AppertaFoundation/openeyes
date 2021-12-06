<div class="data-group">
    <div class="cols-2 column">
        <label>Subjects:</label>
    </div>
    <div class="cols-5 column end">
        <ul class="subjects_list">
            <?php foreach ($subjects as $subject) :?>
                <li>
                    <a href="/Genetics/subject/edit/<?=$subject->id?>" title="<?=$subject->patient->fullName?>">
                        <?=$subject->patient->fullName?>
                    </a>
                  <?php if (isset($pedigree_id)) : ?>
                    <span class="status"><i>(Status: <?= $subject->statusForPedigree($pedigree_id)?>)</i></span>
                  <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
