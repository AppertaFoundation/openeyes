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

/**
 * This is the model class for table "et_ophtrlaser_treatment".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property int $eye_id
 *
 * The followings are the available model relations:
 * @property ElementType $element_type
 * @property EventType $eventType
 * @property Event $event
 * @property User $user
 * @property User $usermodified
 * @property Eye $eye
 */
class Element_OphTrLaser_Treatment extends SplitEventTypeElement
{
    public $service;
    const RIGHT_EYE_ID = 2;
    const LEFT_EYE_ID = 1;

    protected $errorExceptions = array(
    'Element_OphTrLaser_Treatment_left_procedures' => 'treatment_left_procedures',
    'Element_OphTrLaser_Treatment_right_procedures' => 'treatment_right_procedures',
    );
    /**
     * Returns the static model of the specified AR class.
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
        return 'et_ophtrlaser_treatment';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, eye_id, ', 'safe'),
            array('eye_id,', 'required'),
            array('left_procedures', 'requiredIfSide', 'side' => 'left'),
            array('right_procedures', 'requiredIfSide', 'side' => 'right'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, event_id, eye_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * (non-PHPdoc).
     *
     * @see SplitEventTypeElement::sidedFields()
     */
    public function sidedFields()
    {
        return array('procedures');
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'element_type' => array(self::HAS_ONE, 'ElementType', 'id', 'on' => "element_type.class_name='".get_class($this)."'"),
            'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
            'procedure_assignments' => array(self::HAS_MANY, 'OphTrLaser_LaserProcedureAssignment', 'treatment_id', 'order' => 'display_order ASC'),
            'right_procedures' => array(self::HAS_MANY, 'Procedure', 'procedure_id', 'order' => 'display_order ASC', 'through' => 'procedure_assignments', 'on' => 'procedure_assignments.eye_id = '.self::RIGHT_EYE_ID),
            'left_procedures' => array(self::HAS_MANY, 'Procedure', 'procedure_id', 'order' => 'display_order ASC', 'through' => 'procedure_assignments', 'on' => 'procedure_assignments.eye_id = '.self::LEFT_EYE_ID),
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
            'procedures' => 'Procedures',
            'left_procedures' => 'Procedures',
            'right_procedures' => 'Procedures',
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

        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('eye_id', $this->eye_id);

        return new CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
            ));
    }

    /*
     * update procedure assignments to given procedure ids on the given side
     *
     */
    public function updateProcedures($proc_ids, $side)
    {
        // get the current procedures
        $current_ids = array();
        $current_assignments = array();
        foreach ($this->procedure_assignments as $proc) {
            if ($proc->eye_id == $side) {
                $current_ids[] = $proc->procedure_id;
                $current_assignments[] = $proc;
            }
        }

        // check for new procedures
        foreach ($proc_ids as $up_id) {
            if (!in_array($up_id, $current_ids)) {
                // create new procedure assignment
                $ass = new OphTrLaser_LaserProcedureAssignment();
                $ass->eye_id = $side;
                $ass->treatment_id = $this->id;
                $ass->procedure_id = $up_id;
                if (!$ass->save()) {
                    throw new Exception('Unable to save procedure assignment for treatment: '.print_r($ass->getErrors(), true));
                }
            }
        }

        // delete removed
        foreach ($current_assignments as $curr) {
            if (!in_array($curr->procedure_id, $proc_ids)) {
                // delete it
                if (!$curr->delete()) {
                    throw new Exception('Unable to delete procedure assignment for treatment: '.print_r($curr->getErrors(), true));
                }
            }
        }
    }

    /*
     * wrapper function to update the procedures for this treatment on the right eye
     */
    public function updateRightProcedures($data)
    {
        $this->updateProcedures($data, self::RIGHT_EYE_ID);
    }

    /*
     * wrapper function to update the procedures for this treatment on the left eye
    */
    public function updateLeftProcedures($data)
    {
        $this->updateProcedures($data, self::LEFT_EYE_ID);
    }

    public function getViewTitle()
    {
        return 'Procedures';
    }

    public function getFormTitle()
    {
        return 'Procedures';
    }
}
