<?php
/* @var $this PatientController */
$current_trial_flag = 1;
?>


<div class="quick-overview-content">

    <?php
    /* @var TrialPatient $trialPatient */
    foreach ($this->patient->trials as $trialPatient) : //
        if ( $current_trial_flag == 1 ) {
            $current_trial_flag = 0 ?>
        <div class="data-group">
            <h3>Current Trials</h3>
            <table class="patient-trials">
                <tbody>
                    <tr>
                        <td>Trial</td>
                        <td><?php if (Yii::app()->user->checkAccess('TaskViewTrial')) {
                                echo CHtml::link(CHtml::encode($trialPatient->trial->name),
                                Yii::app()->controller->createUrl('/OETrial/trial/permissions',
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
                </tbody>
            </table>
        </div>
        <?php } else {?>
            <div class="data-group">
                <h3>Past Trials</h3>
                <table class="patient-trials">
                    <tbody>
                        <tr>
                            <td>Trial</td>
                            <td><?php if (Yii::app()->user->checkAccess('TaskViewTrial')) {
                                echo CHtml::link(CHtml::encode($trialPatient->trial->name),
                                  Yii::app()->controller->createUrl('/OETrial/trial/permissions',
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
                    </tbody>
                </table>
            </div>
        <?php } ?>
    <?php endforeach; ?>
</div>
