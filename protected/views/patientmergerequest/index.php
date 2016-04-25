<?php
/* @var $this PatientMergeRequestController */
/* @var $dataProvider CActiveDataProvider */

?>

<h1 class="badge">Patient Merge Request List</h1>

<?php /*$this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); */?>

<div id="patientMergeWrapper" class="container content">

    <div class="row">
        <div class="large-8 column large-centered">
            
            <section class="box requestList js-toggle-container">
                <div class="grid-view" id="inbox-table">

                    <table class="grid" id="patientMergeList">
                        <thead>
                            <tr>
                                <th>Secondary</th>
                                <th></th>
                                <th>Primary</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($dataProvider->getData() as $request): ?>
                                <tr>
                                    <td>
                                        <?php echo $request->secondary_hos_num; ?>
                                    </td>
                                    
                                    <td>
                                        <span> INTO </span>
                                    </td>
                                    
                                    <td>
                                        <?php echo $request->primary_hos_num; ?>
                                    </td>
                                    
                                    <td>
                                        <?php echo $request->getStatusText(); ?>
                                    </td>
                                    <td class="actions">
                                        <?php echo CHtml::link('view',array('patientmergerequest/view', 'id' => $request->id), array('class' => 'button small right')); ?>
                                        <?php echo CHtml::link('merge',array('patientmergerequest/view', 'id' => $request->id), array('class' => 'warning button small right')); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tr>
                            
                        </tbody>
                    </table>
            </section>
        </div>
    </div>
</div>


