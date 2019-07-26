<?php
/** @var MedicationSet $medication_set */
$rowkey = 0;
$sites = array_map(function ($e) {
    return ['id' => $e->id, 'label' => $e->name];
}, Site::model()->findAll());
$subspecialties = array_map(function ($e) {
    return ['id' => $e->id, 'label' => $e->name];
}, Subspecialty::model()->findAll());

$is_manual = \Yii::app()->request->getParam('set-type', 'manual') === 'manual';
?>
<h2><?= $medication_set->isNewRecord ? 'Create' : 'Edit';?> Medication set</h2>
<div class="row divider"></div>

<form id="drugset-admin-form" action="/OphDrPrescription/admin/DrugSet/edit/<?=$medication_set->id;?>" method="post">
    <div class="row flex-layout flex-top col-gap">
        <div class="cols-6">
            <table class="large">
                <colgroup>
                    <col class="cols-3">
                    <col class="cols-6">
                    <col class="cols-1">
                </colgroup>
                <tbody>
                <?php if ($medication_set->isNewRecord) : ?>
                <tr>
                    <td colspan="3">
                        <?= CHtml::radioButtonList("set-type", \Yii::app()->request->getParam('set-type', 'manual'), [
                                'manual' => 'Manual set',
                                'automatic' => 'Automatic set',
                        ], [
                                'template' => "{beginLabel}{input}{labelTitle}{endLabel}",
                                'separator' => '',
                                'container' => '',
                                'labelOptions' => ['class' => 'inline highlight']
                        ]);?>
                    </td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td>
                        <span class="js-manual" style="<?=($is_manual ? 'display:block' : 'display:none');?>">Name</span>
                        <span class="js-auto" style="<?=(!$is_manual ? 'display:block' : 'display:none');?>">Search Auto Set</span>
                    </td>
                    <td>
                        <?= \CHtml::activeHiddenField($medication_set, 'id');?>
                        <?php
                            if ($medication_set->isNewRecord) {
                                $this->widget('application.modules.OphDrPrescription.modules.OphDrPrescriptionAdmin.widgets.AutoSetSearchAutocomplete', [
                                        'set' => $medication_set,
                                        'style' => ($is_manual ? 'display:none' : 'display:block')
                                ]);
                            }

                            echo \CHtml::activeTextField($medication_set, 'name', [
                                    'class' => 'cols-full js-manual',
                                    'style' => ($is_manual ? 'display:block' : 'display:none'),
                                    'placeholder' => 'Name of the set'
                            ]);
                        ?>
                    </td>
                    <td>
                        <div class="js-spinner-as-icon" style="display:none"><i class="spinner as-icon"></i></div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="cols-12">
            <h3>Usage Rules</h3>
            <table class="standard" id="rule_tbl">
                <thead>
                <tr>
                    <th>Site</th>
                    <th>Subspecialty</th>
                    <th>Usage Code</th>
                    <th width="5%">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($medication_set->medicationSetRules as $k => $rule): ?>
                    <tr data-key="<?= $rowkey++ ?>">
                        <td>
                            <?= \CHtml::activeHiddenField($rule, "[{$k}]id"); ?>
                            <?= \CHtml::activeHiddenField($rule, "[{$k}]site_id"); ?>
                            <?= ($rule->site_id ? CHtml::encode($rule->site->name) : "") ?>
                        </td>
                        <td>
                            <?= \CHtml::activeHiddenField($rule, "[{$k}]subspecialty_id"); ?>
                            <?= ($rule->subspecialty_id ? CHtml::encode($rule->subspecialty->name) : "") ?>
                        </td>
                        <td>
                            <?= CHtml::activeDropDownList($rule, "[{$k}]usage_code_id", CHtml::listData(MedicationUsageCode::model()->findAll(), 'id', 'name')); ?>
                        </td>
                        <td>
                            <a href="javascript:void(0);" class="js-delete-rule"><i class="oe-i trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot class="pagination-container">
                <tr>
                    <td colspan="4">
                        <div class="flex-layout flex-right">
                            <button class="button hint green js-add-set" type="button"><i
                                        class="oe-i plus pro-theme"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>

