<?php
/** @var Medication $medication */
?>
<h3>Alternative Terms</h3>
<table class="standard" id="medication_alternative_terms_tbl">
    <thead>
    <tr>
        <th width="cols-11">Term</th>
        <th width="cols-1">Action</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($medication->medicationSearchIndexes as $row_key => $medicationSearchIndex) : ?>
        <?php
        $id = is_null($medicationSearchIndex->id) ? -1 : $medicationSearchIndex->id;
        ?>
        <tr data-key="<?=$row_key?>">
            <td>
                <input type="hidden" name="Medication[medicationSearchIndexes][<?=$row_key?>][id]" value="<?=$id?>" />
                <?php echo CHtml::textField("Medication[medicationSearchIndexes][".$row_key."][alternative_term]", $medicationSearchIndex->alternative_term, array('class' => 'cols-full')); ?>
            </td>
            <td>
                <a href="javascript:void(0);" class="js-delete-alt-term"><i class="oe-i trash"></i></a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot class="pagination-container">
    <tr>
        <td colspan="3">
            <div class="flex-layout flex-right">
                <button class="button hint green js-add-alt-term" type="button"><i class="oe-i plus pro-theme"></i></button>
            </div>
        </td>
    </tr>
    </tfoot>
</table>

<script id="alt_terms_row_template" type="x-tmpl-mustache">
    <tr data-key="{{ key }}">
        <td>
            <?php echo CHtml::textField('Medication[medicationSearchIndexes][{{key}}][alternative_term]', null, array('class' => 'cols-full')); ?>
        </td>
        <td>
            <a href="javascript:void(0);" class="js-delete-alt-term"><i class="oe-i trash"></i></a>
        </td>
    </tr>
</script>
<script>
    $(function(){
        $(document).on("click", ".js-add-alt-term", function (e) {
            let key = OpenEyes.Util.getNextDataKey('#medication_alternative_terms_tbl tbody tr:last', 'key') || 1;
            let template = $('#alt_terms_row_template').html();
            let rendered = Mustache.render(template, {"key": key});
            $("#medication_alternative_terms_tbl tbody").append(rendered);
        });

        $(document).on("click", ".js-delete-alt-term", function (e) {
            $(e.target).closest("tr").remove();
        });
    });
</script>
