<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * This is the model class for table "patient_pedigree".
 *
 * The followings are the available columns in table 'patient_pedigree':
 *
 * @property int $id
 *
 * The followings are the available model relations:
 */
class GeneticsPatient extends BaseActiveRecord
{
    protected $auto_update_relations = true;

    protected $statuses = array();

    protected $preExistingPedigreesIds = array();
    
    var $pedigree_id;
    /**
     * Returns the static model of the specified AR class.
     *
     * @return Issue the static model class
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
        return 'genetics_patient';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('studies', 'isProposable'),
            array('patient_id', 'unique', 'on'=>'insert', 'message'=>'This Patient subject is already exists!'),
            array('patient_id, comments, gender_id, is_deceased, relationships, studies, pedigrees, diagnoses', 'safe'),
        );
    }

    /**
     * Checks if it's possible for the user to propose this patient for the study.
     *
     * @param $attribute
     * @param $params
     */
    public function isProposable($attribute, $params)
    {
        if ($this->isAttributeDirty('studies')) {
            $existing = GeneticsStudy::model()->participatingStudyIds($this);
            if($this->studies){
                foreach ($this->studies as $study) {
                    if (in_array($study->id, $existing, true)) {
                        continue;
                    }
                    //New study has been added, make sure that it's possible for the user to propose this.
                    if(!$study->canBeProposedByUser(Yii::app()->user)){
                        $this->addError($attribute, 'You do not have permission to propose subjects for ' . $study->name);
                    }
                }
            }
        }
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        //Was unable to join on the pivot table as any joins added with the relationship are inserted in to
        //the query string before the join generated for the relationship, so selecting status ID here and
        //inserting it in to condition manually.
        if (!array_key_exists('Rejected', $this->statuses)) {
            $statuses = StudyParticipationStatus::model()->findAll();
            foreach ($statuses as $status) {
                $this->statuses[$status->status] = $status->id;
            }
        }

        return array(
            'patient' => array(self::BELONGS_TO, 'Patient', 'patient_id'),
            'gender' => array(self::BELONGS_TO, 'Gender', 'gender_id'),
            'relationships' => array(self::HAS_MANY, 'GeneticsPatientRelationship', 'patient_id'),
            'studies' => array(self::MANY_MANY, 'GeneticsStudy', 'genetics_study_subject(subject_id, study_id)'),
            'previous_studies' => array(
                self::MANY_MANY,
                'GeneticsStudy',
                'genetics_study_subject(subject_id, study_id)',
                'condition' => 'end_date < NOW() ' .
                    'AND (previous_studies_previous_studies.participation_status_id IS NULL ' .
                    'OR previous_studies_previous_studies.participation_status_id <> ' . $this->statuses['Rejected'] . ')',
            ),
            'current_studies' => array(
                self::MANY_MANY,
                'GeneticsStudy',
                'genetics_study_subject(subject_id, study_id)',
                'condition' => 'end_date > NOW() ' .
                    'AND (current_studies_current_studies.participation_status_id IS NULL ' .
                    'OR current_studies_current_studies.participation_status_id <> ' . $this->statuses['Rejected'] . ')',
            ),
            'rejected_studies' => array(
                self::MANY_MANY,
                'GeneticsStudy',
                'genetics_study_subject(subject_id, study_id)',
                'condition' => 'rejected_studies_rejected_studies.participation_status_id = ' . $this->statuses['Rejected'],
            ),
            'pedigrees' => array(
                self::MANY_MANY,
                'Pedigree',
                'genetics_patient_pedigree(patient_id, pedigree_id)',
            ),
            'diagnoses' => array(
                self::MANY_MANY,
                'Disorder',
                'genetics_patient_diagnosis(patient_id, disorder_id)',
            ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'Subject ID',
            'patient_id' => 'Patient',
            'gender_id' => 'Karyotypic Sex',
            'is_deceased' => 'Is Deceased',
        );
    }

    /**
     * Set the pedigrees that exist on load to compare to when saved.
     */
    protected function afterFind()
    {
        parent::afterFind();
        foreach($this->pedigrees as $pedigree) {
            $this->preExistingPedigreesIds[] = $pedigree->attributes['id'];
        }
    }
    
    /**
     * Update the pedigrees this patient has been added to.
     */
    protected function afterSave()
    {
        parent::afterSave();

        if($this->getIsNewRecord()) {
            $this->updateDiagnoses();
            $this->updatePedigrees();
        }

        $pedigrees = GeneticsPatientPedigree::model()->findAllByAttributes(array('patient_id' => $this->id), array('select' =>  'pedigree_id'));
        $pedigreeIds = array();
        foreach($pedigrees as $pedigree) {
            $pedigreeIds[] = $pedigree->attributes['pedigree_id'];
        }

        $added = array_diff($this->preExistingPedigreesIds, $pedigreeIds);
        $deleted = array_diff($pedigreeIds, $this->preExistingPedigreesIds);

        $difference = Pedigree::model()->findAllByPk(array_merge($added, $deleted));

        foreach ($difference as $pedigree) {
            $pedigree->updateDiagnosis();
        }
    }

    /**
     * Should only be called for a new genetic patient record as it doesn't check for duplication or anything along
     * those lines.
     */
    protected function updateDiagnoses()
    {
        $diagnoses = $this->patient->getAllDisorders();

        foreach($diagnoses as $diagnosis) {
            $geneticsDiagnosis = new GeneticsPatientDiagnosis();
            $geneticsDiagnosis->patient_id = $this->id;
            $geneticsDiagnosis->disorder_id = $diagnosis->id;
            $geneticsDiagnosis->save();
        }
    }
    
    protected function updatePedigrees()
    {
        $geneticsPatientPedigree = new GeneticsPatientPedigree();
        $geneticsPatientPedigree->patient_id = $this->id;
        $geneticsPatientPedigree->pedigree_id = $this->pedigree_id;
        $geneticsPatientPedigree->save();
    }

    /**
     * @param int $pedigree_id
     *
     * @return string
     */
    public function statusForPedigree($pedigree_id)
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'pedigree_id = :pedigree_id AND patient_id = :patient_id';
        $criteria->params = array(
            'pedigree_id' => $pedigree_id,
            'patient_id' => $this->id,
        );

        $patientPedigree = GeneticsPatientPedigree::model()->find($criteria);

        if(!$patientPedigree || !$patientPedigree->status) {
            return 'Uknown';
        }

        return $patientPedigree->status->name;
    }
}
