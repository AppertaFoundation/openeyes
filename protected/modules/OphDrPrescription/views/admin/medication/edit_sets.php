<?php
    /** @var Medication $medication */

    $rowkey = 0;
    $sets = MedicationSet::model()->findAllByAttributes(['hidden' => 0, 'deleted_date' => null]);
    $forms = MedicationForm::model()->findAllByAttributes(['deleted_date' => null]);
    $routes = MedicationRoute::model()->findAllByAttributes(['deleted_date' => null]);
    $freqs = MedicationFrequency::model()->findAllByAttributes(['deleted_date' => null]);
    $durations = MedicationDuration::model()->findAllByAttributes(['deleted_date' => null]);

    $medicationSetItems = $medication->medicationSetItems;

?>
<script id="set_row_template" type="x-tmpl-mustache">
    <tr id="{{ key }}">
        <td>
        <input type="hidden" name="Medication[medicationSetItems][id][]" value="-1" />
            <?php echo CHtml::dropDownList('Medication[medicationSetItems][medication_set_id][]', null, CHtml::listData($sets, 'id', 'name'), array('empty' => '-- None --', "class" => "js-attribute", "id" => 'Medication_attribute_id{{ key }}')); ?>
        </td>
       <td>
            <?php echo CHtml::textField('Medication[medicationSetItems][default_dose][]', null); ?>
        </td>
        <td>
            <?php echo CHtml::textField('Medication[medicationSetItems][default_dose_unit_term][]', null); ?>
        </td>
        <td>
            <?php echo CHtml::dropDownList('Medication[medicationSetItems][default_form_id][]', null, CHtml::listData($forms, 'id', 'term'), array('empty' => '-- None --', 'class' => 'cols-full')); ?>
        </td>
        <td>
            <?php echo CHtml::dropDownList('Medication[medicationSetItems][default_route_id][]', null, CHtml::listData($routes, 'id', 'term'), array('empty' => '-- None --', 'class' => 'cols-full')); ?>
        </td>
        <td>
            <?php echo CHtml::dropDownList('Medication[medicationSetItems][default_duration_id][]', null, CHtml::listData($durations, 'id', 'name'), array('empty' => '-- None --', 'class' => 'cols-full')); ?>
        </td>
        <td>
            <?php echo CHtml::dropDownList('Medication[medicationSetItems][default_duration_id][]', null, CHtml::listData($durations, 'id', 'name'), array('empty' => '-- None --', 'class' => 'cols-full')); ?>
        </td>
        <td>
            <a href="javascript:void(0);" class="js-delete-set"><i class="oe-i trash"></i></a>
        </td>
    </tr>
</script>
<script type="text/javascript">
    $(function(){
        $(document).on("click", ".js-add-set", function (e) {
            var lastkey = $("#medication_set_assignment_tbl tbody tr:last").attr("data-key");
            if(isNaN(lastkey)) {
                lastkey = 0;
            }
            var key = parseInt(lastkey) + 1;
            var template = $('#set_row_template').html();
            Mustache.parse(template);
            var rendered = Mustache.render(template, {"key": key});
            $("#medication_set_assignment_tbl tbody").append(rendered);
        });

        $(document).on("click", ".js-delete-set", function (e) {
            $(e.target).closest("tr").remove();
        });
    });
</script>
<h3>Medication is member of the following sets</h3>
<table class="standard" id="medication_set_assignment_tbl">
    <thead>
        <tr>
            <th width="17%">Name</th>
            <th width="13%">Default dose</th>
            <th width="13%">Default dose unit</th>
            <th width="13%">Default form</th>
            <th width="13%">Default route</th>
            <th width="13%">Default freq</th>
            <th width="13%">Default duration</th>
            <th width="5%">Action</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($medicationSetItems as $assignment): ?>
		<?php
            $set_id = $assignment->medication_set_id;
            $id = is_null($assignment->id) ? -1 : $assignment->id;
		    $rowkey++
        ?>
        <tr data-key="<?=$rowkey?>">
            <td>
                <input type="hidden" name="Medication[medicationSetItems][id][]" value="<?=$id?>" />
                <?php echo CHtml::dropDownList('Medication[medicationSetItems][medication_set_id][]', $assignment->medication_set_id, CHtml::listData($sets, 'id', 'name'), array('empty' => '-- None --', "id" => 'Medication_set_id'.$rowkey)); ?>
            </td>
            <td>
				<?php echo CHtml::textField('Medication[medicationSetItems][default_dose][]', $assignment->default_dose); ?>
            </td>
            <td>
				<?php echo CHtml::textField('Medication[medicationSetItems][default_dose_unit_term][]', $assignment->default_dose_unit_term); ?>
            </td>
            <td>
                <?php echo CHtml::dropDownList('Medication[medicationSetItems][default_form_id][]', $assignment->default_form_id, CHtml::listData($forms, 'id', 'term'), array('empty' => '-- None --', 'class' => 'cols-full')); ?>
            </td>
            <td>
				<?php echo CHtml::dropDownList('Medication[medicationSetItems][default_route_id][]', $assignment->default_route_id, CHtml::listData($routes, 'id', 'term'), array('empty' => '-- None --', 'class' => 'cols-full')); ?>
            </td>
            <td>
				<?php echo CHtml::dropDownList('Medication[medicationSetItems][default_frequency_id][]', $assignment->default_frequency_id, CHtml::listData($freqs, 'id', 'term'), array('empty' => '-- None --', 'class' => 'cols-full')); ?>
            </td>
            <td>
				<?php echo CHtml::dropDownList('Medication[medicationSetItems][default_duration_id][]', $assignment->default_duration_id, CHtml::listData($durations, 'id', 'name'), array('empty' => '-- None --', 'class' => 'cols-full')); ?>
            </td>
            <td>
                <a href="javascript:void(0);" class="js-delete-attribute"><i class="oe-i trash"></i></a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot class="pagination-container">
        <tr>
            <td colspan="8">
                <div class="flex-layout flex-right">
                    <button class="button hint green js-add-set" type="button"><i class="oe-i plus pro-theme"></i></button>
                </div>
            </td>
        </tr>
    </tfoot>
</table>