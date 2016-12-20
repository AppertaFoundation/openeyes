<div class="row field-row">
    <div class="large-2 column">
        <label>Subjects:</label>
    </div>
    <div class="large-5 column end">
        <ul class="subjects_list">
            <?php foreach($subjects as $subject):?>
                <li>
                    <a href="/Genetics/subject/edit/<?=$subject->id?>" title="<?=$subject->patient->fullName?>">
                        <?=$subject->patient->fullName?>
                    </a>
                    <span class="status"><i>(Status: <?= $subject->statusForPedigree($pedigree_id)?>)</i></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
