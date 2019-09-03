<?php

$attributes = array_map(function ($e) {
	return ['id' => $e->id, 'label' => $e->name, 'type' => 'attr'];
}, MedicationAttribute::model()->findAll(array("order" => "name")));
$options = array_map(function ($e) {
	return ['id' => $e->id, 'label' => $e->description . " - " . $e->value, 'attr_id' => $e->medication_attribute_id];
}, MedicationAttributeOption::model()->findAll(array("select" => array("id", "medication_attribute_id", "value", "description"), "order" => "value")));
?>
<script id="row_template" type="x-tmpl-mustache">
    <tr data-key="{{ key }}">
        <td>
            <input type="hidden" name="MedicationAutoRuleAttributes[{{ key }}][id]" value="-1" />
            <input class="js-attribute" type="hidden" name="MedicationAutoRuleAttributes[{{ key }}][medication_attribute_id]" value="{{attribute_id}}" />
            {{attribute_name}}
        </td>
        <td>
            <input class="js-option" type="hidden" name="MedicationAutoRuleAttributes[{{ key }}][medication_attribute_option_id]" value={{option_id}} />
            {{option_name}}
        </td>
        <td>
            <a href="javascript:void(0);" class="js-delete-attribute"><i class="oe-i trash"></i></a>
        </td>
    </tr>

</script>
<script type="text/javascript">
    $(document).on("click", ".js-delete-attribute", function () {
        $(this).closest("tr").remove();
    });
</script>
<h3>Include medications having the following attributes:</h3>
<table class="standard" id="medication_attribute_assignment_tbl">
	<thead>
	<tr>
		<th width="25%">Name</th>
		<th width="50%">Value</th>
		<th width="25%">Action</th>
	</tr>
	</thead>
	<tbody>
	<?php if (!is_null($set)): ?>
		<?php foreach ($set->medicationAutoRuleAttributes as $row_key => $assignment): ?>
			<?php
			$attr_id = $assignment->medicationAttributeOption->medication_attribute_id;
			$attr_name = $assignment->medicationAttributeOption->medicationAttribute->name;
			$option_id = $assignment->medicationAttributeOption->id;
			$option_name = $assignment->medicationAttributeOption->description . " - " . $assignment->medicationAttributeOption->value;
			?>
			<tr data-key="<?= $row_key ?>">
				<td>
                    <?= \CHtml::activeHiddenField($assignment, "[{$row_key}]id");?>
                    <?= \CHtml::activeHiddenField($assignment->medicationAttributeOption, "[{$row_key}]medication_attribute_id");?>
                    <?= \CHtml::encode($attr_name); ?>
                </td>
				<td>
                    <?= \CHtml::activeHiddenField($assignment->medicationAttributeOption, "[{$row_key}]id");?>
					<?= \CHtml::encode($option_name); ?>
				</td>
				<td>
					<a href="javascript:void(0);" class="js-delete-attribute"><i class="oe-i trash"></i></a>
				</td>
			</tr>
		<?php endforeach; ?>
	<?php endif; ?>
	</tbody>
	<tfoot class="pagination-container">
	<tr>
		<td colspan="3">
			<div class="flex-layout flex-right">
				<button class="button hint green js-add-attribute" type="button"><i class="oe-i plus pro-theme"></i></button>
			</div>
		</td>
	</tr>
	</tfoot>
</table>
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
            let $items = adderDialog.$tr.children("td:eq(1)").find("ul.add-options li");
            $items.hide();
        },
        onReturn: function (adderDialog, selectedItems) {
            if (selectedItems.length < 2) {
                let alert = new OpenEyes.UI.Dialog.Alert({
                    content: "Please select an attribute and an option"
                });

                alert.open();
                return false;
            }
            let attr = selectedItems[0];
            let opt = selectedItems[1];

            let lastkey = $("#medication_attribute_assignment_tbl > tbody > tr:last").attr("data-key");
            if (isNaN(lastkey)) {
                lastkey = 0;
            }
            let key = parseInt(lastkey) + 1;
            let template = $('#row_template').html();
            Mustache.parse(template);
            let rendered = Mustache.render(template, {
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
            let $item = $(e.target).is("span") ? $(e.target).closest("li") : $(e.target);
            let $tr = $item.closest("tr");
            if ($item.attr("data-type") === "attr") {
                let $all_options = $tr.children("td:eq(1)").find("ul.add-options li");
                let $relevant_options = $tr.children("td:eq(1)").find("ul.add-options li[data-attr_id=" + $item.attr("data-id") + "]");
                $all_options.hide();
                $relevant_options.show();
            }
        },
        enableCustomSearchEntries: true,
    });
</script>
