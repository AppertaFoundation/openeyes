<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php
    $patient_overview_popup_mode = isset(Yii::app()->controller->jsVars['popupMode']) ? Yii::app()->controller->jsVars['popupMode'] : SettingMetadata::model()->getSetting('patient_overview_popup_mode');
foreach ($patients->getData() as $SearchPatient) {
    if ($page === 'caseSearch') {
        $data = $SearchPatient;
    } elseif ($page === 'trial' || 'worklist') {
        $data = $SearchPatient->patient;
    }
    $patientSummaryPopup = $this->createWidget(
        'application.widgets.PatientSummaryPopup',
        array(
            'patient' => $data,
        )
    );
    if ($patient_overview_popup_mode=='side') {
        $patientSummaryPopup->render('application.widgets.views.PatientSummaryPopup'.($page==='worklist'?'Worklist':'').'Side', []);
    } elseif ($patient_overview_popup_mode=='float') {
        $patientSummaryPopup->render('application.widgets.views.PatientSummaryPopup'.($page==='worklist'?'Worklist':'Multi').'Float', []);
    }
    ?>
        <script type="application/javascript">
            $(function () {
                PatientPanel.patientPopups.init(false ,<?= $data->id?>);
            });
        </script>
    <?php
}
    $assetManager = Yii::app()->getAssetManager();
    $widgetPath = $assetManager->publish('protected/widgets/js');
    Yii::app()->clientScript->registerScriptFile($widgetPath . '/PatientPanelPopupMulti.js');
?>
<script type="application/javascript">
    let allPopupBtns={};
    $('body').on('click', '.js-patient-expand-btn', function () {
        $(this).toggleClass('collapse expand');
        $(this).parents('table').find('tbody').toggle();
    });
</script>
