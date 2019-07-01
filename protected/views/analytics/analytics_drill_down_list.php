<?php $coreAPI = new CoreAPI();
      $operation_API = new OphTrOperationnote_API();
      $cataract = isset($event_list);
      ?>
<div class="analytics-patient-list <?=$cataract? 'analytics-event-list':'';?>" style="display: none;" >
    <div class="flex-layout">
        <h3 id="js-list-title">Patient List</h3>
        <button id="js-back-to-chart" class="selected js-plot-display-label" >Back to chart</button>
    </div>
    <table>
        <colgroup>
            <col style="width: 100px;">
            <col style="width: 100px">
            <col style="width: 200px;">
            <col style="width: 100px;">
            <col style="width: 50px;">
           <?= $cataract?
               ' <col style="width: 350px;">
                 <col style="width: 50px;">
                 <col style="width: 400px;">
                 <col style="width: 100px;">':
                '<col style="width: 450px;">
                 <col style="width: 450px;">';?>
        </colgroup>
        <thead>
        <tr>
            <th class="drill_down_patient_list text-left" style="vertical-align: center;">Hospital No</th>
            <th class="drill_down_patient_list text-left" style="vertical-align: center;">Name</th>
            <th class="text-left" style="vertical-align: center;">DOB</th>
            <th class="text-left" style="vertical-align: center;">Age</th>
            <th class="text-left" style="vertical-align: center;">Gender</th>
            <th class="text-left" style="vertical-align: center;">Diagnoses</th>
            <?=$cataract? '<th class="text-left" style="vertical-align: center;">Eye</th>': '';?>
            <th class="text-left" style="vertical-align: center;">Procedures</th>
            <?=$cataract? '<th style="vertical-align: center;">Date</th>': '';?>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach (($cataract? $event_list : $patient_list) as $item) {
              if ($cataract){
                $row = array_search($item['patient_id'], array_column($patient_list, 'id'));
                $patient = $patient_list[$row];
              }else{
                $patient = $item;
              }
            ?>
            <tr id="<?= $cataract? $item['event_id']: $patient['id']; ?>" class="analytics-patient-list-row <?=$cataract? 'analytics-event-list-row':'';?> clickable"
                data-link="<?php echo Yii::app()->createUrl("/patient/summary/" . $patient['id']); ?>"
                style="display: none">
                <td class="drill_down_patient_list js-csv-data js-csv-hos_num"
                    style="vertical-align: center;"><?= $patient['hos_num']; ?></td>
                <td class="drill_down_patient_list js-csv-name"
                    style="vertical-align: center;"><?= $patient['name']; ?></td>
                <td style="vertical-align: center;" class="js-csv-dob"><?= $patient['dob'] ?></td>
                <td class="js-anonymise js-csv-data js-csv-age"
                    style="vertical-align: center;"><?= $patient['age']; ?></td>
                <td class="js-anonymise js-csv-gender"
                    style="vertical-align: center;"><?= $patient['gender']; ?></td>
                <td class="text-left" style="vertical-align: center;" class="js-csv-diagnoses"><?= $patient['diagnoses']; ?></td>
                <?=$cataract? '<td class="text-left" style="vertical-align: center;">'.$item['eye_side'].'</td>':'';?>
                <td class="text-left" style="vertical-align: center;" class="js-csv-procedures"><?= $cataract? $item['procedures']:$patient['procedures']; ?></td>
                <?=$cataract? '<td style="vertical-align: center;">'.Helper::convertDate2NHS($item['event_date']).'</td>':'';?>
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

