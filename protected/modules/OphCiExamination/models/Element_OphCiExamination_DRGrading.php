<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;

use Yii;

/**
 * This is the model class for table "et_ophciexamination_drgrading".
 *
 * NOTE that this element provides the facility to set a patient secondary diagnosis for the diabetic type. To enable
 * support for deleting it, we record the id of the SecondaryDiagnosis it creates, as well as the type. A foreign key
 * constraint is not enforced to allow the SecondaryDiagnosis to be deleted as normal through the Patient view.
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property int $eye_id
 * @property int $secondarydiagnosis_id
 * @property int $secondarydiagnosis_disorder_id
 * @property string $left_nscretinopathy_id
 * @property string $left_nscmaculopathy_id
 * @property string $right_nscretionopathy_id
 * @property string $right_nscmaculopathy_id
 * @property bool $left_nscretinopathy_photocoagulation
 * @property bool $left_nscmaculopathy_photocoagulation
 * @property bool $right_nscretinopathy_photocoagulation
 * @property bool $right_nscmaculopathy_photocoagulation
 * @property int $left_clinicalret_id
 * @property int $right_clinicalret_id
 * @property int $left_clinicalmac_id
 * @property int $right_clinicalmac_id
 * The followings are the available model relations:
 * @property OphCiExamination_NSCRetinopathy $left_nscretinopathy
 * @property OphCiExamination_NSCRetinopathy $right_nscretinopathy
 * @property OphCiExamination_NSCMaculopathy $left_nscmaculopathy
 * @property OphCiExamination_NSCMaculopathy $right_nscmaculopathy
 * @property OphCiExamination_ClinicalRetinopathy $left_clinicalret
 * @property OphCiExamination_ClinicalRetinopathy $right_clinicalret
 * @property OphCiExamination_ClinicalMaculopathy $left_clinicalmac
 * @property OphCiExamination_ClinicalMaculopathy $right_clinicalretmac
 */
class Element_OphCiExamination_DRGrading extends \SplitEventTypeElement
{
    use traits\CustomOrdering;
    public $service;
    public $secondarydiagnosis_disorder_required = false;

