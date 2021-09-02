<script id="individual_medications_tbl_row_template" type="x-tmpl-mustache">
    <tr data-key="{{ key }}">
       <td>
            <input type="hidden" name="MedicationSetAutoRuleMedication[{{ key }}][id]" value="-1" />
            <input type="hidden" name="MedicationSetAutoRuleMedication[{{ key }}][medication_id]" value="{{ id }}" />
            {{ &infobox }} {{ medication }}
        </td>
        <td align="center">
            <input type="hidden" name="MedicationSetAutoRuleMedication[{{ key }}][include_parent]" value="{{ include_parents_id }}" />
            {{include_parents}}
        </td>
        <td align="center">
            <input type="hidden" name="MedicationSetAutoRuleMedication[{{ key }}][include_children]" value="{{ include_children_id }}" />
            {{include_children}}
        </td>
        <td>
            <a href="javascript:void(0);" class="js-delete-medication-assignment"><i class="oe-i trash"></i></a>
        </td>
    </tr>

</script>
<script type="text/javascript">
    $(document).on("click", ".js-delete-medication-assignment", function (e) {
        $(e.target).closest("tr").remove();
    });
</script>
<h3>Include the following individual medications:</h3>
<table class="standard" id="individual_medications_tbl">
    <thead>
    <tr>
        <th width="60%">Item</th>
        <th width="15%">Include parents</th>
        <th width="15%">Include children</th>
        <th width="15%">Action</th>
    </tr>
    </thead>
    <tbody>
    <?php if (!is_null($set)) : ?>
        <?php foreach ($set->medicationSetAutoRuleMedications as $row_key => $assignment) : ?>
            <tr data-key="<?= $row_key ?>">
                <td>
                    <input type="hidden" name="MedicationSetAutoRuleMedication[<?= $row_key; ?>][id]"
                           value="<?= $assignment->id ?>"/>
                    <input type="hidden" name="MedicationSetAutoRuleMedication[<?= $row_key; ?>][medication_id]"
                           value="<?= $assignment->medication_id ?>"/>
                    <?php $this->widget('MedicationInfoBox', array('medication_id' => $assignment->medication_id));
                    ; ?><?php echo $assignment->medication->preferred_term; ?>
                </td>
                <td align="center">
                    <input type="hidden" name="MedicationSetAutoRuleMedication[<?= $row_key; ?>][include_parent]"
                           value="<?= $assignment->include_parent ?>"/>
                    <?= $assignment->include_parent ? 'Yes' : 'No'; ?>
                </td>
                <td align="center">
                    <input type="hidden" name="MedicationSetAutoRuleMedication[<?= $row_key; ?>][include_children]"
                           value="<?= $assignment->include_children ?>"/>
                    <?= $assignment->include_children ? 'Yes' : 'No' ?>
                </td>
                <td>
                    <a href="javascript:void(0);" class="js-delete-medication-assignment"><i class="oe-i trash"></i></a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
    <tfoot class="pagination-container">
    <tr>
        <td colspan="4">
            <div class="flex-layout flex-right">
                <button id="add-medication-assignment" class="button hint green" type="button"><i
                            class="oe-i plus pro-theme"></i></button>
            </div>
            <script type="text/javascript">
                new OpenEyes.UI.AdderDialog({
                    openButton: $('#add-medication-assignment'),
                    itemSets: [
                        new OpenEyes.UI.AdderDialog.ItemSet([{"id": 1, "label": "Yes"}, {
                            "id": 0,
                            "label": "No"
                        }], {"id": "inc_parents", 'multiSelect': false, header: "Include parents?"}),
                        new OpenEyes.UI.AdderDialog.ItemSet([{"id": 1, "label": "Yes"}, {
                            "id": 0,
                            "label": "No"
                        }], {"id": "inc_children", 'multiSelect': false, header: "Include children?"})
                    ],
                    onReturn: function (adderDialog, selectedItems) {

                        let row = {};
                        $.each(selectedItems, function (i, e) {
                            if (typeof e.itemSet === "undefined") {
                                row.medication = Object.assign({}, e);
                                return;
                            }
                            if (e.itemSet.options.id === "inc_parents") {
                                row.include_parents = e.id
                            } else if (e.itemSet.options.id === "inc_children") {
                                row.include_children = e.id;
                            }
                        });

                        if (typeof row.medication === "undefined") {
                            return false;
                        }

                        let $body = $("#individual_medications_tbl > tbody");
                        let lastkey = $body.find("tr:last").attr("data-key");
                        if (isNaN(lastkey)) {
                            lastkey = 0;
                        }
                        let key = parseInt(lastkey) + 1;
                        let template = $('#individual_medications_tbl_row_template').html();
                        Mustache.parse(template);
                        let rendered = Mustache.render(template, {
                            "key": key,
                            "medication": row.medication.preferred_term,
                            "id": row.medication.id,
                            "infobox": row.medication.prepended_markup,
                            "include_parents": row.include_parents == 1 ? "yes" : "no",
                            "include_children": row.include_children == 1 ? "yes" : "no",
                            "include_parents_id": row.include_parents,
                            "include_children_id": row.include_children
                        });
                        $body.append(rendered);
                        return true;
                    },
                    searchOptions: {
                        searchSource: '/medicationManagement/findRefMedications',
                    },
                    enableCustomSearchEntries: false,
                });
            </script>
        </td>
    </tr>
    </tfoot>
</table>
