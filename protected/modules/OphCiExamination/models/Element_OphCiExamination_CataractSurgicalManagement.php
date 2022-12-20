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
 * This is the model class for table "et_ophciexamination_cataractsurgicalmanagement".
 *
 * The followings are the available columns in table:
 *
 * @property int $id
 * @property int $event_id
 * @property int $eye_id
 * @property float $left_target_postop_refraction
 * @property float $right_target_postop_refraction
 * @property int $left_correction_discussed
 * @property int $right_correction_discussed
 * @property int $left_refraction_category
 * @property int $right_refraction_category
 * @property int $left_eye_id
 * @property int $right_eye_id
 * @property int $left_reason_for_surgery_id
 * @property int $right_reason_for_surgery_id
 * @property string $left_notes
 * @property string $right_notes
 * @property int $left_guarded_prognosis
 * @property int $right_guarded_prognosis
 * @property int $last_modified_user_id
 * @property \DateTime $last_modified_date
 * @property int $created_user_id
 * @property \DateTime $created_date
 *
 * The followings are the available model relations:
 *
 * @property \EventType $eventType
 * @property \Event $event
 * @property \User $user
 * @property \User $usermodified
 * @property \Eye $eye
 * @property OphCiExamination_CataractSurgicalManagement_Eye $leftEye
 * @property OphCiExamination_CataractSurgicalManagement_Eye $rightEye
 * @property OphCiExamination_Primary_Reason_For_Surgery $leftReasonForSurgery
 * @property OphCiExamination_Primary_Reason_For_Surgery $rightReasonForSurgery
 */