    /**
     * Returns the static model of the specified AR class.
     *
     * @param string $className
     *
     * @return the static model class
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
        return 'et_ophciexamination_drgrading';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
                array('left_nscretinopathy_id, left_nscmaculopathy_id, left_nscretinopathy_photocoagulation,
						left_nscmaculopathy_photocoagulation, right_nscretinopathy_id, right_nscmaculopathy_id,
						right_nscretinopathy_photocoagulation, right_nscmaculopathy_photocoagulation, left_clinicalret_id,
						right_clinicalret_id, left_clinicalmac_id, right_clinicalmac_id, eye_id', 'safe'),
                array('secondarydiagnosis_disorder_id', 'flagRequired', 'flag' => 'secondarydiagnosis_disorder_required'),
                array('left_nscretinopathy_id, left_nscmaculopathy_id, left_nscretinopathy_photocoagulation,
						left_nscmaculopathy_photocoagulation, left_clinicalmac_id', 'requiredIfSide', 'side' => 'left'),
                array('right_nscretinopathy_id, right_nscmaculopathy_id, right_nscretinopathy_photocoagulation,
						right_nscmaculopathy_photocoagulation, right_clinicalmac_id', 'requiredIfSide', 'side' => 'right'),
                // The following rule is used by search().
                array('event_id, left_nscretinopathy_id, left_nscmaculopathy_id, left_nscretionopathy_photocoagulation,
						left_nscmaculopathy_photocoagulation, right_nscretinopathy_id, right_nscmaculopathy_id,
						right_nscretinopathy_photocoagulation, right_nscmaculopathy_photocoagulation, left_clinicalret_id,
						right_clinicalret_id, left_clinicalmac_id, right_clinicalmac_id, eye_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array
     *               (non-phpdoc)
     *
     * @see parent::sidedFields()
     */
    public function sidedFields()
    {
        return array('nscretinopathy_id', 'nscmaculopathy_id', 'nscretinopathy_photocoagulation', 'nscmaculopathy_photocoagulation', 'clinicalret_id', 'clinicalmac_id');
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
                'element_type' => array(self::HAS_ONE, '\ElementType', 'id', 'on' => "element_type.class_name='".get_class($this)."'"),
                'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
                'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
                'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
                'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
                'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
                'left_nscretinopathy' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_DRGrading_NSCRetinopathy', 'left_nscretinopathy_id'),
                'left_nscmaculopathy' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_DRGrading_NSCMaculopathy', 'left_nscmaculopathy_id'),
                'left_clinicalret' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_DRGrading_ClinicalRetinopathy', 'left_clinicalret_id'),
                'left_clinicalmac' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_DRGrading_ClinicalMaculopathy', 'left_clinicalmac_id'),
                'right_nscretinopathy' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_DRGrading_NSCRetinopathy', 'right_nscretinopathy_id'),
                'right_nscmaculopathy' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_DRGrading_NSCMaculopathy', 'right_nscmaculopathy_id'),
                'right_clinicalret' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_DRGrading_ClinicalRetinopathy', 'right_clinicalret_id'),
                'right_clinicalmac' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_DRGrading_ClinicalMaculopathy', 'right_clinicalmac_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
                'id' => 'ID',
                'event_id' => 'Event',
                'secondarydiagnosis_disorder_id' => 'Diabetes type',
                'left_nscretinopathy_id' => 'NSC retinopathy',
                'left_nscmaculopathy_id' => 'NSC maculopathy',
                'right_nscretinopathy_id' => 'NSC retinopathy',
                'right_nscmaculopathy_id' => 'NSC maculopathy',
                'left_nscretinopathy_photocoagulation' => 'Panretinal photocoagulation',
                'left_nscmaculopathy_photocoagulation' => 'Maculopathy photocoagulation scars',
                'right_nscretinopathy_photocoagulation' => 'Panretinal photocoagulation',
                'right_nscmaculopathy_photocoagulation' => 'Maculopathy photocoagulation scars',
                'left_clinicalret_id' => 'Clinical Grading for retinopathy',
                'right_clinicalret_id' => 'Clinical Grading for retinopathy',
                'left_clinicalmac_id' => 'Clinical Grading for maculopathy',
                'right_clinicalmac_id' => 'Clinical Grading for maculopathy',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new \CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('event_id', $this->event_id, true);

        $criteria->compare('left_retinopathy_id', $this->left_retinopathy_id);
        $criteria->compare('left_maculopathy_id', $this->left_maculopathy_id);
        $criteria->compare('left_clinicalret_id', $this->left_clinicalret_id);
        $criteria->compare('left_clinicalmac_id', $this->left_clinicalmac_id);
        $criteria->compare('right_retinopathy_id', $this->right_retinopathy_id);
        $criteria->compare('right_maculopathy_id', $this->right_maculopathy_id);
        $criteria->compare('right_clinicalret_id', $this->right_clinicalret_id);
        $criteria->compare('right_clinicalmac_id', $this->right_clinicalmac_id);

        $criteria->compare('left_nscretinopathy_photocoagulation', $this->left_nscretinopathy_photocoagulation);
        $criteria->compare('left_nscmaculopathy_photocoagulation', $this->left_nscmaculopathy_photocoagulation);
        $criteria->compare('right_nscretinopathy_photocoagulation', $this->right_nscretinopathy_photocoagulation);
        $criteria->compare('right_nscmaculopathy_photocoagulation', $this->right_nscmaculopathy_photocoagulation);

        return new \CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }

    public function getDiabetesTypes()
    {
        return array(
            \Disorder::SNOMED_DIABETES_TYPE_I => \Disorder::model()->findByPk(\Disorder::SNOMED_DIABETES_TYPE_I)->term,
            \Disorder::SNOMED_DIABETES_TYPE_II => \Disorder::model()->findByPk(\Disorder::SNOMED_DIABETES_TYPE_II)->term,
        );
    }

    /**
     * Because the secondary diagnosis may or may not exist we have a function to check for it.
     *
     * @return SecondaryDiagnosis|null
     */
    protected function _getSecondaryDiagnosis()
    {
        if ($this->secondarydiagnosis_id) {
            return \SecondaryDiagnosis::model()->findByPk($this->secondarydiagnosis_id);
        }

        return;
    }

