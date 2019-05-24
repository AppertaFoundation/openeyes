<?php
    /** @var OphCiExaminationRisk $model */
    $active_sets = array_map(function ($e) { return $e->id; }, $model->medicationSets);
?>

<h3>Related medication sets</h3>
<table id="medication-sets-list">
	<tbody>
	<?php foreach ($model->medicationSets as $set): ?>
    <?php /** @var MedicationSet $set */ ?>
	<tr>
		<td>
            <label for="chk_risk_set_<?=$set->id?>">
			<?php echo CHtml::encode($set->name); ?>
			<?php if($set->rulesString()) { echo "(".$set->rulesString().")"; } ?>
            </label>
		</td>
        <td>
            <a class="risk-set-remove-btn" href="javascript:void(0)"><i class="oe-i trash"></i></a>
            <input type="hidden" id="chk_risk_set_<?=$set->id?>" name="OEModule_OphCiExamination_models_OphCiExaminationRisk[medicationSets][]" value="<?=$set->id?>">
        </td>
	</tr>
	<?php endforeach; ?>
	</tbody>
    <tfoot class="pagination-container">
    <tr>
        <td colspan="3">
            <div class="patient-activity">
                <input placeholder="Type to search" class="cols-full search autocompletesearch" id="medication_set_id" type="text" value="" name="medication_set_id" autocomplete="off">
                <ul class="oe-autocomplete hidden" id="ui-id-1" tabindex="0">
                </ul>
                <div class="data-group no-result warning alert-box hidden">
                    <div class="small-12 column text-center">
                        No results found.
                    </div>
                </div>
                <div class="data-group min-chars warning alert-box hidden">
                    <div class="small-12 column text-center">
                        Minimum of 2 characters
                    </div>
                </div>
            </div>
        </td>
    </tr>
    </tfoot>
</table>

<script type="text/javascript" src="<?= Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.widgets.js') . '/AutoCompleteSearch.js', false, -1); ?>"></script>
<script>
    OpenEyes.UI.AutoCompleteSearch.init({
        input: $('#medication_set_id'),
        url: '/OphCiExamination/risksAdmin/search',
        onSelect: function(){
            let AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
            addRiskMedSetTr(AutoCompleteResponse.label,'medication-sets-list', AutoCompleteResponse.id, 0  );
            setRiskMedSetTableText();
        }
    });

    function addRiskMedSetTr(selected_text, table_id, select_value, display_order)
    {

        var $table = $('#' + table_id);
        var $tr = $('<tr>');
        var $td_name = $('<td>', {class: "medication-set-name"}).text(selected_text);

        var $hidden_input = $("<input>", {
            type:"hidden",
            id:'chk_risk_set_' + $('#' + table_id + ' tr').length,
            name: 'OEModule_OphCiExamination_models_OphCiExaminationRisk[medicationSets][' + $('#' + table_id + ' tr').length +']',
            value: select_value,
        });
        $hidden_input.data('display_order', display_order);

        var $td_action = $('<td>',{class:'right'}).html( "<a class='risk-set-remove-btn' href='javascript:void(0)'><i class='oe-i trash'></i></a>" );
        $td_action.append($hidden_input);

        $tr.append($td_name);
        $tr.append($td_action);
        $table.append( $tr );
    }

    function setRiskMedSetTableText()
    {
        var $medicationsets__table = $('#medication-sets-list');

        var $active_form = $medicationsets__table.closest('.active-form');
        if( $medicationsets__table.find('tbody').find('tr').length === 0  ){
            $active_form.find('.recorded').hide();
            $active_form.find('.no-recorded').show();
            $medicationsets__table.hide();
        } else {
            $active_form.find('.recorded').show();
            $active_form.find('.no-recorded').hide();
            $medicationsets__table.show();
        }
    }

    $('#medication-sets-list').on('click','a.risk-set-remove-btn', function(){

        var value = $(this).parent().find('input[type=hidden]').val();
        var text = $(this).closest('tr').find('.postop-complication-name').text();

        var select_id = $(this).closest('table').attr('id').replace('list', 'select');

        $select = $('#' + select_id);
        $select.append( $('<option>',{value: value}).text(text));

        $(this).closest('tr').remove();

        setPostOpComplicationTableText();
    });

</script>
<?php
?>