<?php
/**
 * OpenEyes.
 *
 *
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class PcrRisk
{
    protected $stringMap = array(
        'general' => array(
            'NK' => 'Not Known',
            'N' => 'No',
            'Y' => 'Yes',
        ),
        'glaucoma' => array(
            'NK' => 'Not Known',
            'N' => 'No Glaucoma',
            'Y' => 'Glaucoma Present',
        ),
        'diabetes' => array(
            'NK' => 'Not Known',
            'N' => 'No Diabetes',
            'Y' => 'Diabetes Present',
        ),
        'axial' => array(
            '0' => 'Not Known',
            '1' => '< 26',
            '2' => '> or = 26',
        ),
    );

    /**
     * @var Patient
     */
    protected $patient;

    /**
     * @param $age
     * @return int
     */
    protected function getAgeGroup($age)
    {

        if ($age < 60) {
            return 1;
        } elseif ($age < 70) {
            return 2;
        } elseif ($age < 80) {
            return 3;
        } elseif ($age < 90) {
            return 4;
        }
        return 5;
    }

    /**
     * @param $patientId
     * @param $side
     * @param $element
     * @param $set_data was introduced to be able to set back POSTed data when there is an error on the page
     *          definitely needs to refactored
     *
     * @return array
     */
    public function getPCRData($patientId, $side, $element, $set_data = [])
    {
        $pcr = array();
        $this->patient = Patient::model()->findByPk((int) $patientId);

        $eye = Eye::model()->find('LOWER(name) = ?', array(strtolower($side)));
        $pcrRiskValues = new PcrRiskValues();
        if ($eye) {
            $storedValues = PcrRiskValues::model()->findByAttributes(array('eye_id' => $eye->id, 'patient_id' => $patientId));
            if ($storedValues) {
                $pcrRiskValues = $storedValues;
            }
        }

        $ageGroup = isset($set_data['age']) ? $set_data['age'] : $this->getAgeGroup($this->patient->getAge());
        $gender = ucfirst(isset($set_data['gender']) ? $set_data['gender'] : $this->patient->getGenderString());

        if (isset($set_data['PcrRisk'][$side]['diabetic'])){
            $is_diabetic = $set_data['PcrRisk'][$side]['diabetic'];
        } else {
            $is_diabetic = (!is_null($pcrRiskValues->diabetic)) ? $pcrRiskValues->diabetic : 'NK';
            if ($this->patient->getDiabetes()) {
                $is_diabetic = 'Y';
            }
        }

        if ( isset($set_data['PcrRisk'][$side]['glaucoma']) ){
            $is_glaucoma = $set_data['PcrRisk'][$side]['glaucoma'];

        } else{
            $is_glaucoma = (!is_null($pcrRiskValues->glaucoma)) ? $pcrRiskValues->glaucoma : 'NK';
            if (strpos($this->patient->getSdl(), 'glaucoma') !== false) {
                $is_glaucoma = 'Y';
            }
        }

        $user = Yii::app()->session['user'];
        $user_id = $user->id;
        if (strpos(get_class($element), 'OphTrOperationnote') !== false) {
            $operation_note_surgeon_id = $this->getOperationNoteSurgeonId($patientId);
            if ($operation_note_surgeon_id !== 0) {
                $user_id = $operation_note_surgeon_id;
            }
        }

        if ( isset($set_data['PcrRisk'][$side]['pcr_doctor_grade']) ){
            $doctor_grade_id = $set_data['PcrRisk'][$side]['pcr_doctor_grade'];
        } else {
            $doctor_grade_id = $pcrRiskValues->doctor_grade_id;
            if (!$doctor_grade_id){
                $user_data = User::model()->findByPk($user_id);
                $doctor_grade_id = $user_data['originalAttributes']['doctor_grade_id'];
            }
        }
        $pcr['doctor_grade_id'] = $doctor_grade_id;

        $pcr['patient_id'] = $patientId;
        $pcr['side'] = $side;
        $pcr['age_group'] = $ageGroup;
        $pcr['gender'] = $gender;
        $pcr['diabetic'] = $is_diabetic;
        $pcr['glaucoma'] = $is_glaucoma;

        if ( isset($set_data['PcrRisk'][$side]['abletolieflat']) ){
            $pcr['lie_flat'] = $set_data['PcrRisk'][$side]['abletolieflat'];
        } else {
            $pcr['lie_flat'] = ($this->getCannotLieFlat($patientId)) ? $this->getCannotLieFlat($patientId) : $pcrRiskValues->can_lie_flat;
        }


        if ( isset($set_data['PcrRisk'][$side]['no_fundal_view']) ){
            $pcr['noview'] = $set_data['PcrRisk'][$side]['no_fundal_view'];
        } else {
            $no_view = (!is_null($pcrRiskValues->no_fundal_view)) ? $pcrRiskValues->no_fundal_view : 'NK';
            $no_view_data = $this->getOpticDisc($patientId, $side);
            if (count($no_view_data) >= 1) {
                $no_view = 'Y';
            }
            $pcr['noview'] = $no_view;
        }

        if ( \Yii::app()->request->isPostRequest){
            $pcr['anteriorsegment']['pxf_phako'] = isset($set_data['PcrRisk'][$side]['pxf_phako']) ? $set_data['PcrRisk'][$side]['pxf_phako'] : 'NK';
            $pcr['anteriorsegment']['pupil_size'] = isset($set_data['PcrRisk'][$side]['pupil_size']) ? $set_data['PcrRisk'][$side]['pupil_size'] : 'Large';
            $pcr['anteriorsegment']['brunescent_white_cataract'] = isset($set_data['PcrRisk'][$side]['brunescent_white_cataract']) ? $set_data['PcrRisk'][$side]['brunescent_white_cataract'] : 'NK';
        } else {
            $pcr['anteriorsegment'] = $this->getPatientAnteriorSegment($patientId, $side, $pcrRiskValues);
        }

        if (isset($set_data['PcrRisk'][$side]['axial_length'])){
            $pcr['axial_length_group'] = $set_data['PcrRisk'][$side]['axial_length'];
        } else {
            $calculated_axial_length = $this->getAxialLength($patientId, $side);

            $pcr['axial_length_group'] = ($calculated_axial_length !== 'N') ? $calculated_axial_length : $pcrRiskValues->axial_length_group;

            if(empty($pcr['axial_length_group'])) {
                $pcr['axial_length_group'] = '0';
            }
        }

        if (isset($set_data['PcrRisk'][$side]['arb'])){
            $pcr['arb'] = $set_data['PcrRisk'][$side]['arb'];
        }else{
            $pcr['arb'] = ($this->getAlphaBlocker($this->patient)) ? $this->getAlphaBlocker($this->patient) : $pcrRiskValues->alpha_receptor_blocker;
        }

        return $pcr;
    }

    /**
     * @param $patient_id
     *
     * @return string
     */
    protected function getCannotLieFlat($patient_id)
    {
        $cnt = Yii::app()->db->createCommand()
            ->select('count(*) as cnt')
            ->from('patient_risk_assignment as prs')
            ->join('risk as r', 'r.id = prs.risk_id')
            ->where('prs.patient_id=:pid and r.name=:name', array(':pid' => $patient_id, ':name' => 'Cannot Lie Flat'))
            ->queryRow();

        if ($cnt['cnt'] >= 1) {
            $lieflat = 'N';
        } else {
            $lieflat = 'Y';
        }

        return $lieflat;
    }

    /**
     * @param $patient_id
     *
     * @return int
     */
    protected function getOperationNoteSurgeonId($patient_id)
    {
        $surgeon_id = 0;

        $surgeon = Yii::app()->db->createCommand()
            ->select('as.*')
            ->from('episode as ep')
            ->join('event as e', 'e.episode_id = ep.id')
            ->join('et_ophtroperationnote_surgeon as', 'as.event_id = e.id')
            ->where('ep.patient_id=:pid and e.deleted=:del', array(':pid' => $patient_id, ':del' => 0))
            ->order('as.last_modified_date DESC')
            ->limit(1)
            ->queryRow();

        if (isset($surgeon['surgeon_id'])) {
            $surgeon_id = $surgeon['surgeon_id'];
        }

        return $surgeon_id;
    }

    /**
     * @param $patientId
     * @param $side
     * @param int $isAll
     *
     * @return mixed
     */
    public function getOpticDisc($patientId, $side, $isAll = false)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 'event.id, ophciexamination_opticdisc_cd_ratio.name';

        if ($side === 'right') {
            $criteria->join = 'JOIN event ON event.episode_id = t.id
                                JOIN et_ophciexamination_opticdisc ON et_ophciexamination_opticdisc.event_id = event.id
                                JOIN ophciexamination_opticdisc_cd_ratio ON et_ophciexamination_opticdisc.right_cd_ratio_id = ophciexamination_opticdisc_cd_ratio.id';
        } elseif ($side === 'left') {
            $criteria->join = 'JOIN event ON event.episode_id = t.id
                                JOIN et_ophciexamination_opticdisc ON et_ophciexamination_opticdisc.event_id = event.id
                                JOIN ophciexamination_opticdisc_cd_ratio ON et_ophciexamination_opticdisc.left_cd_ratio_id = ophciexamination_opticdisc_cd_ratio.id';
        }

        if ($isAll) {
            $criteria->condition = 't.patient_id = :patient_id and event.deleted=:del';
            $criteria->params = array(':patient_id' => $patientId, ':del' => 0);
        } else {
            $criteria->condition = 't.patient_id = :patient_id and ophciexamination_opticdisc_cd_ratio.name = :name and event.deleted=:del';
            $criteria->params = array(':patient_id' => $patientId, ':name' => 'No view', ':del' => 0);
            $criteria->limit = '1';
        }

        $criteria->order = 'et_ophciexamination_opticdisc.last_modified_date DESC';

        return Episode::model()->findAll($criteria);
    }

    /**
     * @param $patientId
     * @param $side
     *
     * @return mixed
     */
    public function getPatientAnteriorSegment($patientId, $side, PcrRiskValues $storedValues)
    {
        $as['pxf_phako'] = (!is_null($storedValues->pxf)) ? $storedValues->pxf : 'NK';
        $as['pxe'] = null;
        $as['phakodonesis'] = null;
        $as['pupil_size'] = (!is_null($storedValues->pupil_size)) ? $storedValues->pupil_size : 'Large';
        $as['brunescent_white_cataract'] = (!is_null($storedValues->brunescent_white_cataract)) ? $storedValues->brunescent_white_cataract : 'NK';
        $as['pxf_phako_nk'] = 0;
        $anteriorsegment = Yii::app()->db->createCommand()
            ->select('as.*')
            ->from('episode as ep')
            ->join('event as e', 'e.episode_id = ep.id')
            ->join('et_ophciexamination_anteriorsegment as', 'as.event_id = e.id')
            ->where('ep.patient_id=:pid and e.deleted=:del', array(':pid' => $patientId, ':del' => 0))
            ->order('as.last_modified_date DESC')
            ->limit(1)
            ->queryRow();

        if ($anteriorsegment) {
            $eyedraw = json_decode($anteriorsegment["{$side}_eyedraw"], true);
            $as['nuclear_id'] = $anteriorsegment["{$side}_nuclear_id"];
            $as['cortical_id'] = $anteriorsegment["{$side}_cortical_id"];
            $as['phakodonesis'] = $anteriorsegment["{$side}_phako"];
        }

        if (isset($eyedraw) && is_array($eyedraw)) {
            foreach ($eyedraw as $val) {
                if (!empty($val['pupilSize'])) {
                    $as['pupil_size'] = $val['pupilSize'];
                }

                if (!empty($val['pxe'])) {
                    $as['pxe'] = $val['pxe'];
                }
            }
        }

        if (($as['phakodonesis']) || ($as['pxe'])) {
            $as['pxf_phako'] = 'Y';
        }

        if (is_null($as['phakodonesis']) && is_null($as['pxe'])) {
            $as['pxf_phako_nk'] = 1;
        }

        if (isset($as['nuclear_id']) && $as['nuclear_id'] === "4" || isset($as['cortical_id']) && $as['cortical_id'] === "4") {
            $as['brunescent_white_cataract'] = 'Y';
        }

        return $as;
    }

    /**
     * @param $patientId
     * @param $side
     *
     * @return int|string
     */
    public function getAxialLength($patientId, $side)
    {
        if (Yii::app()->db->schema->getTable('et_ophinbiometry_measurement', true) === null) {
            $axial_length_group = 'N';
        } else {
            $axial_length_group = 'N';
            $biometry_measurement = Yii::app()->db->createCommand()
                ->select('om.*')
                ->from('episode as ep')
                ->join('event as e', 'e.episode_id = ep.id')
                ->join('et_ophinbiometry_measurement as om', 'om.event_id = e.id')
                ->where('ep.patient_id=:pid and e.deleted=:del', array(':pid' => $patientId, ':del' => 0))
                ->order('om.last_modified_date DESC')
                ->limit(1)
                ->queryRow();

            $axial_length = 0;

            if ($biometry_measurement) {
                if (($biometry_measurement['eye_id'] === (string) Eye::getIdFromName($side) || $biometry_measurement['eye_id'] === (string) Eye::BOTH)) {
                    $axial_length = $biometry_measurement["axial_length_{$side}"];
                }
            }

            if ($axial_length > 0) {
                if ($axial_length >= 26) {
                    $axial_length_group = 2;
                } else {
                    $axial_length_group = 1;
                }
            }
        }

        return $axial_length_group;
    }

    /**
     * @param Patient $patient
     *
     * @return string
     */
    protected function getAlphaBlocker(Patient $patient)
    {
        $exam_api = Yii::app()->moduleAPI->get('OphCiExamination');
        if ($exam_api){
            $alphaBlocker = $exam_api->getRiskByName($patient, 'Alpha blockers');
        }
        if (!$alphaBlocker || $alphaBlocker['status'] === null) {
            return 'NK';
        } elseif ($alphaBlocker['status'] === true) {
            return 'Y';
        } else {
            return 'N';
        }
    }

    /**
     * @param $side
     * @param Patient $patient
     * @param array   $data
     *
     * @throws CException
     * @throws Exception
     */
    public function persist($side, Patient $patient, $data = array())
    {
        if (!$side) {
            throw new CException('No Side provided');
        }

        $eye = Eye::model()->find('LOWER(name) = ?', array(strtolower($side)));

        if (!$eye) {
            throw new CException('Cannot find eye');
        }

        $pcrRiskValues = new PcrRiskValues();
        $pcrRiskValues->patient_id = $patient->id;
        $pcrRiskValues->eye_id = $eye->id;

        $existing = PcrRiskValues::model()->findByAttributes($pcrRiskValues->getAttributes(array('eye_id', 'patient_id')));
        if ($existing) {
            $pcrRiskValues = $existing;
        }

        $pcrRiskValues->glaucoma = (isset($data['glaucoma']) && $data['glaucoma'] !== 'NK') ? $data['glaucoma'] : null;
        $pcrRiskValues->pxf = (isset($data['pxf_phako']) && $data['pxf_phako'] !== 'NK') ? $data['pxf_phako'] : null;
        $pcrRiskValues->diabetic = (isset($data['diabetic']) && $data['diabetic'] !== 'NK') ? $data['diabetic'] : null;
        $pcrRiskValues->pupil_size = (isset($data['pupil_size']) && $data['pupil_size'] !== 'NK') ? $data['pupil_size'] : null;
        $pcrRiskValues->no_fundal_view = (isset($data['no_fundal_view']) && $data['no_fundal_view'] !== 'NK') ? $data['no_fundal_view'] : null;
        $pcrRiskValues->axial_length_group = (isset($data['axial_length']) && $data['axial_length'] !== 'NK') ? $data['axial_length'] : null;
        $pcrRiskValues->brunescent_white_cataract = (isset($data['brunescent_white_cataract']) && $data['brunescent_white_cataract'] !== 'NK') ? $data['brunescent_white_cataract'] : null;
        $pcrRiskValues->alpha_receptor_blocker = (isset($data['arb']) && $data['arb'] !== 'NK') ? $data['arb'] : null;
        $pcrRiskValues->doctor_grade_id = (isset($data['doctor_grade_id']) && $data['doctor_grade_id'] !== '') ? $data['doctor_grade_id'] : null;
        $pcrRiskValues->can_lie_flat = (isset($data['abletolieflat']) && $data['abletolieflat'] !== 'NK') ? $data['abletolieflat'] : null;

        if (!$pcrRiskValues->save()) {
            throw new CException('PCR Risk failed to save');
        }
    }

    /**
     * @param string $value
     * @param string $type
     * @return string
     */
    public function displayValues($value, $type = 'general')
    {
        if (!array_key_exists($type, $this->stringMap)){
            $type = 'general';
        }

        if (array_key_exists($value, $this->stringMap[$type])){
            return $this->stringMap[$type][$value];
        }

        return $value;
    }
}
