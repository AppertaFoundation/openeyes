
<table class="grid" id="patientMergeRequestList">
    <thead>
        <tr>
            <th class="checkbox"><input type="checkbox" name="selectall" id="selectall" /></th>
            <th class="secondary">
                <?php echo $dataProvider->getSort()->link('secondary_hos_num','Secondary<br><span class="hos_num">hospital num</span>',array('class'=>'sort-link')) ?>
            </th>
            <th></th>
            <th class="primary">
                <?php echo $dataProvider->getSort()->link('primary_hos_num','Primary<br><span class="hos_num">hospital num</span>',array('class'=>'sort-link')) ?>
            </th>
            <th class="status"><?php echo $dataProvider->getSort()->link('status','Status',array('class'=>'sort-link')); ?></th>
            <th class="created"><?php echo $dataProvider->getSort()->link('created_date','Created',array('class'=>'sort-link')); ?></th>
        </tr>
    </thead>
    <tfoot class="pagination-container">
        <tr>
            <td colspan="5">
                <?php echo CHtml::link('Add', array('patientMergeRequest/create'), array('class' => 'button small')); ?>
                <?php echo CHtml::link('Delete', array('patientMergeRequest/delete'), array('id' => 'rq_delete', 'class' => 'button small')); ?>

                <?php echo $this->renderPartial('//admin/_pagination',array(
                        'pagination' => $dataProvider->getPagination()
                )); ?>
            </td>
        </tr>
    </tfoot>
    <tbody>
        <?php foreach ($dataProvider->getData() as $i => $request): ?>
        
            <?php if( $request->status == PatientMergeRequest::STATUS_NOT_PROCESSED ): ?>
        
                <?php $action = Yii::app()->user->checkAccess('Patient Merge') ? 'merge' : 'update'; ?>
                <tr class="clickable" data-id="<?php echo $request->id?>" data-uri="patientMergeRequest/<?php echo $action . '/' . $request->id?>">
            <?php else: ?>
                <tr class="clickable" data-id="<?php echo $request->id?>" data-uri="patientMergeRequest/view/<?php echo $request->id?>">
            <?php endif; ?>
                <td class="checkbox"><input type="checkbox" name="patientMergeRequest[]" value="<?php echo $request->id?>" /></td>
                <td class="secondary"><?php echo $request->secondary_hos_num?></td>
                <td class="into">INTO</td>
                <td class="primary"><?php echo $request->primary_hos_num?></td>
                <td class="status">
                    <div class="circle <?php echo (strtolower(str_replace(" ", "-", $request->getStatusText())) ); ?>" ></div> 
                    <?php echo $request->getStatusText(); ?> 
                </td>
                <td class="created"><?php echo $request->created_date; ?> </td>
            </tr>
        <?php endforeach;?>
    </tbody>
</table>    