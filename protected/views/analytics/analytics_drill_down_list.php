<?php $coreAPI = new CoreAPI();?>
<div class="analytics-patient-list" style="display: none; margin-right: 500px; " >
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
            <th>Hospital No</th>
            <th>Gender</th>
            <th>Age</th>
            <th>Name</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($patient_list as $patient) { ?>
            <tr id="<?=$patient->id?>" class="analytics-patient-list-row clickable" data-link="<?=$coreAPI->generateEpisodeLink($patient)?>" style="display: none">
                <td><?= $patient->hos_num; ?></td>
                <td><?= $patient->gender; ?></td>
                <td><?= $patient->getAge(); ?></td>
                <td><?= $patient->getFullName(); ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
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