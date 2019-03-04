<?php $coreAPI = new CoreAPI();
      $operation_API = new OphTrOperationnote_API();?>
<div class="analytics-patient-list" style="display: none;" >
    <div class="flex-layout">
        <h3 id="js-list-title">Patient List</h3>
        <button id="js-back-to-chart" class="selected js-plot-display-label" >Back to chart</button>
    </div>
    <table>
        <colgroup>
            <col style="width: 100px;">
            <col style="width: 200px;">
            <col style="width: 100px;">
            <col style="width: 50px;">
            <col style="width: 50px;">
            <col style="width: 450px;">
            <col style="width: 450px;">
        </colgroup>
        <thead>
        <tr>
            <th class="drill_down_patient_list text-left" style="vertical-align: center;">Hospital No</th>
            <th class="drill_down_patient_list text-left" style="vertical-align: center;">Name</th>
            <th class="text-left" style="vertical-align: center;">DOB</th>
            <th clsas="text-left" style="vertical-align: center;">Age</th>
            <th clsas="text-left" style="vertical-align: center;">Gender</th>
            <th clsas="text-left" style="vertical-align: center;">Diagnoses</th>
            <th clsas="text-left" style="vertical-align: center;">Procedures</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($patient_list as $patient) { ?>
            <tr id="<?=$patient->id?>" class="analytics-patient-list-row clickable" data-link="<?=$coreAPI->generatePatientLandingPageLink($patient)?>" style="display: none">
                <td class="drill_down_patient_list js-csv-data" style="text-align: center;vertical-align: center;"><?= $patient->hos_num; ?></td>
                <td class="drill_down_patient_list" style="text-align: center;vertical-align: center;"><?= $patient->getFullName(); ?></td>
                <td style="text-align: center;vertical-align: center;"><?=$patient->dob?></td>
                <td class="js-anonymise js-csv-data" style="text-align: center;vertical-align: center;"><?= $patient->getAge(); ?></td>
                <td class="js-anonymise js-csv-data" style="text-align: center;vertical-align: center;"><?= $patient->gender; ?></td>
                <td style="text-align: center;vertical-align: center;"><?= $patient->getUniqueDiagnosesString();?></td>
                <?php
                    $operations = $operation_API->getOperationsSummaryData($patient);
                    $procedure_lists = "";
                    foreach ($operations as $operation){
                        $procedure_lists = $procedure_lists.$operation['operation'].",";
                    }
                    $procedure_lists = substr($procedure_lists,0,-1);
                ?>
                <td class="text-left" style="vertical-align: center;"><?=$procedure_lists; ?></td>
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
    $('.js-plot-display-label').click(function () {
        $('.analytics-charts').show();
        $('.analytics-patient-list').hide();
        $('.analytics-patient-list-row').hide();
    })
</script>