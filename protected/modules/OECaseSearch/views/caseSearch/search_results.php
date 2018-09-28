<?php
/* @var $data Patient
 * @var $this CaseSearchController
 * @var $trialPatient TrialPatient
 */

$navIconsUrl = Yii::app()->assetManager->getPublishedUrl(Yii::getPathOfAlias('application.assets.newblue')) . '/svg/oe-nav-icons.svg';
$warnings = array();
foreach ($data->getWarnings(true) as $warn) {
    $warnings[] = "{$warn['long_msg']}: {$warn['details']}";
}
$data->hasAllergyStatus();
?>
<tr>
    <td>
        <?php
        /** @var $patientPanel PatientPanel */
        $patientPanel = $this->createWidget('application.widgets.PatientPanel',
            array(
                'patient' => $data,
                'layout' => 'list',
                'trial' => $this->trialContext,
                'list_mode' => true,
            )
        );
        $patientPanel->render('PatientPanel');
        ?>
    </td>
</tr>
