<?php
$coreAPI = new CoreAPI();
$operation_API = new OphTrOperationnote_API();
$cataract = (isset($data['event_list']) && $data['event_list']);
$base_patient_url = Yii::app()->createUrl('/patient/summary');
foreach (($cataract ? $data['event_list'] : $data['patient_list']) as $item) {
    $tr_id = $cataract ? $item['event_id'] : $item['patient_id'];
    ?>
    <tr id="<?= $tr_id; ?>" class="analytics-patient-list-row <?=$cataract? 'analytics-event-list-row':'';?> clickable"
        data-link="<?php echo "$base_patient_url/" . $item['patient_id']; ?>"
        style="cursor:pointer;">
        <td class="drill_down_patient_list js-csv-data js-csv-hos_num"
            style="vertical-align: center;"><?= $item['hos_num'] ?></td>
        <td class="drill_down_patient_list js-csv-data js-csv-nhs_num"
            style="vertical-align: center;"><?= $item['nhs_num'] ?></td>
        <td class="drill_down_patient_list js-csv-name"
            style="vertical-align: center;"><?= $item['name']; ?></td>
        <td style="vertical-align: center;" class="js-csv-dob">
            <span class="oe-date">
                <?php if (strtotime($item['dob']) !== false) {
                    $dob = strtotime($item['dob']);
                    ?>
                    <span class="day"><?=date('j', $dob)?></span>
                    <span class="mth"><?=date('M', $dob)?></span>
                    <span class="yr"><?=date('Y', $dob)?></span>
                <?php } else {?>
                    <?=$item['dob']?>
                <?php }?>
            </span>
        </td>
        <td class="js-anonymise js-csv-data js-csv-age"
            style="vertical-align: center;"><?= $item['age'] ?></td>
        <td class="js-anonymise js-csv-gender"
            style="vertical-align: center;"><?= $item['gender'] ?></td>
        <td class="text-left" style="vertical-align: center;" class="js-csv-diagnoses"><?= $item['diagnoses'] ?></td>
        <?=$cataract? '<td class="text-left" style="vertical-align: center;">'.$item['eye_side'].'</td>':''?>
        <td class="text-left" style="vertical-align: center;" class="js-csv-procedures"><?= $item['procedures'] ?></td>
        <?=$cataract? '<td style="vertical-align: center;">'.Helper::convertDate2NHS($item['event_date']).'</td>':''?>
    </tr>
<?php } ?>

