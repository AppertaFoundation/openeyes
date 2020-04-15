<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Class PatientSummaryPopup
 * @property Patient $patient
 */
class PatientSummaryPopup extends BaseCWidget
{
    public $patient;

    public static $LIST_SEPARATOR = '<br/>';

    protected $warnings;
    protected $ophthalmicDiagnoses;
    protected $systemicDiagnoses;
    protected $cviStatus;
    protected $medications;
    protected $allergies;
    protected $operations;
    protected $family_history;
    protected $social_history;
    protected $referredTo;

    public function init()
    {
        // NOTE: we should be registering the widget package here, but as we don't
        // have core assets defined as a clientside package, we have to manually publish
        // the widget script to ensure the script tag is output below the core scripts.
        // (This is done in BaseCWidget);
        // Yii::app()->clientScript->registerPackage('patientSummaryPopup');

        $this->cviStatus = $this->patient->getCviSummary();

        $this->warnings = $this->patient->getWarnings(
            Yii::app()->user->checkAccess('OprnViewClinical')
        );

        $this->ophthalmicDiagnoses = implode(
            self::$LIST_SEPARATOR,
            $this->patient->ophthalmicDiagnosesSummary
        );

        $this->systemicDiagnoses = implode(
            self::$LIST_SEPARATOR,
            $this->patient->systemicDiagnosesSummary
        );

        $this->allergies = implode(
            self::$LIST_SEPARATOR,
            $this->patient->allergiesSummary
        );

        $family_history_model = new \OEModule\OphCiExamination\models\FamilyHistory();
        $this->family_history = $family_history_model->getMostRecentForPatient($this->patient);

        $social_history_model = new \OEModule\OphCiExamination\models\SocialHistory();
        $this->social_history = $social_history_model->getMostRecentForPatient($this->patient);

        $referred_user = PatientUserReferral::model()->findByAttributes(array('patient_id' => $this->patient->id));
        $this->referredTo = isset($referred_user) ? $referred_user->user : null;

        $widget = $this->createWidget('OEModule\OphCiExamination\widgets\PastSurgery', array(
            'patient' => $this->patient,
            'mode' => BaseEventElementWidget::$PATIENT_POPUP_MODE,
            'popupListSeparator' => self::$LIST_SEPARATOR,
        ));
        $this->operations = $widget->run();

        parent::init();
    }
}
