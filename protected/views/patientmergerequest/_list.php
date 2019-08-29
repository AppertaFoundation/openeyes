
<table class="standard" id="patientMergeRequestList">
    <thead>
        <tr>
            <th class="checkbox"><input type="checkbox" name="selectall" id="selectall" /></th>
            <th class="secondary">
                <!--                Parameterised secondary and primary hospital number - CERA-519-->
                <?php echo $data_provider->getSort()->link('secondary_hos_num', 'Secondary<br><span class="hos_num">'. (Yii::app()->params["hos_num_label"]). ((Yii::app()->params["institution_code"]=="CERA")?"":" Number").'</span>', array('class' => 'sort-link')) ?>            </th>
            <th></th>
            <th class="primary">
                <?php echo $data_provider->getSort()->link('primary_hos_num', 'Primary<br><span class="hos_num">'. (Yii::app()->params["hos_num_label"]). ((Yii::app()->params["institution_code"]=="CERA")?"":" Number").'</span>', array('class' => 'sort-link')) ?>            </th>
            <th class="status"><?php echo $data_provider->getSort()->link('status', 'Status', array('class' => 'sort-link')); ?></th>
            <th class="created"><?php echo $data_provider->getSort()->link('created_date', 'Created', array('class' => 'sort-link')); ?></th>
            <?php if ($filters['show_merged']) :?>
            <th class="created"><?php echo $data_provider->getSort()->link('last_modified_date', 'Merged', array('class' => 'sort-link')); ?></th>
            <?php endif; ?>
        </tr>
        <?php if ($data_provider->itemCount): ?>
        <tr class="table-filter">
            <td> <img class="loader" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif') ?>"
                    alt="loading..." style="display: none;"/></td>
            <td class="filter-col"><input id="secondary_hos_num_filter" name="PatientMergeRequestFilter[secondary_hos_num_filter]" type="text" value="<?php echo isset($filters['secondary_hos_num_filter']) ? $filters['secondary_hos_num_filter'] : '';?>"></td>
            <td></td>
            <td class="filter-col"><input id="primary_hos_num_filter" name="PatientMergeRequestFilter[primary_hos_num_filter]" type="text" value="<?php echo isset($filters['primary_hos_num_filter']) ? $filters['primary_hos_num_filter'] : '';?>"></td>
            <td></td>
            <td></td>
            <?php if ($filters['show_merged']) :?><td></td><?php endif; ?>
        </tr>
        <?php endif; ?>
    </thead>
    <tfoot class="pagination-container">
        <tr>
            <td colspan="5">
                <?=\CHtml::link('Add', array('patientMergeRequest/create'), array('class' => 'button small')); ?>
                <?=\CHtml::link('Delete', array('patientMergeRequest/delete'), array('id' => 'rq_delete', 'class' => 'button small', 'disabled' => ($filters['show_merged'] ? 'disabled' : ''))); ?>

                <?php echo $this->renderPartial('//admin/_pagination', array(
                        'pagination' => $data_provider->getPagination(),
                )); ?>
            </td>
        </tr>
    </tfoot>
    <tbody>

        <?php if ($data_provider->itemCount): ?>
            <?php foreach ($data_provider->getData() as $i => $request): ?>

                <?php if ($request->status == PatientMergeRequest::STATUS_NOT_PROCESSED): ?>
                    <tr class="clickable" data-id="<?php echo $request->id?>" data-uri="patientMergeRequest/<?php echo 'view/'.$request->id?>">
                <?php else: ?>
                    <tr class="clickable" data-id="<?php echo $request->id?>" data-uri="patientMergeRequest/log/<?php echo $request->id?>">
                <?php endif; ?>
                    <td class="checkbox">
                        <input type="checkbox" name="patient_merge_request_ids[]" value="<?php echo $request->id?>" <?php echo $request->status == PatientMergeRequest::STATUS_NOT_PROCESSED ? '' : 'disabled'?>>
                    </td>
                    <td class="secondary">
                        <?php if($request->secondary_hos_num):?>
                            <?php echo $request->secondary_hos_num;?>
                        <?php else: ?>
                            <?php
                                $patient = Patient::model()->findByPk($request->secondary_id);
                                echo $patient->fullName;
                            ?>
                        <?php endif ?>
                    </td>
                    <td class="into">INTO</td>
                    <td class="primary">
                        <?php if($request->primary_hos_num):?>
                            <?php echo $request->primary_hos_num;?>
                        <?php else: ?>
                            <?php
                            $patient = Patient::model()->findByPk($request->primary_id);
                            echo $patient->fullName;
                            ?>
                        <?php endif ?>
                    </td>
                    <td class="status">
                        <div class="circle <?php echo strtolower(str_replace(' ', '-', $request->getStatusText())); ?>" ></div>
                        <?php echo $request->getStatusText(); ?>
                    </td>
                    <td class="created"><?php echo $request->created_date; ?> </td>
                    <?php if ($filters['show_merged']) :?>
                    <td class="merged"><?php echo $request->last_modified_date; ?> </td>
                    <?php endif; ?>

                </tr>
            <?php endforeach;?>
        <?php else: ?>
                <tr><td colspan="6" >No results found.</td></tr>
        <?php endif; ?>

    </tbody>
</table>