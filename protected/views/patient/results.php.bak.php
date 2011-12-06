<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'patient-grid',
	'dataProvider'=>$dataProvider,
	'template'=>"{items}\n{pager}",
	'columns'=>array(
		'pas_key',
		'title',
		'first_name',
		'last_name',
		'dob',
		'gender',
		'hos_num',
		'nhs_num'
	)
));
