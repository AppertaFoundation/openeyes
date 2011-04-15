<strong>Book Operation</strong>
<div class="row">
	<label for="ElementOperation_value">Eye(s) to be operated on:</label>
	<?php echo CHtml::activeRadioButtonList($model, 'eye', $model->getEyeOptions(), 
		array('separator' => ' &nbsp; ')); ?>
</div>
<div class="row">
	<label for="ElementOperation_value">Add procedure:</label>
<?php
$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
    'name'=>'procedure_id',
    'sourceUrl'=>array('procedure/autocomplete'),
    'options'=>array(
        'minLength'=>'2',
		'select'=>"js:function() { alert('hi'); }"
    ),
    'htmlOptions'=>array(
        'style'=>'height:20px;width:200px;'
    ),
));
?>
</div>
<div>
	<div>
		<span>Procedures Added</span>
		<span>Duration</span>
	</div>
	<div>
<?php
$this->widget('zii.widgets.jui.CJuiSortable', array(
	'id'=>'procedure_list',
    'items'=>array(
        'id1'=>'<div class="ui-widget-content">Item 1</div><div class="ui-widget-content">30</div>',
        'id2'=>'<div class="ui-widget-content">Item 2</div><div class="ui-widget-content">30</div>',
        'id3'=>'<div class="ui-widget-content">Item 3</div><div class="ui-widget-content">30</div>',
    ),
	'itemTemplate'=>'<li id="{id}" class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>{content}</li>',
	'options'=>array(
		'placeholder'=>'ui-state-highlight',
		'cursor'=>'move'
	)
));
?>
	</div>
</div>
<p>or browse procedures for all services</p>
<div class="row">
	<label for="ElementOperation_value">Consultant required?</label>
	<?php echo CHtml::activeRadioButtonList($model, 'consultant_required', 
		$model->getConsultantOptions(), array('separator' => ' &nbsp; ')); ?>
</div>
<div class="row">
	<label for="ElementOperation_value">Anaesthetic required:</label>
	<?php echo CHtml::activeRadioButtonList($model, 'anaesthetic_type', 
		$model->getAnaestheticOptions(), array('separator' => ' &nbsp; ')); ?>
</div>
<div class="row">
	<label for="ElementOperation_value">Overnight Stay required?</label>
	<?php echo CHtml::activeRadioButtonList($model, 'overnight_stay', 
		$model->getOvernightOptions(), array('separator' => ' &nbsp; ')); ?>
</div>