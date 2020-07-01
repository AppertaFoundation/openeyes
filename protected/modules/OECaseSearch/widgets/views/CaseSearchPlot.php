<?php
/**
 * @var $this CaseSearchPlot
 */

?>
    <script src="<?= Yii::app()->assetManager->createUrl('../../node_modules/plotly.js-dist/plotly.js')?>"></script>
<div class="results-options"<?= !$this->display ? ' style="display: none;"' : null ?>>
    Select plot:
    <?= CHtml::dropDownList('selected_variable', isset($this->variables[0]) ? $this->variables[0]->field_name : null, CHtml::listData($this->variables, 'field_name', 'label'), array('id' => 'selected-variable')) ?>
    <span class="tabspace"></span>
    <button data-selector="<?= $this->list_selector ?>">View as list</button>
</div>
<?php foreach ($this->variables as $id => $variable) {
    $x = array_column($this->variable_data[$variable->field_name], $variable->getPrimaryDataPointName());
    $y = array_column($this->variable_data[$variable->field_name], 'frequency');
    $customdata = array_column($this->variable_data[$variable->field_name], 'patient_id_list');

    $n = $this->total_patients
    ?>
    <!-- BEGIN PLOT CONTAINER -->
    <div
        id="<?= $variable->field_name ?>"
        style="height: calc(100vh - 220px); margin:10px 0;<?= $id !== 0 ? ' display: none;' : '' ?>"
        class="js-plotly-plot"
        data-x="<?= CHtml::encode(json_encode($x)) ?>"
        data-y="<?= CHtml::encode(json_encode($y)) ?>"
        data-patient-id-list="<?= CHtml::encode(json_encode(array_map(
            static function ($item) {
                    return explode(', ', $item);
            },
            $customdata
        ))) ?>"
        data-total="<?= $n ?>"
        data-bin-size="<?= $variable->bin_size ?>"
        data-min-value="<?= $variable->min_value ?>"
        data-var-name="<?= $variable->field_name ?>"
        data-var-label="<?= $variable->label ?>"
        data-var-unit="<?= $variable->x_label ?>"></div>
    <!-- END PLOT CONTAINER -->
<?php } ?>