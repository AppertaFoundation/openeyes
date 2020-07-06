<?php

/**
 * This is the model class for table "et_ophcitheatreadmission_procedurelist".
 *
 * The followings are the available columns in table 'et_ophcitheatreadmission_procedurelist':
 * @property string $id
 * @property string $event_id
 * @property string $eye_id
 * @property string $booking_event_id
 * @property string $disorder_id
 * @property string $priority_id
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property Event $bookingEvent
 * @property User $createdUser
 * @property Disorder $disorder
 * @property Event $event
 * @property Eye $eye
 * @property User $lastModifiedUser
 * @property Procedure[] $procedures
 * @property OphTrOperationbooking_Operation_Priority $priority
 * @property OphCiTheatreadmission_ProcedurelistProcedureAssignment[] $procedure_assignments
 */
class Element_OphCiTheatreadmission_ProcedureList extends \BaseEventTypeElement
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophcitheatreadmission_procedurelist';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('eye_id, procedures, disorder_id, priority_id', 'required'),
            array('event_id, eye_id, booking_event_id, priority_id, last_modified_user_id, created_user_id', 'length', 'max'=>10),
            array('disorder_id', 'length', 'max'=>20),
            array('procedure_assignments', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, event_id, eye_id, booking_event_id, disorder_id, priority_id, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'bookingEvent' => array(self::BELONGS_TO, 'Event', 'booking_event_id'),
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'disorder' => array(self::BELONGS_TO, 'Disorder', 'disorder_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'priority' => array(self::BELONGS_TO, 'OphTrOperationbooking_Operation_Priority', 'priority_id'),
            'procedures' => array(self::MANY_MANY, 'Procedure', 'ophcitheatreadmission_procedurelist_procedure_assignment(procedurelist_id, proc_id)', 'order' => 'display_order ASC'),
            'procedure_assignments' => array(self::HAS_MANY, 'OphCiTheatreadmission_ProcedurelistProcedureAssignment', 'procedurelist_id', 'order' => 'display_order ASC'),
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
            'eye_id' => 'Eye',
            'booking_event_id' => 'Booking Event',
            'disorder_id' => 'Disorder',
            'priority_id' => 'Priority',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
        );
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

        $criteria->compare('id', $this->id, true);
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('eye_id', $this->eye_id, true);
        $criteria->compare('booking_event_id', $this->booking_event_id, true);
        $criteria->compare('disorder_id', $this->disorder_id, true);
        $criteria->compare('priority_id', $this->priority_id, true);
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
     * @return Element_OphCiTheatreadmission_ProcedureList the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * Update the procedures for this element with the given ids.
     *
     * @param $procedure_ids
     *
     * @throws Exception
     */
    public function updateProcedures($procedure_ids)
    {
        $current_procedures = array();
        foreach ($this->procedure_assignments as $pa) {
            $current_procedures[$pa->proc_id] = $pa;
        }

        foreach ($procedure_ids as $i => $proc_id) {
            $display_order = $i + 1;
            if (isset($current_procedures[$proc_id])) {
                $procedure_assignment = $current_procedures[$proc_id];
                if ($procedure_assignment->display_order != $display_order) {
                    $procedure_assignment->display_order = $display_order;
                    if (!$procedure_assignment->save()) {
                        throw new Exception('Unable to save procedure assignment');
                    }
                }
                unset($current_procedures[$proc_id]);
            } else {
                $procedure_assignment = new OphCiTheatreadmission_ProcedurelistProcedureAssignment();
                $procedure_assignment->procedurelist_id = $this->id;
                $procedure_assignment->proc_id = $proc_id;
                $procedure_assignment->display_order = $display_order;
                if (!$procedure_assignment->save()) {
                    throw new Exception('Unable to save procedure assignment');
                }
            }
        }

        // delete remaining current procedures
        foreach ($current_procedures as $pa) {
            if (!$pa->delete()) {
                throw new Exception('Unable to delete procedure assignment: '.print_r($pa->getErrors(), true));
            }
        }
    }

    public function getEyeOptions()
    {
        $criteria = new CDbCriteria();
        $criteria->order = 't.display_order asc';

        $criteria->addCondition('t.id != :three');
        $criteria->params[':three'] = 3;

        return CHtml::listData(Eye::model()->findAll($criteria), 'id', 'name');
    }
}
