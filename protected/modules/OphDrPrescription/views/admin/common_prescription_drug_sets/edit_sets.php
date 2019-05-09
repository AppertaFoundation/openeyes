<?php
/** @var Medication $medication */

$rowkey = 0;
$taperrowkey = 0;
$sets = array_map(function ($e) {
    return ['id' => $e->id, 'label' => $e->name];
}, MedicationSet::model()->findAllByAttributes(['hidden' => 0, 'deleted_date' => null]));
$units = [];
if ($unit_attr = MedicationAttribute::model()->find("name='UNIT_OF_MEASURE'")) {
    $units = array_map(function ($e) {
        return ['id' => $e->id, 'label' => $e->description];
    }, $unit_attr->medicationAttributeOptions);
}
$forms = array_map(function ($e) {
    return ['id' => $e->id, 'label' => $e->term];
}, MedicationForm::model()->findAllByAttributes(['deleted_date' => null]));
$routes = array_map(function ($e) {
    return ['id' => $e->id, 'label' => $e->term];
}, MedicationRoute::model()->findAllByAttributes(['deleted_date' => null]));
$freqs = array_map(function ($e) {
    return ['id' => $e->id, 'label' => $e->term];
}, MedicationFrequency::model()->findAllByAttributes(['deleted_date' => null]));
$durations = array_map(function ($e) {
    return ['id' => $e->id, 'label' => $e->name];
}, MedicationDuration::model()->findAllByAttributes(['deleted_date' => null]));

$medicationSetItems = [];
if (!empty($id)) {
    $medicationSet = MedicationSet::model()->findByAttributes(['id' => $id]);
    $medicationSetItems = $medicationSet->medicationSetItems;
}

?>
<script id="set_row_template" type="x-tmpl-mustache">
    <tr class="medication_row" data-key="{{ key }}" id="medication_{{ key }}">
        <td>
        <input type="hidden" name="MedicationSet[medicationSetItems][id][{{ key }}]" value="-1" />
        <input type="hidden" name="MedicationSet[medicationSetItems][medication_id][{{ key }}]" value="{{medication.id}}" />
        {{medication.label}}
        </td>
       <td>
            <?php echo CHtml::textField('MedicationSet[medicationSetItems][default_dose][{{ key }}]', "1"); ?>
        </td>
        <td>
            <?php echo CHtml::textField('MedicationSet[medicationSetItems][default_dose_unit_term][{{ key }}]', '{{unit.label}}'); ?>
        </td>
        <td>
            <input type="hidden" name="MedicationSet[medicationSetItems][default_form_id][{{ key }}]" value="{{form.id}}" />
            {{form.label}}
        </td>
        <td>
            <input type="hidden" name="MedicationSet[medicationSetItems][default_route_id][{{ key }}]" value="{{route.id}}" />
            {{route.label}}
        </td>
        <td>
            <input type="hidden" name="MedicationSet[medicationSetItems][default_frequency_id][{{ key }}]" value="{{frequency.id}}" />
            {{frequency.label}}
        </td>
        <td>
            <input type="hidden" name="MedicationSet[medicationSetItems][default_duration_id][{{ key }}]" value="{{duration.id}}" />
            {{duration.label}}
        </td>
        <td>
                <button class="button hint green js-add-taper" data-row-key="{{ key }}"
                        data-medication-id="{{medication.id}}" type="button">add taper
                </button>
        </td>
        <td>
            <a href="javascript:void(0);" class="js-delete-set"><i class="oe-i trash"></i></a>
        </td>
    </tr>
    <input type="hidden" class="row_number_{{ key }}" value="1">


</script>

<script type="text/javascript">
    $(function () {
        $(document).on("click", ".js-delete-set", function (e) {
            $(e.target).closest("tr").remove();
        });
    });
</script>

<script id="set_row_taper_template" type="x-tmpl-mustache">

    <tr class="taper_row medication_{{ key }}" data-key="{{ taperrowkey }}">
        <td>
        <input type="hidden" name="MedicationSet[medicationSetItems][medicationSetItemTapers][{{ key }}][{{ taperrowkey }}][id]" value="-1" />
        <input type="hidden" name="MedicationSet[medicationSetItems][medicationSetItemTapers][{{ key }}][{{ taperrowkey }}][medication_set_item_id]" value="{{medication_id}}" />
        <em class="fade">-></em>
        </td>
       <td>
        </td>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
        <td>
            <select name="MedicationSet[medicationSetItems][medicationSetItemTapers][{{ key }}][{{ taperrowkey }}][default_frequency_id]">
            <?php foreach ($freqs as $freq) : ?>
            <option value="<?=$freq['id']?>"><?=$freq['label']?></option>
            <?php endforeach; ?>
            </select>
        </td>
        <td>
            <select name="MedicationSet[medicationSetItems][medicationSetItemTapers][{{ key }}][{{ taperrowkey }}][default_duration_id]">
            <?php foreach ($durations as $duration) : ?>
            <option value="<?=$duration['id']?>"><?=$duration['label']?></option>
            <?php endforeach; ?>
            </select>
        </td>
         <td>
        </td>
        <td>
            <a href="javascript:void(0);" class="js-delete-taper"><i class="oe-i trash"></i></a>
        </td>
    </tr>

