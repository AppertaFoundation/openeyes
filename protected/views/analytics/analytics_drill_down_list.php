<?php
$coreAPI = new CoreAPI();
$operation_API = new OphTrOperationnote_API();
$cataract = (isset($data['event_list']) && $data['event_list']);
$base_patient_url = Yii::app()->createUrl('/patient/summary');
foreach (($cataract ? $data['event_list'] : $data['patient_list']['res']) as $item) {
    $tr_id = $cataract ? $item['event_id'] : $item['patient_id'];
    ?>
    <tr id="<?= $tr_id ?>" class="analytics-patient-list-row <?=$cataract? 'analytics-event-list-row':''?> clickable"
        data-link="<?php echo "$base_patient_url/" . $item['patient_id']; ?>" style="cursor:pointer;">
        <?php
        foreach ($headers as $header) {
            if (in_array($header, ['patient_id', 'last name', 'event_id'])) {
                continue;
            }
            $header = trim($header);
            switch (trim($header)) {
                case 'first name':
                    ?>
                        <td class="js-csv-data js-csv-<?= $header; ?>" style="vertical-align: center;"><?= $item['first name'].' '.$item['last name'] ?></td>
                    <?php
                    break;

                case 'dob':
                    ?>
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
                    <?php
                    break;

                case 'eye_side':
                    ?>
                        <?=$cataract? '<td class="text-left" style="vertical-align: center;">'.$item['eye_side'].'</td>':''?>
                    <?php
                    break;

                case 'event_date':
                    ?>
                        <?=$cataract? '<td style="vertical-align: center;">'.Helper::convertDate2NHS($item['event_date']).'</td>':''?>
                    <?php
                    break;

                default:
                    ?>
                        <td class="js-csv-data js-csv-<?= $header; ?>" style="vertical-align: center;"><?= $item[$header] ?></td>
                    <?php
                    break;
            }
        } ?>
    </tr>
<?php } ?>

