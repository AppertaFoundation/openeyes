<?php
    /** @var Medication $medication */

    $rowkey = 0;
    $sets = array_map(function ($e) {
        return ['id' => $e->id, 'label' => $e->name];

    }, MedicationSet::model()->findAllByAttributes(['hidden' => 0, 'deleted_date' => null]));
    $units = [];
    if ($unit_attr = MedicationAttribute::model()->find("name='UNIT_OF_MEASURE'")) {
        $units = array_map(function ($e) {
            return ['id' => $e->id, 'label' => $e->description];
        }, $unit_attr->medicationAttributeOptions);
    } else {
        $units = array();
    }
    $routes = array_map(function ($e) {
        return ['id' => $e->id, 'label' => $e->term];

    }, MedicationRoute::model()->findAllByAttributes(['deleted_date' => null]));
    $frequencies = array_map(function ($e) {
        return ['id' => $e->id, 'label' => $e->term];

    }, MedicationFrequency::model()->findAllByAttributes(['deleted_date' => null]));
    $durations = array_map(function ($e) {
        return ['id' => $e->id, 'label' => $e->name];

    }, MedicationDuration::model()->findAllByAttributes(['deleted_date' => null]));

    $medicationSetItems = $medication->medicationSetItems;

    ?>
<script id="set_row_template" type="x-tmpl-mustache">
    <tr data-key="{{ key }}">
        <td>
        <input type="hidden" name="Medication[medicationSetItems][{{key}}][medication_set_id]" value="{{set.id}}" />
        {{set.label}}
        </td>
       <td>
            <?php echo CHtml::textField('Medication[medicationSetItems][{{key}}][default_dose]', "1"); ?>
        </td>
        <td>
            <?php echo CHtml::textField('Medication[medicationSetItems][{{key}}][default_dose_unit_term]', '{{unit.label}}'); ?>
        </td>
        <td>
            <input type="hidden" name="Medication[medicationSetItems][{{key}}][default_route_id]" value="{{route.id}}" />
            {{route.label}}
        </td>
        <td>
            <input type="hidden" name="Medication[medicationSetItems][{{key}}][default_frequency_id]" value="{{frequency.id}}" />
            {{frequency.label}}
        </td>
        <td>
            <input type="hidden" name="Medication[medicationSetItems][{{key}}][default_duration_id]" value="{{duration.id}}" />
            {{duration.label}}
        </td>
        <td>
            <a href="javascript:void(0);" class="js-delete-set"><i class="oe-i trash"></i></a>
        </td>
    </tr>
</script>
<script type="text/javascript">
    $(function(){
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
            <th width="13%">Default route</th>
            <th width="13%">Default freq</th>
            <th width="13%">Default duration</th>
            <th width="5%">Action</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($medicationSetItems as $assignment) : ?>
        <?php
            $set_id = $assignment->medication_set_id;
            $id = is_null($assignment->id) ? -1 : $assignment->id;
            $rowkey++
        ?>
        <tr data-key="<?=$rowkey?>" <?php if ($assignment->medicationSet->hidden) :
            ?>style="display:none;" <?php
                      endif; ?>>
            <td>
                <input type="hidden" name="Medication[medicationSetItems][<?=$rowkey?>][id]" value="<?=$id?>" />
                <input type="hidden" name="Medication[medicationSetItems][<?=$rowkey?>][medication_set_id]" value="<?=$assignment->medication_set_id?>" />
                <?=CHtml::encode($assignment->medicationSet->name)?>
            </td>
            <td>
                <?php echo CHtml::textField("Medication[medicationSetItems][$rowkey][default_dose]", $assignment->default_dose); ?>
            </td>
            <td>
                <?php echo CHtml::textField("Medication[medicationSetItems][$rowkey][default_dose_unit_term]", $assignment->default_dose_unit_term); ?>
            </td>
            <td>
                <input type="hidden" name="Medication[medicationSetItems][<?=$rowkey?>][default_route_id]" value="<?=$assignment->default_route_id?>" />
                <?=$assignment->default_route_id ? CHtml::encode($assignment->defaultRoute->term) : ""?>
            </td>
            <td>
                <input type="hidden" name="Medication[medicationSetItems][<?=$rowkey?>][default_frequency_id]" value="<?=$assignment->default_frequency_id?>" />
                <?=$assignment->default_frequency_id ? CHtml::encode($assignment->defaultFrequency->term) : ""?>
            </td>
            <td>
                <input type="hidden" name="Medication[medicationSetItems][<?=$rowkey?>][default_duration_id]" value="<?=$assignment->default_duration_id?>" />
                <?=$assignment->default_duration_id ? CHtml::encode($assignment->defaultDuration->name) : ""?>
            </td>
            <td>
                <a href="javascript:void(0);" class="js-delete-attribute"><i class="oe-i trash"></i></a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot class="pagination-container">
        <tr>
            <td colspan="7">
                <div class="flex-layout flex-right">
                    <button class="button hint green js-add-set" type="button"><i class="oe-i plus pro-theme"></i></button>
                    <script type="text/javascript">
                        new OpenEyes.UI.AdderDialog({
                            openButton: $('.js-add-set'),
                            itemSets: [
                                new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode($sets) ?>, {'id': 'set', 'multiSelect': false, header: "Set"}),
                                new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode($units) ?>, {'id': 'unit','multiSelect': false, header: "Default unit"}),
                                new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode($routes) ?>, {'id': 'route', 'multiSelect': false, header: "Default route"}),
                                new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode($frequencies) ?>, {'id': 'frequency', 'multiSelect': false, header: "Default frequency"}),
                                new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode($durations) ?>, {'id': 'duration', 'multiSelect': false, header: "Default duration"})
                            ],
                            onReturn: function (adderDialog, selectedItems) {

                                var selObj = {};

                                $.each(selectedItems, function(i,e){
                                    selObj[e.itemSet.options.id] = {
                                        id: e.id,
                                        label: e.label
                                    };
                                });

                                var lastkey = $("#medication_set_assignment_tbl > tbody > tr:last").attr("data-key");
                                if(isNaN(lastkey)) {
                                    lastkey = 0;
                                }
                                var key = parseInt(lastkey) + 1;
                                var template = $('#set_row_template').html();
                                Mustache.parse(template);

                                selObj.key = key;

                                var rendered = Mustache.render(template, selObj);
                                $("#medication_set_assignment_tbl > tbody").append(rendered);
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
