<?php
/** @var Medication $medication */

$attributes = array_map(function ($e) {
    return ['id' => $e->id, 'label' => $e->name, 'type' => 'attr'];
}, MedicationAttribute::model()->findAll(array("order" => "name")));
$options = array_map(function ($e) {
    return ['id' => $e->id, 'label' => $e->description . " - " . $e->value, 'attr_id' => $e->medication_attribute_id];
}, MedicationAttributeOption::model()->findAll(array("select" => array("id", "medication_attribute_id", "value", "description"), "order" => "value")));

$rowkey = 0;
?>
<script id="row_template" type="x-tmpl-mustache">
    <tr data-key="{{ key }}">
        <td>
            <input type="hidden" name="Medication[medicationAttributeAssignment][id][]" value="-1" />
            <input class="js-attribute" type="hidden" name="Medication[medicationAttributeAssignment][medication_attribute_id][]" value="{{attribute_id}}" />
            {{attribute_name}}
        </td>
        <td>
            <input class="js-option" type="hidden" name="Medication[medicationAttributeAssignment][medication_attribute_option_id][]" value={{option_id}} />
            {{option_name}}
        </td>
        <td>
            <a href="javascript:void(0);" class="js-delete-attribute"><i class="oe-i trash"></i></a>
        </td>
    </tr>

</script>
<script type="text/javascript">
    $(function () {
        $(document).on("click", ".js-delete-attribute", function (e) {
            $(e.target).closest("tr").remove();
        });
    });
</script>
<h3>Attributes</h3>
<table class="standard" id="medication_attribute_assignment_tbl">
    <thead>
    <tr>
        <th width="25%">Name</th>
        <th width="50%">Value</th>
        <th width="25%">Action</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($medication->medicationAttributeAssignments as $assignment): ?>
        <?php
        $attr_id = isset($assignment->medicationAttributeOption) ? $assignment->medicationAttributeOption->medication_attribute_id : null;
        $attr_name = isset($assignment->medicationAttributeOption) ? $assignment->medicationAttributeOption->medicationAttribute->name : "";
        $option_id = isset($assignment->medicationAttributeOption) ? $assignment->medicationAttributeOption->id : null;
        $option_name = isset($assignment->medicationAttributeOption) ? $assignment->medicationAttributeOption->description . " - " . $assignment->medicationAttributeOption->value : null;
        $id = is_null($assignment->id) ? -1 : $assignment->id;
        $rowkey++;
        ?>
        <tr data-key="<?= $rowkey ?>">
            <td>
                <input type="hidden" name="Medication[medicationAttributeAssignment][id][]" value="<?= $id ?>"/>
                <input type="hidden" name="Medication[medicationAttributeAssignment][medication_attribute_id][]"
                       value="<?= $attr_id ?>"/>
                <?php echo CHtml::encode($attr_name); ?>
            </td>
            <td>
                <input type="hidden" name="Medication[medicationAttributeAssignment][medication_attribute_option_id][]"
                       value="<?= $option_id ?>"/>
                <?php echo CHtml::encode($option_name); ?>
            </td>
            <td>
                <a href="javascript:void(0);" class="js-delete-attribute"><i class="oe-i trash"></i></a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot class="pagination-container">
    <tr>
        <td colspan="3">
            <div class="flex-layout flex-right">
                <button class="button hint green js-add-attribute" type="button"><i class="oe-i plus pro-theme"></i>
                </button>
                <script type="text/javascript">
                    new OpenEyes.UI.AdderDialog({
                        openButton: $('.js-add-attribute'),
                        itemSets: [
                            new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode($attributes) ?>, {
                                'multiSelect': false,
                                header: "Attributes"
                            }),
                            new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode($options) ?>, {
                                'multiSelect': false,
                                header: "Options"
                            })
                        ],
                        onOpen: function (adderDialog) {
                            var $items = adderDialog.$tr.children("td:eq(1)").find("ul.add-options li");
                            $items.hide();
                        },
                        onReturn: function (adderDialog, selectedItems) {
                            if (selectedItems.length < 2) {
                                var alert = new OpenEyes.UI.Dialog.Alert({
                                    content: "Please select an attribute and an option"
                                });

                                alert.open();
                                return false;
                            }
                            var attr = selectedItems[0];
                            var opt = selectedItems[1];

                            var lastkey = $("#medication_attribute_assignment_tbl > tbody > tr:last").attr("data-key");
                            if (isNaN(lastkey)) {
                                lastkey = 0;
                            }
                            var key = parseInt(lastkey) + 1;
                            var template = $('#row_template').html();
                            Mustache.parse(template);
                            var rendered = Mustache.render(template, {
                                "key": key,
                                "attribute_id": attr.id,
                                "attribute_name": attr.label,
                                "option_id": opt.id,
                                "option_name": opt.label
                            });
                            $("#medication_attribute_assignment_tbl > tbody").append(rendered);
                            return true;
                        },
                        onSelect: function (e) {
                            var $item = $(e.target).is("span") ? $(e.target).closest("li") : $(e.target);
                            var $tr = $item.closest("tr");
                            if ($item.attr("data-type") === "attr") {
                                var $all_options = $tr.children("td:eq(1)").find("ul.add-options li");
                                var $relevant_options = $tr.children("td:eq(1)").find("ul.add-options li[data-attr_id=" + $item.attr("data-id") + "]");
                                $all_options.hide();
                                $relevant_options.show();
                            }
                        },
                        enableCustomSearchEntries: true,
                    });
                </script>
            </div>
        </td>
    </tr>
    </tfoot>
</table>