    /**
     * if a secondary diagnosis disorder id has been set, we need to ensure its created on the patient.
     *
     * @see parent::beforeSave()
     */
    public function beforeSave()
    {
        $curr_sd = $this->_getSecondaryDiagnosis();

        if ($this->secondarydiagnosis_disorder_id
            && $curr_sd
            && $curr_sd->disorder_id != $this->secondarydiagnosis_disorder_id) {
            // looks like this is an edit and the previous secondary diagnosis should be removed
            // so we can set the correct value
            $curr_disorder = $curr_sd->disorder;
            $curr_sd->delete();
            $curr_sd = null;
            Yii::app()->user->setFlash('warning.alert', "Disorder '".$curr_disorder->term."' has been removed because DR Grading diagnosis was updated.");
        }

        if (!$curr_sd) {
            // need to determine if we are setting a specific disorder on the patient, or a generic diabetes
            // diagnosis (which is implied by recording DR)
            $patient = $this->event->episode->patient;
            $sd = null;

            if ($this->secondarydiagnosis_disorder_id) {
                // no secondary diagnosis has been set by this element yet but one has been
                // assigned (i.e. the element is being created with a diabetes type)

                // final check to ensure nothing has changed whilst processing
                if (!$patient->hasDisorderTypeByIds(array_merge(\Disorder::$SNOMED_DIABETES_TYPE_I_SET, \Disorder::$SNOMED_DIABETES_TYPE_II_SET))) {
                    $sd = new \SecondaryDiagnosis();
                    $sd->patient_id = $patient->id;
                    $sd->disorder_id = $this->secondarydiagnosis_disorder_id;
                } else {
                    // clear out the secondarydiagnosis_disorder_id
                    $this->secondarydiagnosis_disorder_id = null;
                    // reset required flag as patient now has a diabetes type
                    $this->secondarydiagnosis_disorder_required = false;
                }
            } elseif (!$patient->hasDisorderTypeByIds(\Disorder::$SNOMED_DIABETES_SET)) {
                // Set the patient to have diabetes
                $sd = new \SecondaryDiagnosis();
                $sd->patient_id = $patient->id;
                $sd->disorder_id = \Disorder::SNOMED_DIABETES;
            }

            if ($sd !== null) {
                $sd->date = $sd->isNewRecord ? date('Y-m-d') : $sd->date;
                $sd->save();
                \Audit::add('SecondaryDiagnosis', 'add', $sd->id, null, array('patient_id' => $patient->id));
                $this->secondarydiagnosis_id = $sd->id;
                Yii::app()->user->setFlash('info.info', "Disorder '".$sd->disorder->term."' has been added to patient by DR Grading.");
            }
        }

        return parent::beforeSave();
    }

    /**
     * if this element is linked to a secondary diagnosis that still exists, it will be removed.
     */
    protected function cleanUpSecondaryDiagnosis()
    {
        if ($sd = $this->_getSecondaryDiagnosis()) {
            $disorder = $sd->disorder;
            $audit_data = serialize($sd->attributes);
            $sd->delete();
            \Audit::add('SecondaryDiagnosis', 'delete', $sd->id, null, array('patient_id' => $sd->patient_id));
            Yii::app()->user->setFlash('warning.alert', "Disorder '".$disorder->term."' has been removed because DR Grading was deleted.");
        }
    }

    /**
     * (non-phpdoc).
     *
     * @see cleanUpSecondaryDiagnosis()
     * @see parent::softDelete()
     */
    public function softDelete()
    {
        $this->cleanUpSecondaryDiagnosis();

        return parent::softDelete();
    }
    /**
     * @see cleanUpSecondaryDiagnosis()
     * @see parent::delete()
     *
     * @return bool
     */
    public function delete()
    {
        $this->cleanUpSecondaryDiagnosis();

        return parent::delete();
    }

    /**
     * validator that requires the attribute only if the flag attribute on the element is true.
     *
     * @param $attribute
     * @param $params
     */
    public function flagRequired($attribute, $params)
    {
        $flag = $params['flag'];
        if ($this->$flag && empty($this->$attribute)) {
            $this->addError($attribute, $this->getAttributeLabel($attribute).' is required');
        }
    }

    public function canCopy()
    {
        return true;
    }
}
