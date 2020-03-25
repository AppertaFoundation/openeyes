<?php
/**
 * @var $this CaseSearchPlot
 */

?>
    <script src="<?= Yii::app()->assetManager->createUrl('../../node_modules/plotly.js-dist/plotly.js')?>"></script>
<div class="results-options">
    Select plot:
    <?= CHtml::dropDownList('selected_variable', isset($this->variables[0]) ? $this->variables[0]->field_name : null, CHtml::listData($this->variables, 'field_name', 'label'), array('id' => 'selected-variable')) ?>
    <span class="tabspace"></span>
    <button data-selector="<?= $this->list_selector ?>">View as list</button>
</div>
<?php foreach ($this->variables as $id => $variable) {
    $x1 = null;
    $y1 = null;
    $customdata1 = null;

    $x0 = array_column($this->variable_data[$variable->field_name][0], $variable->field_name);
    $y0 = array_column($this->variable_data[$variable->field_name][0], 'frequency');
    $customdata0 = array_column($this->variable_data[$variable->field_name][0], 'patient_id_list');
    if (array_key_exists(1, $this->variable_data[$variable->field_name])) {
        $x1 = array_column($this->variable_data[$variable->field_name][1], $variable->field_name);
        $y1 = array_column($this->variable_data[$variable->field_name][1], 'frequency');
        $customdata1 = array_column($this->variable_data[$variable->field_name][1], 'patient_id_list');
    }

    $n = $this->total_patients
    ?>
    <!-- BEGIN PLOT CONTAINER -->
    <div
        id="<?= $variable->field_name ?>"
        style="height: calc(100vh - 220px); margin:10px 0;<?= $id !== 0 ? ' display: none;' : '' ?>"
        class="js-plotly-plot"
        data-x0="<?= CHtml::encode(json_encode($x0)) ?>"
        data-y0="<?= CHtml::encode(json_encode($y0)) ?>"
        data-patient-id-list0="<?= CHtml::encode(json_encode(array_map(
                static function ($item) {
                    return explode(', ', $item);
                }, $customdata0))) ?>"
        data-x1="<?= $x1 ? CHtml::encode(json_encode($x1)) : null ?>"
        data-y1="<?= $y1 ? CHtml::encode(json_encode($y1)) : null ?>"
        data-patient-id-list1="<?= $customdata1 ? CHtml::encode(json_encode(array_map(
            static function ($item) {
                return explode(', ', $item);
            }, $customdata1))) : null ?>"
        data-total="<?= $n ?>"
        data-var-name="<?= $variable->field_name ?>"
        data-var-label="<?= $variable->label ?>"
        data-var-unit="<?= $variable->unit ? " ({$variable->unit})" : ''?>"></div>
    <!-- END PLOT CONTAINER -->
<?php } ?>