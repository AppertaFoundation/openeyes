<div class="view">
	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>: <?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?></b>

                <table>
                        <tr>
                                <td>Event type</td><td><?php echo $data->possibleElementType->eventType->name;?></td>
                        </tr>
                        <tr>
                                <td>Element type</td><td><?php echo $data->possibleElementType->elementType->name;?></td>
                        </tr>
                        <tr>
                                <td>Specialty</td><td><?php echo $data->specialty->name;?></td>
                        </tr>
                        <tr>
                                <td>First in episode</td><td><?php if ($data->first_in_episode) {echo 'Yes';} else {echo 'No';} ?></td>
                        </tr>
                </table>


</div>
