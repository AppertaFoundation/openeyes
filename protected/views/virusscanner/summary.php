<?php
?>

<div class="oe-full-header flex-layout">
    <div class="title wordcaps">Scan Results</div>
</div>
<div class="oe-full-content">
    <p>The following files were quarantined:</p>
    <div class="cols-9">
            <?php
                $dataProvider = new CArrayDataProvider($quarantined_file_details, array(
                        'id'=>'uid',
                        'keyField'=>'uid',
                        'sort'=>array(
                            'attributes'=>array(
                                'uid', 'quarantine_reason',
                            ),
                        ),
                        'pagination'=>array(
                            'pageSize'=>100,
                        ),
                    ));
                $this->widget('zii.widgets.grid.CGridView', array(
                    'dataProvider'=>$dataProvider,
                    'itemsCssClass'=>'standard',
                    'columns'=>array(
                        'uid:text:Original File UID',
                        'quarantine_reason:text:Quarantine Reason',
                    ),
                ));
                ?>
    </div>
</div>
