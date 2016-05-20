<?php
/* @var $this PatientMergeRequestController */
/* @var $dataProvider CActiveDataProvider */

?>

<h1 class="badge">Patient Merge Request List</h1>

<div id="patientMergeWrapper" class="container content">
    
    <?php $this->renderPartial('//base/_messages')?>

    <div class="row">
        <div class="large-8 column large-centered">
            
            <section class="box requestList js-toggle-container">
                <div class="grid-view" id="inbox-table">

                    <form id="patientMergeList">
                        <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken?>" />
                        <table class="grid">
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
                                                'pagination' => $pagination
                                        )); ?>
                                    </td>
                                </tr>
                            </tfoot>
                            <tbody>
                                <?php foreach ($dataProvider->getData() as $i => $request): ?>
                                    <tr class="clickable" data-id="<?php echo $request->id?>" data-uri="/patientMergeRequest/merge/<?php echo $request->id?>">
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

                    </form>                    
            </section>
        </div>
    </div>
</div>