</script>

<script type="text/javascript">
    $(function () {
        $(document).on("click", ".js-delete-taper", function (e) {
            $(e.target).closest("tr").remove();
        });
    });
</script>

<h3>This set contains the following medications</h3>
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
    <?php
    foreach ($medicationSetItems as $assignment):
        ?>
        <?php
        //$set_id = $assignment->medication_set_id;
        $id = is_null($assignment->id) ? -1 : $assignment->id;
        $rowkey++
        ?>
        <tr class="medication_row" data-key="<?= $rowkey ?>" id="medication_<?= $rowkey ?>">
            <td>
                <input type="hidden" name="MedicationSet[medicationSetItems][id][<?= $rowkey ?>]" value="<?= $id ?>"/>
                <input type="hidden" name="MedicationSet[medicationSetItems][medication_id][<?= $rowkey ?>]"
                       value="<?= $assignment->medication_id ?>"/>
                <?= CHtml::encode($assignment->medication->preferred_term) ?>
            </td>
            <td>
                <?php echo CHtml::textField('MedicationSet[medicationSetItems][default_dose]['.$rowkey.']', $assignment->default_dose); ?>
            </td>
            <td>
                <?php echo CHtml::textField('MedicationSet[medicationSetItems][default_dose_unit_term]['.$rowkey.']', $assignment->default_dose_unit_term); ?>
            </td>
            <td>
                <input type="hidden" name="MedicationSet[medicationSetItems][default_form_id][<?= $rowkey ?>]"
                       value="<?= $assignment->default_form_id ?>"/>
                <?= $assignment->default_form_id ? CHtml::encode($assignment->defaultForm->term) : "" ?>
            </td>
            <td>
                <input type="hidden" name="MedicationSet[medicationSetItems][default_route_id][<?= $rowkey ?>]"
                       value="<?= $assignment->default_route_id ?>"/>
                <?= $assignment->default_route_id ? CHtml::encode($assignment->defaultRoute->term) : "" ?>
            </td>
            <td>
                <input type="hidden" name="MedicationSet[medicationSetItems][default_frequency_id][<?= $rowkey ?>]"
                       value="<?= $assignment->default_frequency_id ?>"/>
                <?= $assignment->default_frequency_id ? CHtml::encode($assignment->defaultFrequency->term) : "" ?>
            </td>
            <td>
                <input type="hidden" name="MedicationSet[medicationSetItems][default_duration_id][<?= $rowkey ?>]"
                       value="<?= $assignment->default_duration_id ?>"/>
                <?= $assignment->default_duration_id ? CHtml::encode($assignment->defaultDuration->name) : "" ?>
            </td>
            <td>
                <button class="button hint green js-add-taper" data-row-key="<?= $rowkey ?>"
                        data-medication-id="<?= $assignment->id ?>" type="button">add taper
                </button>
            </td>
            <td>
                <a href="javascript:void(0);" class="js-delete-attribute" data-key="<?= $rowkey; ?>"><i class="oe-i trash"></i></a>
            </td>
        </tr>

        <?php
        if (!empty($assignment->tapers)) : ?>

            <?php foreach ($assignment->tapers as $taper): ?>
                <?php
                //$set_id = $assignment->medication_set_id;
                $taper_id = is_null($taper->id) ? -1 : $taper->id;
                $taperrowkey++
                ?>
                <tr class="taper_row medication_<?= $rowkey; ?>" data-key="<?= $taperrowkey ?>">
                    <td>
                        <input type="hidden" name="MedicationSet[medicationSetItems][medicationSetItemTapers][<?= $rowkey ?>][<?=$taperrowkey?>][id]"
                               value="<?= $taper_id ?>"/>
                        <input type="hidden" name="MedicationSet[medicationSetItems][medicationSetItemTapers][<?= $rowkey ?>][<?=$taperrowkey?>][medication_set_item_id]"
                               value="<?= $taper->medication_set_item_id ?>"/>
                        <em class="fade">-></em>
                    </td>
                    <td>
                    </td>
                    <td>
                    </td>
                    <td>
                    </td>
                    <td>
                    </td>
                    <td>
                        <select name="MedicationSet[medicationSetItems][medicationSetItemTapers][<?= $rowkey ?>][<?=$taperrowkey?>][default_frequency_id]">
                            <option value="<?=$taper->frequency_id?>"><?=$taper->frequency->term?></option>
                            <?php foreach ($freqs as $freq) : ?>
                            <?php if($freq['id'] != $taper->frequency_id)?>
                                <option value="<?=$freq['id']?>"><?=$freq['label']?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <select name="MedicationSet[medicationSetItems][medicationSetItemTapers][<?= $rowkey ?>][<?=$taperrowkey?>][default_duration_id]">
                            <option value="<?=$taper->duration_id?>"><?=$taper->duration->name?></option>
                            <?php foreach ($durations as $duration) : ?>
                                <?php if($duration['id'] != $taper->duration_id) : ?>
                                    <option value="<?=$duration['id']?>"><?=$duration['label']?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                    </td>
                    <td>
                        <a href="javascript:void(0);" class="js-delete-taper"><i class="oe-i trash"></i></a>
                    </td>
                </tr>

            <?php endforeach; ?>
            <input type="hidden" class="row_number_<?= $rowkey; ?>" value="<?= count($assignment->tapers); ?>">
        <?php endif; ?>

    <?php endforeach; ?>
    <script type="text/javascript">
        $(function () {
            $(document).on("click", ".js-delete-attribute", function (e) {
                var rowKey = $(this).attr('data-key');
                $('.medication_'+rowKey).remove();
                $(e.target).closest("tr").remove();
            });
        });
    </script>
    </tbody>
    <tfoot class="pagination-container">
    <tr>
        <td colspan="8">
            <div class="flex-layout flex-right">
                <button class="button hint green js-add-medication" type="button"><i class="oe-i plus pro-theme"></i>
                </button>
                <script type="text/javascript">
                    new OpenEyes.UI.AdderDialog({
                        openButton: $('.js-add-medication'),
                        itemSets: [
                            new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode($units) ?>, {
                                'id': 'unit',
                                'multiSelect': false,
                                header: "Default unit"
                            }),
                            new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode($forms) ?>, {
                                'id': 'form',
                                'multiSelect': false,
                                header: "Default form"
                            }),
                            new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode($routes) ?>, {
                                'id': 'route',
                                'multiSelect': false,
                                header: "Default route"
                            }),
                            new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode($freqs) ?>, {
                                'id': 'frequency',
                                'multiSelect': false,
                                header: "Default frequency"
                            }),
                            new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode($durations) ?>, {
                                'id': 'duration',
                                'multiSelect': false,
                                header: "Default duration"
                            })
                        ],
                        onReturn: function (adderDialog, selectedItems) {

                            var row = {};
                            $.each(selectedItems, function (i, e) {
                                if (typeof e.itemSet === "undefined") {
                                    row.medication = Object.assign({}, e);
                                    return;
                                }
                                if (e.itemSet.options.id == "unit") {
                                    row.unit = Object.assign({}, e);
                                }
                                else if (e.itemSet.options.id == "form") {
                                    row.form = Object.assign({}, e);
                                }
                                else if (e.itemSet.options.id == "route") {
                                    row.route = Object.assign({}, e);
                                }
                                else if (e.itemSet.options.id == "frequency") {
                                    row.frequency = Object.assign({}, e);
                                }
                                else if (e.itemSet.options.id == "duration") {
                                    row.duration = Object.assign({}, e);
                                }
                            });

                            if (typeof row.medication === "undefined") {
                                return false;
                            }

                            var $body = $("#medication_set_assignment_tbl > tbody");
                            var lastkey = $body.find(".medication_row:last").attr("data-key");

                            if (isNaN(lastkey)) {
                                lastkey = 0;
                            }
                            var key = parseInt(lastkey) + 1;
                            var template = $('#set_row_template').html();
                            Mustache.parse(template);
                            var rendered = Mustache.render(template, {
                                "key": key,
                                "medication": row.medication,
                                "id": row.medication.id,
                                "unit": row.unit,
                                "form": row.form,
                                "route": row.route,
                                "frequency": row.frequency,
                                "duration": row.duration
                            });
                            $body.append(rendered);
                            return true;
                        },
                        searchOptions: {
                            searchSource: '/medicationManagement/findRefMedications',
                        },
                        enableCustomSearchEntries: true,
                    });

                </script>


            </div>
        </td>
    </tr>
    </tfoot>
</table>

<script type="text/javascript">
    $(document).on("click", ".js-add-taper", function (e) {
        var medicationId = $(e.target).attr("data-medication-id");
        var rowKey = $(e.target).attr("data-row-key");

        var $body = $("#medication_" + rowKey);

        var row_number = $(".row_number_" + rowKey).val();
        $(".row_number_" + rowKey).val(parseInt(row_number)+1);
        var row_number = $(".row_number_" + rowKey).val();

        var lastkey = $("#medication_set_assignment_tbl > tbody").find(".taper_row:last").attr("data-key");
        if (isNaN(lastkey)) {
            lastkey = 0;
        }
        var taperrowkey = parseInt(lastkey) + 1;

        var template = $('#set_row_taper_template').html();
        Mustache.parse(template);

        var rendered = Mustache.render(template, {
            "key": rowKey,
            "medication_id": medicationId,
            "taperrowkey": row_number,
        });
        $body.after(rendered);

    });

</script>