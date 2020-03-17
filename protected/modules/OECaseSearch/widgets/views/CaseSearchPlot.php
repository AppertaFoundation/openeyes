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
    $x = array_column($this->variable_data[$variable->field_name], $variable->field_name);
    $y = array_column($this->variable_data[$variable->field_name], 'frequency');
    $n = $this->total_patients
?>
    <!-- BEGIN PLOT CONTAINER -->
    <div
        id="<?= $variable->field_name ?>"
        style="height: calc(100vh - 220px); margin:10px 0;<?= $id !== 0 ? ' display: none;' : '' ?>"
        class="js-plotly-plot"
        data-x="<?= CHtml::encode(json_encode($x)) ?>"
        data-y="<?= CHtml::encode(json_encode($y)) ?>"
        data-total="<?= $n ?>"
        data-var-name="<?= $variable->field_name ?>"
        data-var-label="<?= $variable->label ?>"
        data-var-unit="<?= $variable->unit ? " ({$variable->unit})" : ''?>'"></div>
    <!-- END PLOT CONTAINER -->
<?php } ?>