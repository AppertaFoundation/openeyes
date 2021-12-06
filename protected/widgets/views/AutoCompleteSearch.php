<div class="patient-activity">
    <input placeholder="<?= $htmlOptions['placeholder'] ?>" class="cols-<?= $layoutColumns['field'] ?> search autocompletesearch" id="<?= $field_name; ?>" type="text" value="<?= $htmlOptions['value'] ?? "" ?>" name="<?= $field_name; ?>" autocomplete="off">
    <ul class="oe-autocomplete hidden" id="ui-id-1" tabindex="0">
    </ul>
    <?php if ($hide_no_result_msg === false): ?>
        <div class="js-no-result info alert-box hidden">
            No results found.
        </div>
        <div class="js-min-chars info alert-box hidden">
            Minimum of 2 characters
        </div>
    <?php endif; ?>
</div>