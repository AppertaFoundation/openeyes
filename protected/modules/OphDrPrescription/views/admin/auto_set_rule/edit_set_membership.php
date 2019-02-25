<label>Include medications from the following sets:</label>
<?php
    /** @var MedicationSetAutoRule $medicationSetAutoRule */
    $rowkey = 0;
    $setlist = CHtml::listData(MedicationSet::model()->findAll(['order' => 'name']), 'id', 'name');
?>
<script id="set_membership_row_template" type="x-tmpl-mustache">
     <tr data-key="{{ key }}">
        <input type="hidden" name="MedicationSetAutoRule[sets][id][]" value="-1" />
        <td>
            <?php echo CHtml::dropDownList('MedicationSetAutoRule[sets][medication_set_id][]', null, $setlist); ?>
        </td>
        <td>
            <a href="javascript:void(0);" class="js-delete-set-membership"><i class="oe-i trash"></i></a>
        </td>
    </tr>
</script>
<script type="text/javascript">
    $(function(){
        $(document).on("click", ".js-add-set-membership", function (e) {
            var lastkey = $("#medication_set_membership_tbl tbody tr:last").attr("data-key");
            var key = parseInt(lastkey) + 1;
            var template = $('#set_membership_row_template').html();
            Mustache.parse(template);
            var rendered = Mustache.render(template, {"key": key});
            $("#medication_set_membership_tbl tbody").append(rendered);
        });

        $(document).on("click", ".js-delete-set-membership", function (e) {
            $(e.target).closest("tr").remove();
        });
    });
</script>
<table class="standard" id="medication_set_membership_tbl">
    <thead>
    <tr>
        <th width="25%">Set name</th>
        <th width="25%">Action</th>
    </tr>
    </thead>
    <tbody>
	<?php if(!is_null($medicationSetAutoRule)): ?>
    <?php foreach ($medicationSetAutoRule->medicationSetAutoRuleSetMemberships as $membership): ?>
        <tr data-key="<?php echo ++$rowkey; ?>">
            <input type="hidden" name="MedicationSetAutoRule[sets][id][]" value="<?=$membership->id?>" />
            <td>
                <?php echo CHtml::dropDownList('MedicationSetAutoRule[sets][medication_set_id][]', $membership->medication_set_id, $setlist); ?>
            </td>
            <td>
                <a href="javascript:void(0);" class="js-delete-set-membership"><i class="oe-i trash"></i></a>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
    <tfoot class="pagination-container">
    <tr>
        <td colspan="3">
            <div class="flex-layout flex-right">
                <button class="button hint green js-add-set-membership" type="button"><i class="oe-i plus pro-theme"></i></button>
            </div>
        </td>
    </tr>
    </tfoot>
</table>
