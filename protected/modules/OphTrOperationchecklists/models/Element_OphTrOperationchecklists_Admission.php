<?php
/**
 * (C) Copyright Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * This is the model class for table "et_ophtroperationchecklists_admission".
 *
 * The followings are the available columns in table 'et_ophtroperationchecklists_admission':
 * @property integer $id
 * @property string $event_id
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property User $createdUser
 * @property User $lastModifiedUser
 * @property Event $event
 * @property OphTrOperationchecklists_AdmissionResults[] $checklistResults
 */
class Element_OphTrOperationchecklists_Admission extends \BaseEventTypeElement
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophtroperationchecklists_admission';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, last_modified_user_id, created_user_id', 'length', 'max'=>10),
            array('checklistResults, event_id, last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, event_id, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'checklistResults' => array(self::HAS_MANY, 'OphTrOperationchecklists_AdmissionResults', 'element_id'),
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
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
        );
    }

    public function setDefaultOptions(Patient $patient = null)
    {

        parent::setDefaultOptions($patient);
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Element_OphTrOperationchecklists_Admission the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * Update the admission checklist results, observations and dilations (if exists) for this element
     *
     * @throws Exception
     */
    public function saveData()
    {
        foreach ($this->checklistResults as $result) {
            $result->element_id = $this->id;
            if (!$result->save()) {
                throw new Exception('Unable to save result');
            }
            if ($result->dilation) {
                $result->dilation->checklist_result_id = $result->id;
                if (!$result->dilation->save()) {
                    throw new Exception('Unable to save dilation: ' . print_r($result->dilation->getErrors(), true));
                }
                foreach ($result->dilation->treatments as $treatment) {
                    $treatment->dilation_id = $result->dilation->id;
                    if (!$treatment->save()) {
                        throw new Exception('Unable to save dilation treatment: ' . print_r($treatment->getErrors(), true));
                    }
                }
            }
            if ($result->observations) {
                $result->observations->checklist_result_id = $result->id;
                OphTrOperationchecklists_Observations::model()->deleteAllByAttributes(array('checklist_result_id' => $result->id));
                if (!$result->observations->save()) {
                    throw new Exception('Unable to save observations');
                }
            }
        }
    }

    protected function afterValidate()
    {
        $anaestheticTypeQuestionRelationIds = OphTrOperationchecklists_Questions::getAnaestheticTypeQuestionRelationIds();
        $selectedAnaestheticTypes = Yii::app()->request->getPost('AnaestheticType');
        if (isset($selectedAnaestheticTypes)) {
            $typeGaSedId = [];
            $anaestheticModels = AnaestheticType::model()->findAll('code = "GA" or code = "Sed"');
            foreach ($anaestheticModels as $anaestheticModel) {
                $typeGaSedId[] = $anaestheticModel->id;
            }

            $result = array_intersect($selectedAnaestheticTypes, $typeGaSedId);
        }

        $isError = false;
        foreach ($this->checklistResults as $checklistResult) {
            if (!in_array($checklistResult->question_id, $anaestheticTypeQuestionRelationIds)) {
                if ($checklistResult->question->mandatory === '1') {
                    // check answer is given or not
                    if ($checklistResult->answer_id === '' && $checklistResult->answer === '') {
                        $isError = true;
                        $this->setFrontEndError('checklistResults_' . $checklistResult->question_id . '_answer');
                    }
                }
            } else {
                if (!empty($result) ) {
                    // check answer is given or not
                    if ($checklistResult->answer_id === '' && $checklistResult->answer === '') {
                        $isError = true;
                        $this->setFrontEndError('checklistResults_' . $checklistResult->question_id . '_answer');
                    }
                }
            }

            if (isset($checklistResult->observations)) {
                if (!$checklistResult->observations->validate()) {
                    $observationsErrors = $checklistResult->observations->getErrors();
                    foreach ($observationsErrors as $observationsErrorAttributeName => $observationsErrorMessages) {
                        foreach ($observationsErrorMessages as $observationsErrorMessage) {
                            $this->addError('checklistResults_' . $checklistResult->question_id . '_observations_' . $observationsErrorAttributeName, $observationsErrorMessage);
                        }
                    }
                }
            }
        }

        if ($isError) {
            $this->addError(null, "Please provide responses to the mandatory questions.");
        }

        return parent::afterValidate();
    }
}