class Element_OphCiExamination_CataractSurgicalManagement extends \SplitEventTypeElement
{
    use traits\CustomOrdering;
    public $service;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return Element_OphCiExamination_CataractSurgicalManagement the static model class
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
        return 'et_ophciexamination_cataractsurgicalmanagement';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['eye_id, left_target_postop_refraction, left_correction_discussed,
              right_target_postop_refraction, right_correction_discussed, left_refraction_category, right_refraction_category,
              left_notes, right_notes, left_guarded_prognosis, right_guarded_prognosis', 'safe'],
            ['left_eye_id, right_eye_id', 'required'],
            ['right_reason_for_surgery_id, right_guarded_prognosis', 'requiredIfSide', 'side' => 'right'],
            ['left_reason_for_surgery_id, left_guarded_prognosis', 'requiredIfSide', 'side' => 'left'],
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            ['id, eye_id, right_target_postop_refraction, right_correction_discussed, right_guarded_prognosis', 'safe', 'on' => 'search'],
            ['left_target_postop_refraction, left_correction_discussed, left_guarded_prognosis', 'safe', 'on' => 'search'],
        ];
    }

    public function sidedFields()
    {
        return ['target_postop_refraction', 'correction_discussed', 'reason_for_surgery_id', 'refraction_category'];
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return [
            'eventType' => [self::BELONGS_TO, 'EventType', 'event_type_id'],
            'event' => [self::BELONGS_TO, 'Event', 'event_id'],
            'user' => [self::BELONGS_TO, 'User', 'created_user_id'],
            'usermodified' => [self::BELONGS_TO, 'User', 'last_modified_user_id'],
            'eye' => [self::BELONGS_TO, 'Eye', 'eye_id'],
            'leftEye' => [
                self::BELONGS_TO,
                'OEModule\OphCiExamination\models\OphCiExamination_CataractSurgicalManagement_Eye',
                'left_eye_id'
            ],
            'rightEye' => [
                self::BELONGS_TO,
                'OEModule\OphCiExamination\models\OphCiExamination_CataractSurgicalManagement_Eye',
                'right_eye_id'
            ],
            'leftReasonForSurgery' => [
                self::BELONGS_TO,
                'OEModule\OphCiExamination\models\OphCiExamination_Primary_Reason_For_Surgery',
                'left_reason_for_surgery_id',
            ],
            'rightReasonForSurgery' => [
                self::BELONGS_TO,
                'OEModule\OphCiExamination\models\OphCiExamination_Primary_Reason_For_Surgery',
                'right_reason_for_surgery_id',
            ],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event_id' => 'Event',
            'left_target_postop_refraction' => 'Post op refractive target',
            'right_target_postop_refraction' => 'Post op refractive target',
            'left_correction_discussed' => 'The post operative refractive target has been discussed with the patient',
            'right_correction_discussed' => 'The post operative refractive target has been discussed with the patient',
            'right_guarded_prognosis' => 'Guarded Prognosis',
            'left_guarded_prognosis' => 'Guarded Prognosis',
            'left_reason_for_surgery_id' => 'Reason For Surgery',
            'right_reason_for_surgery_id' => 'Reason For Surgery',
            'leftReasonForSurgery' => 'Primary reason for cataract surgery',
            'rightReasonForSurgery' => 'Primary reason for cataract surgery',
        ];
    }

    public static function getLatestTargetRefraction($patient, $side)
    {
        $criteria = new \CDbCriteria();
        $criteria->join = 'JOIN event ev ON t.event_id = ev.id';
        $criteria->join .= ' JOIN episode ep ON ev.episode_id = ep.id';
        $criteria->addCondition('ep.patient_id = ' . $patient->id);
        $criteria->order = 'last_modified_date DESC';

        $cataract_surgical_managements = self::model()->findAll($criteria);
        return (count($cataract_surgical_managements) > 0) ?
            $cataract_surgical_managements[0]->{$side . '_target_postop_refraction'} :
            null;
    }

    public function getFormattedTargetRefraction($side)
    {
        $raw_target_refraction = (string)$this->{$side . '_target_postop_refraction'};
        $is_zero = $raw_target_refraction === '0.00';
        $index = 0;
        $output = '';
        if (!in_array($raw_target_refraction[$index], ['-', '+']) && !$is_zero) {
            $output .= '+';
        } elseif (in_array($raw_target_refraction[$index], ['-', '+'])) {
            $output .= $raw_target_refraction[$index++];
        }
        if ($raw_target_refraction[$index + 1] === '.') {
            $output .= '0';
        }
        while ($index < strlen($raw_target_refraction)) {
            $output .= $raw_target_refraction[$index++];
        }
        if ($raw_target_refraction[--$index] !== 'D') {
            $output .= 'D';
        }
        return $output;
    }

    public function getCorrectionDiscussed($side)
    {
        $discussed_attribute = $side . '_correction_discussed';
        if (isset($this->$discussed_attribute) && $this->$discussed_attribute !== '') {
            return ($this->$discussed_attribute) ? '' : '<span style="float:right;">Refractive target not discussed</span>';
        }
        return '';
    }

    public function beforeSave()
    {
        foreach ($this->sidedFields() as $field_suffix) {
            foreach (['left_', 'right_'] as $field_prefix) {
                if ($this->{$field_prefix . $field_suffix} === '') {
                    $this->{$field_prefix . $field_suffix} = null;
                }
            }
        }

        return parent::beforeSave();
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return \CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new \CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('event_id', $this->event_id, true);

        $criteria->compare('description', $this->description);

        return new \CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function setDefaultOptions(\Patient $patient = null)
    {
        $this->right_eye_id = \OEModule\OphCiExamination\models\OphCiExamination_CataractSurgicalManagement_Eye::FIRST_EYE;
        $this->left_eye_id = \OEModule\OphCiExamination\models\OphCiExamination_CataractSurgicalManagement_Eye::SECOND_EYE;

        $api = \Yii::app()->moduleAPI->get('OphCiExamination');
        $previous_element = $api->getLatestElement('OEModule\OphCiExamination\models\Element_OphCiExamination_CataractSurgicalManagement', $patient, false);

        if ($previous_element) {
            $this->eye_id = $previous_element->eye_id;
            foreach (['left', 'right'] as $side) {
                $this->{$side . '_target_postop_refraction'} = $previous_element->{$side . '_target_postop_refraction'};
                $this->{$side . '_correction_discussed'} = $previous_element->{$side . '_correction_discussed'};
                $this->{$side . '_refraction_category'} = $previous_element->{$side . '_refraction_category'};
                $this->{$side . '_eye_id'} = $previous_element->{$side . '_eye_id'};
                $this->{$side . '_reason_for_surgery_id'} = $previous_element->{$side . '_reason_for_surgery_id'};
                $this->{$side . '_notes'} = $previous_element->{$side . '_notes'};
                $this->{$side . '_guarded_prognosis'} = $previous_element->{$side . '_guarded_prognosis'};
            }
        }
    }

    public function __toString()
    {
        $right_description = ($this->eye_id !== (string)self::LEFT) ? $this->rightEye->name . ' target post-op: ' . $this->right_target_postop_refraction . ' ' : '';
        $left_description = ($this->eye_id !== (string)self::RIGHT) ? $this->leftEye->name . ' target post-op: ' . $this->left_target_postop_refraction : '';
        return $right_description . $left_description;
    }
}