<?=\CHtml::submitButton(
    'Save',
    [
        'class' => 'button large green hint',
        'name' => 'save',
        'id' => 'et_save'
    ]
); ?>
 <?=\CHtml::submitButton(
    'Cancel',
    [
        'class' => 'button large red hint',
        'data-uri' => '/OphDrPrescription/admin/DrugSet/index',
        'name' => 'cancel',
        'id' => 'et_cancel'
    ]
); ?>
    <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken?>" />
    <input type="hidden" class="js-search-data js-update-row-data" data-name="set_id" value="<?=$medication_set->id;?>" />

    <?php if (!$medication_set->isNewRecord && !$medication_set->automatic) :?>
        <div class="row divider"></div>
        <?php $this->renderPartial('/DrugSet/_meds_in_set', ['medication_set' => $medication_set, 'medication_data_provider' => $medication_data_provider]); ?>
    <?php endif; ?>

</form>

<script type="x-tmpl-mustache" id="rule_row_template" style="display:none">
<tr data-key="{{key}}">
    <td>
        <input type="hidden" name="MedicationSetRule[{{key}}][id]" />
        <input type="hidden" name="MedicationSetRule[{{key}}][site_id]" value="{{site.id}}" />
        {{site.label}}
    </td>
    <td>
        <input type="hidden" name="MedicationSetRule[{{key}}][subspecialty_id]" value="{{subspecialty.id}}" />
        {{subspecialty.label}}
    </td>
    <td>
        <?= CHtml::dropDownList('MedicationSetRule[{{key}}][usage_code_id]', null, CHtml::listData(MedicationUsageCode::model()->findAll(), 'id', 'name')); ?>
    </td>
    <td>
        <a href="javascript:void(0);" class="js-delete-rule"><i class="oe-i trash"></i></a>
    </td>
</tr>
</script>
<script>
    var drugSetController = new OpenEyes.OphDrPrescriptionAdmin.DrugSetController({
        tableSelector: '#meds-list',
        searchUrl: '/OphDrPrescription/admin/DrugSet/searchmedication',
        templateSelector: '#medication_template'
    });

    var tableInlineEditController = new OpenEyes.TableInlineEdit({
        tableSelector: '#meds-list',
        templateSelector: '#medication_template',
        onAjaxError: function() {
            drugSetController.refreshResult();
        },
        onAjaxComplete: function() {
            //drugSetController.refreshResult();
        }
    });

    $(function () {
        $(document).on("click", ".js-delete-rule", function (e) {
            $(e.target).closest("tr").remove();
        });
    });

    new OpenEyes.UI.AdderDialog({
        openButton: $('.js-add-set'),
        itemSets: [
            new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode($sites) ?>, {
                'id': 'site',
                'multiSelect': false,
                header: "Site"
            }),
            new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode($subspecialties) ?>, {
                'id': 'subspecialty',
                'multiSelect': false,
                header: "Subspecialty"
            }),
        ],
        onReturn: function (adderDialog, selectedItems) {

            var selObj = {};

            $.each(selectedItems, function (i, e) {
                selObj[e.itemSet.options.id] = {
                    id: e.id,
                    label: e.label
                };
            });

            let lastkey = $("#rule_tbl > tbody > tr:last").attr("data-key");
            if (isNaN(lastkey)) {
                lastkey = 0;
            }
            let key = parseInt(lastkey) + 1;
            let template = $('#rule_row_template').html();
            Mustache.parse(template);

            selObj.key = key;

            var rendered = Mustache.render(template, selObj);
            $("#rule_tbl > tbody").append(rendered);
            return true;
        },
        enableCustomSearchEntries: true,
    });

    $('input[name="set-type"]').on('input', function() {
        const $id_input = $('#MedicationSet_id');
        $('.js-auto, .js-manual').toggle();
        let value = $(this).val();
        if (value === 'manual') {
            $id_input.val('');
            $('#MedicationSet_name, #MedicationSet_auto_name').val('');
        }
    });

</script>