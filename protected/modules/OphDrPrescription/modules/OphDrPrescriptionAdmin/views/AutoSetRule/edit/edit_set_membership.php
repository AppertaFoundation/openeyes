<h3>Include medications from the following sets:</h3>
<?php
$setlist = array_map(function ($e) {
    return ['id' => $e->id, 'label' => $e->name];
}, MedicationSet::model()->findAll(['order' => 'name']));
?>
<script id="set_membership_row_template" type="x-tmpl-mustache">
     <tr data-key="{{ key }}">
        <input type="hidden" name="MedicationSetAutoRuleSetMemberships[{{ key }}][id]" value="" />
        <input type="hidden" name="MedicationSetAutoRuleSetMemberships[{{ key }}][source_medication_set_id]" value="{{set_id}}" />
        <td>{{set_name}}</td>
        <td>
            <a href="javascript:void(0);" class="js-delete-set-membership"><i class="oe-i trash"></i></a>
        </td>
    </tr>

</script>
<script type="text/javascript">
    $(document).on("click", ".js-delete-set-membership", function (e) {
        $(e.target).closest("tr").remove();
    });
</script>
<table class="standard last-right" id="medication_set_membership_tbl">
    <thead>
    <tr>
        <th width="25%">Set name</th>
        <th width="25%">Action</th>
    </tr>
    </thead>
    <tbody>
    <?php if (!is_null($set)): ?>
        <?php foreach ($set->medicationSetAutoRuleSetMemberships as $row_key => $membership): ?>
            <tr data-key="<?=$row_key; ?>">
                <input type="hidden" name="MedicationSetAutoRuleSetMemberships[<?=$row_key; ?>][id]" value="<?= (isset($membership->id) ? $membership->id : '') ?>"/>
                <input type="hidden" name="MedicationSetAutoRuleSetMemberships[<?=$row_key; ?>][source_medication_set_id]"
                       value="<?= $membership->source_medication_set_id ?>"/>
                <td><?= \CHtml::encode($membership->sourceMedicationSet->name); ?></td>
                <td><a href="javascript:void(0);" class="js-delete-set-membership"><i class="oe-i trash"></i></a></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
    <tfoot class="pagination-container">
    <tr>
        <td colspan="3">
            <div class="flex-layout flex-right">
                <button class="button hint green js-add-set-membership" type="button"><i
                            class="oe-i plus pro-theme"></i></button>
                <script type="text/javascript">
                    new OpenEyes.UI.AdderDialog({
                        openButton: $('.js-add-set-membership'),
                        itemSets: [
                            new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode($setlist) ?>, {
                                'multiSelect': true,
                                header: "Medication sets"
                            }),
                        ],
                        onReturn: function (adderDialog, selectedItems) {

                            $.each(selectedItems, function (i, item) {
                                let lastkey = $("#medication_set_membership_tbl > tbody > tr:last").attr("data-key");
                                if (isNaN(lastkey)) {
                                    lastkey = 0;
                                }
                                let key = parseInt(lastkey) + 1;
                                let template = $('#set_membership_row_template').html();
                                Mustache.parse(template);
                                let rendered = Mustache.render(template, {
                                    "key": key,
                                    "set_id": item.id,
                                    "set_name": item.label
                                });
                                $("#medication_set_membership_tbl > tbody").append(rendered);
                            });

                            return true;
                        },
                        enableCustomSearchEntries: true,
                    });
                </script>
            </div>
        </td>
    </tr>
    </tfoot>
</table>
