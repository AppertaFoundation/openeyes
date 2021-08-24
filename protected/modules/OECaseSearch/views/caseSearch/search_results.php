<?php
/* @var $data Patient
 * @var $this CaseSearchController
 * @var $trialPatient TrialPatient
 */

$navIconsUrl = Yii::app()->assetManager->getPublishedUrl(Yii::getPathOfAlias('application.assets.newblue'), true) . '/dist/svg/oe-nav-icons.svg';
$warnings = array();
foreach ($data->getWarnings() as $warn) {
    $warnings[] = "{$warn['long_msg']}: {$warn['details']}";
}
$data->hasAllergyStatus();
?>
<tr>
    <?php
    /** @var $patientPanel PatientPanel */
    $patientPanel = $this->createWidget(
        'application.widgets.PatientPanel',
        array(
            'patient' => $data,
            'layout' => 'list',
            'trial' => $this->trialContext,
            'list_mode' => true,
            'selected_site_id' => $this->selectedSiteId,
        )
    );
    $patientPanel->render('PatientPanel');
    ?>
        ?>
</tr>
