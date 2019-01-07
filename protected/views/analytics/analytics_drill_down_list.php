<div class="analytics-patient-list" style="display: none">
    <div class="flex-layout">
        <h3 id="js-list-title"></h3>
        <a id="js-back-to-chart" class="selected" href="#">Back to chart</a>
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
            <tr>
                <td><?= $patient['hospital_number']; ?></td>
                <td><?= $patient['gender']; ?></td>
                <td><?= $patient['age']; ?></td>
                <td><?= $patient['name']; ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>