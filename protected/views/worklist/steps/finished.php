<?php

/**
 * @var $pathway Pathway
 * @var $patient Patient
 */
?>
<div class="slide-open">
    <div class="patient"><?= strtoupper($patient->last_name) . ', ' . $patient->first_name . ' (' . $patient->title . ')'?></div>
    <h3 class="title">Pathway completed</h3>
    <div class="step-content">
        <table>
            <colgroup>
                <col class="cols-3">
                <col class="cols-3">
                <col class="cols-6">
            </colgroup>
            <thead>
            <tr>
                <th>State</th>
                <th>Time</th>
                <th>Person</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Confirmed</td>
                <td>at <?= $pathway->end_time ? DateTime::createFromFormat('Y-m-d H:i:s', $pathway->end_time)->format('H:i') : date('H:i') ?></td>
                <td>
                    <?= $pathway->lastModifiedUser->getFullNameAndTitle() ?>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
