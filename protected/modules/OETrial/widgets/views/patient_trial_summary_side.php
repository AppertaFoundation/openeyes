<?php
/* @var $this PatientController */
$current_trial_flag = 1;
?>


<div class="quick-overview-content">

    <?php
    $can_view_trial = Yii::app()->user->checkAccess('TaskViewTrial');
    $current_trials = array();
    $past_trials = array();

    /* @var TrialPatient $trialPatient */
    foreach ($this->patient->trials as $trialPatient) {
        if (!empty($trialPatient->trial->closed_date)) {
            $past_trials[] = $trialPatient;
        } else {
            $current_trials[] = $trialPatient;
        }
    }

    ?>
    <div class="data-group">
        <h3>Current Trials</h3>
        <table class="patient-trials">
            <tbody>
                <?php foreach ($current_trials as $trialPatient): ?>
                <tr>
                    <td>Trial</td>
                    <td><?php if ($can_view_trial) {
                            echo CHtml::link(CHtml::encode($trialPatient->trial->name),
                            Yii::app()->controller->createUrl('/OETrial/trial/view',
                                array('id' => $trialPatient->trial_id)));
                        } else {
                            echo CHtml::encode($trialPatient->trial->name);
                        } ?>
                    </td>
                </tr>
                <tr>
                    <td>Date</td>
                    <td><?= $trialPatient->trial->getStartedDateForDisplay().' - '.$trialPatient->trial->getClosedDateForDisplay(); ?></td>
                </tr>
                <tr>
                    <td>Study Coordinator</td>
                    <td>
                        <?php
                        $coordinators = $trialPatient->trial->getTrialStudyCoordinators();
                        if (sizeof($coordinators)) {
                            foreach ($coordinators as $item) {
                                echo $item->user->getFullName().'<br>';
                            }
                        } else {
                            echo 'N/A';
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>Treatment</td>
                    <td><?= $trialPatient->treatmentType->name; ?></td>
                </tr>
                <tr>
                    <td>Trial Type</td>
                    <td><?= $trialPatient->trial->trialType->name; ?></td>
                </tr>
                <tr>
                    <td>Trial Status</td>
                    <td><?= $trialPatient->status->name; ?></td>
                </tr>
                <tr class="divider"></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="data-group">
        <h3>Past Trials</h3>
        <table class="patient-trials">
            <tbody>
                <?php foreach ($past_trials as $trialPatient): ?>
                <tr>
                    <td>Trial</td>
                    <td><?php if ($can_view_trial) {
                        echo CHtml::link(CHtml::encode($trialPatient->trial->name),
                          Yii::app()->controller->createUrl('/OETrial/trial/view',
                            array('id' => $trialPatient->trial_id)));
                        } else {
                            echo CHtml::encode($trialPatient->trial->name);
                        } ?>
                    </td>
                </tr>
                <tr>
                    <td>Date</td>
                    <td><?= $trialPatient->trial->getStartedDateForDisplay().' - '.$trialPatient->trial->getClosedDateForDisplay(); ?></td>
                </tr>
                <tr>
                    <td>Study Coordinator</td>
                    <td>
                        <?php
                        $coordinators = $trialPatient->trial->getTrialStudyCoordinators();
                        if (sizeof($coordinators)) {
                            foreach ($coordinators as $item) {
                                echo $item->user->getFullName().'<br>';
                            }
                        } else {
                            echo 'N/A';
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>Treatment</td>
                    <td><?= $trialPatient->treatmentType->name; ?></td>
                </tr>
                <tr>
                    <td>Trial Type</td>
                    <td><?= $trialPatient->trial->trialType->name; ?></td>
                </tr>
                <tr>
                    <td>Trial Status</td>
                    <td><?= $trialPatient->status->name; ?></td>
                </tr>
                <tr class="divider"></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
