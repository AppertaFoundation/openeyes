History view 2
<br />
<?php $this->widget('zii.widgets.CDetailView', array(
    'data'=>$model,
    'attributes'=>array(
        'id',
        'event_id',
        'description',
    ),
)); ?>