<?php
$this->breadcrumbs=array(
	'Site Element Types'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List SiteElementType', 'url'=>array('index')),
	array('label'=>'Update SiteElementType', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Manage SiteElementType', 'url'=>array('admin')),
);
?>

<h1>View Site Element Type for: </h1>

<div class="view">

                <table>
                        <tr>
                                <td>Event type</td><td><?php echo $model->possibleElementType->eventType->name;?></td>
                        </tr>
                        <tr>
                                <td>Element type</td><td><?php echo $model->possibleElementType->elementType->name;?></td>
                        </tr>
                        <tr>
                                <td>Specialty</td><td><?php echo $model->specialty->name;?></td>
                        </tr>
                        <tr>
                                <td>First in episode</td><td><?php if ($model->first_in_episode) {echo 'Yes';} else {echo 'No';} ?></td>
                        </tr>
                </table>
</div>

<div class="view">
        <b><?php echo CHtml::encode($model->getAttributeLabel('id')); ?>:</b>
        <?php echo CHtml::link(CHtml::encode($model->id), array('view', 'id'=>$model->id)); ?>
        <br />

        <b><?php echo CHtml::encode($model->getAttributeLabel('possible_element_type_id')); ?>:</b>
        <?php echo CHtml::encode($model->possible_element_type_id); ?>
        <br />

        <b><?php echo CHtml::encode($model->getAttributeLabel('specialty_id')); ?>:</b>
        <?php echo CHtml::encode($model->specialty->name); ?>
        <br />

        <b><?php echo CHtml::encode($model->getAttributeLabel('view_number')); ?>:</b>
        <?php echo CHtml::encode($model->view_number); ?>
        <br />

        <b><?php echo CHtml::encode($model->getAttributeLabel('required')); ?>:</b>
        <?php if ($model->required) {echo 'Yes';} else {echo 'No';} ?>
        <br />

        <b><?php echo CHtml::encode($model->getAttributeLabel('first_in_episode')); ?>:</b>
        <?php if ($model->first_in_episode) {echo 'Yes';} else {echo 'No';} ?>
        <br />
</div>

