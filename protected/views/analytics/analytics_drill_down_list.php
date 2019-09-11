<?php
$coreAPI = new CoreAPI();
$operation_API = new OphTrOperationnote_API();
$cataract = isset($event_list);
$base_patient_url = Yii::app()->createUrl('/patient/summary');
foreach (($cataract? $event_list : $patient_list) as $item) {
    if ($cataract) {
        $row = array_search($item['patient_id'], array_column($patient_list, 'id'));
        $patient = $patient_list[$row];
    } else {
        $patient = $item;
    }
    ?>
    <tr id="<?= $cataract? $item['event_id']: $patient['id']; ?>" class="analytics-patient-list-row <?=$cataract? 'analytics-event-list-row':'';?> clickable"
        data-link="<?php echo "$base_patient_url/" . $patient['id']; ?>">
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

