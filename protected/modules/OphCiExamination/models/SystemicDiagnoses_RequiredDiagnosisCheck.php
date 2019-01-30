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

namespace OEModule\OphCiExamination\models;

/**
 * Class SystemicDiagnoses_RequiredDiagnosisCheck
 * @package OEModule\OphCiExamination\models
 *
 * @property int $element_id
 * @property int $disorder_id
 * @property int $has_disorder
 *
 * @property SystemicDiagnoses $element
 * @property \Disorder $disorder
 * @property \SecondaryDiagnosisNotPresent $secondary_diagnosis
 */
class SystemicDiagnoses_RequiredDiagnosisCheck extends SystemicDiagnoses_Diagnosis
{

    /**
     * Returns the static model of the specified AR class.
     *
     * @return static
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophciexamination_systemic_diagnoses_req_diag_check';
    }

    public function __toString()
    {
        return '<strong>' . $this->getDisplayHasDisorder() . ':</strong> ' . $this->getDisplayDisorder();
    }

    protected function getSecondaryDiagnosisRelation()
    {
        return array(self::BELONGS_TO, 'SecondaryDiagnosisNotPresent', 'secondary_diagnosis_id');
    }

    protected function getNewSecondaryDiagnosis()
    {
        return new \SecondaryDiagnosisNotPresent();
    }
}