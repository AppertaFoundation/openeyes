This is the extra episode summary detail page for Medical Retinal.
<br />
<br />
As an example it includes the exampleSummary widget.
<br />
<?php $this->widget('application.components.summaryWidgets.ExampleSummary', array(
   'episode_id' => $episode->id
)); ?>
<br />
<br />
And a link to view the exampleSummary widget on a different page:
<br />
<?php

echo CHtml::link(
	'exampleSummary',
	Yii::app()->createUrl('clinical/summary', array(
		'id' => $episode->id,
		'summary' => 'ExampleSummary'
	))
);
