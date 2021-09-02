<?php
?>

<div class="oe-full-header flex-layout">
    <div class="title wordcaps">Scan Results</div>
</div>
<div class="oe-full-content">
    <div class="cols-9">
            <form action="/VirusScan/removeInfectedFiles">
                <?php
                echo CHtml::hiddenField('scan_id', $data['scan_id']);
                echo CHtml::submitButton(
                    'Remove Infected Files',
                    array('class' => 'button red hint')
                );
                ?>
            </form>

            <?php
            $dataProvider = new CActiveDataProvider('VirusScanItem', array(
                'criteria'=>array(
                    'condition'=>'parent_scan_id='.$data['scan_id'],
                    'order'=>'scan_result ASC',
                ),
                'countCriteria'=>array(
                    'condition'=>'parent_scan_id='.$data['scan_id'],
                    // 'order' and 'with' clauses have no meaning for the count query
                ),
                'pagination'=>array(
                    'pageSize'=>100,
                ),
                ));
                $this->widget('zii.widgets.grid.CGridView', array(
                    'dataProvider'=>$dataProvider,
                    'itemsCssClass'=>'standard',
                    'columns'=>array(
                        'file_uid:text:UID',
                        'scan_result:text:Scan Result',
                        'details:text:Details',
                    ),
                ));
                ?>
    </div>
</div>
