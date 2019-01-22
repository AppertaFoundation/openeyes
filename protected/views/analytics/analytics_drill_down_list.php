<?php $coreAPI = new CoreAPI();?>
<div class="analytics-patient-list" style="display: none;" >
    <div class="flex-layout">
        <h3 id="js-list-title">Patient List</h3>
        <button id="js-back-to-chart" class="selected" >Back to chart</button>
    </div>
    <table>
        <colgroup>
            <col style="width: 100px;">
            <col style="width: 100px;">
            <col style="width: 100px;">
        </colgroup>
        <thead>
        <tr>
            <th class="drill_down_patient_list">Hospital No</th>
            <th>Gender</th>
            <th>Age</th>
            <th class="drill_down_patient_list">Name</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($patient_list as $patient) { ?>
            <tr id="<?=$patient->id?>" class="analytics-patient-list-row clickable" data-link="<?=$coreAPI->generateEpisodeLink($patient)?>" style="display: none">
                <td class="drill_down_patient_list"><?= $patient->hos_num; ?></td>
                <td style="display: none;"><?=$patient->first_name?></td>
                <td style="display: none;"><?=$patient->last_name?></td>
                <td style="display: none;"><?=$patient->dob?></td>
                <td><?= $patient->gender; ?></td>
                <td><?= $patient->getAge(); ?></td>
                <td class="drill_down_patient_list"><?= $patient->getFullName(); ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<script src="<?= Yii::app()->assetManager->createUrl('js/analytics/analytics_csv.js')?>"></script>
<script type="text/javascript">
    $('.clickable').click(function () {
        var link = $(this).data('link');
        window.location.href = link;
    });
    $('#js-back-to-chart').click(function () {
        $('.analytics-charts').show();
        $('.analytics-patient-list').hide();
        $('.analytics-patient-list-row').hide();
    });
</script>