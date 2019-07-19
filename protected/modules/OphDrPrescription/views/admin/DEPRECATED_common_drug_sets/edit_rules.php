<?php
	/** @var MedicationSet $medication_set */
	$rowkey = 0;
	$sites = array_map(function($e){ return ['id' => $e->id, 'label' => $e->name]; }, Site::model()->findAll());
	$subspecialties = array_map(function($e){ return ['id' => $e->id, 'label' => $e->name]; }, Subspecialty::model()->findAll());


?>
<h3>Usage Rules</h3>
<script id="rule_row_template" type="x-tmpl-mustache">
    <tr data-key="{{ key }}">
        <td>
				<input type="hidden" name="MedicationSet[medicationSetRules][id][]" value="-1" />
				<input type="hidden" name="MedicationSet[medicationSetRules][site_id][]" value="{{site.id}}" />
				<input type="hidden" name="MedicationSet[medicationSetRules][usage_code][]" value="<?=$this->usage_code?>" />
				{{site.label}}
			</td>
			<td>
				<input type="hidden" name="MedicationSet[medicationSetRules][subspecialty_id][]" value="{{subspecialty.id}}" />
				{{subspecialty.label}}
			</td>
            <td>
                <a href="javascript:void(0);" class="js-delete-rule"><i class="oe-i trash"></i></a>
            </td>
    </tr>
</script>
<script type="text/javascript">
    $(function(){
        $(document).on("click", ".js-delete-rule", function (e) {
            $(e.target).closest("tr").remove();
        });
    });
</script>
<table class="standard" id="rule_tbl">
	<thead>
	<tr>
		<th>Site</th>
		<th>Subspecialty</th>
		<th width="5%">Action</th>
	</tr>
	</thead>
	<tbody>
		<?php
		$rules = array();
		$siteName = '';
		$subspecialtyName = '';

		if (!empty($_GET['id'])) {
			$rules = MedicationSetRule::model()->findByAttributes(['medication_set_id' => $_GET['id'], 'usage_code' => $this->usage_code]);
		}
		?>
			<?php if (empty($rules) OR empty($_GET['id'])) :
				$siteName = Site::model()->findByPk($_GET['default']['site_id']) ? Site::model()->findByPk($_GET['default']['site_id'])->name : '';
				$subspecialtyName = Subspecialty::model()->findByPk($_GET['default']['subspecialty_id']) ? Subspecialty::model()->findByPk($_GET['default']['subspecialty_id'])->name : '';
				?>
			<tr data-key="<?=$rowkey++?>">
				<td>
					<input type="hidden" name="MedicationSet[medicationSetRules][id][]" value="-1" />
					<input type="hidden" name="MedicationSet[medicationSetRules][site_id][]" value="<?= (!empty($_GET['default']['site_id']) ? $_GET['default']['site_id'] : "null")?>" />
                    <input type="hidden" name="MedicationSet[medicationSetRules][usage_code][]" value="<?=$this->usage_code?>" />
                    <?=($siteName ? CHtml::encode($siteName) : "")?>
				</td>
				<td>
					<input type="hidden" name="MedicationSet[medicationSetRules][subspecialty_id][]" value="<?=(!empty($_GET['default']['subspecialty_id']) ? $_GET['default']['subspecialty_id'] : "null")?>" />
					<?=($subspecialtyName ? CHtml::encode($subspecialtyName) : "")?>
				</td>
				<td>
					<a href="javascript:void(0);" class="js-delete-rule"><i class="oe-i trash"></i></a>
				</td>
			</tr>
			<?php endif; ?>



		<?php
		foreach ($medication_set->medicationSetRules as $rule): ?>
		<tr data-key="<?=$rowkey++?>">
			<td>
				<input type="hidden" name="MedicationSet[medicationSetRules][id][]" value="<?=$rule->id?>" />
				<input type="hidden" name="MedicationSet[medicationSetRules][site_id][]" value="<?=$rule->site_id?>" />
                <input type="hidden" name="MedicationSet[medicationSetRules][usage_code][]" value="<?=$rule->usage_code?>" />
				<?=($rule->site_id ? CHtml::encode($rule->site->name) : "")?>
			</td>
			<td>
				<input type="hidden" name="MedicationSet[medicationSetRules][subspecialty_id][]" value="<?=$rule->subspecialty_id?>" />
				<?=($rule->subspecialty_id ? CHtml::encode($rule->subspecialty->name) : "")?>
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
                    <button class="button hint green js-add-set" type="button"><i class="oe-i plus pro-theme"></i></button>
                    <script type="text/javascript">
                        new OpenEyes.UI.AdderDialog({
                            openButton: $('.js-add-set'),
                            itemSets: [
                                new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode($sites) ?>, {'id': 'site', 'multiSelect': false, header: "Site"}),
                                new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode($subspecialties) ?>, {'id': 'subspecialty','multiSelect': false, header: "Subspecialty"}),
                            ],
                            onReturn: function (adderDialog, selectedItems) {

                                var selObj = {};

                                $.each(selectedItems, function(i,e){
                                    selObj[e.itemSet.options.id] = {
                                        id: e.id,
                                        label: e.label
                                    };
                                });

                                var lastkey = $("#rule_tbl > tbody > tr:last").attr("data-key");
                                if(isNaN(lastkey)) {
                                    lastkey = 0;
                                }
                                var key = parseInt(lastkey) + 1;
                                var template = $('#rule_row_template').html();
                                Mustache.parse(template);

                                selObj.key = key;

                                var rendered = Mustache.render(template, selObj);
                                $("#rule_tbl > tbody").append(rendered);
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

