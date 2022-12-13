<?php

/**
 * This is the model class for table "event_template".
 *
 * The followings are the available columns in table 'event_template':
 * @property integer $id
 * @property string $name
 * @property string $event_type_id
 * @property string $source_event_id
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property User $createdUser
 * @property Event $sourceEvent
 * @property EventType $eventType
 * @property User $lastModifiedUser
 * @property OphTrOperationnote_Template[] $opnote_templates
 */

class EventTemplate extends BaseActiveRecordVersioned
{
    use MappedReferenceData;

    public const UPDATE_UNNEEDED = 'UNNEEDED';
    public const UPDATE_CREATE_ONLY = 'CREATE_ONLY';
    public const UPDATE_OR_CREATE = 'UPDATE_OR_CREATE';

    public const PRIORITY_PATIENT = 1;
    public const PRIORITY_TEMPLATE = 2;

    public function getSupportedLevels(): int
    {
        return ReferenceData::LEVEL_USER;
    }

    public function mappingColumn(int $level): string
    {
        return 'event_template_id';
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'event_template';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, event_type_id, source_event_id', 'required'),
            array('event_type_id, source_event_id, last_modified_user_id, created_user_id', 'length', 'max' => 10),
            array('name', 'length', 'max' => 100),
            array('last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array(
                'id, name, event_type_id, source_event_id, last_modified_user_id, last_modified_date, created_user_id, created_date',
                'safe',
                'on' => 'search'
            ),
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
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'sourceEvent' => array(self::BELONGS_TO, 'Event', 'source_event_id'),
            'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'opnote_templates' => array(self::HAS_ONE, 'OphTrOperationnote_Template', 'event_template_id'),
            'user_assignment' => array(self::HAS_ONE, 'EventTemplateUser', 'event_template_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'event_type_id' => 'Event Type',
            'source_event_id' => 'Source Event',
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

        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('event_type_id', $this->event_type_id, true);
        $criteria->compare('source_event_id', $this->source_event_id, true);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return EventTemplate the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return BaseTemplate
     */
    public function getDetailRecord()
    {
        $class_name = $this->eventType->class_name . '_Template';
        return $class_name::model()->findByPk($this->id);
    }

    /**
     * @param array $elements
     * @return array
     */
    public static function getPrefillablePriorities(array $elements): array
    {
        return array_reduce($elements, static function($priorities, $element) {
            if (method_exists($element, 'getPrefillablePriorities')) {
                $priorities[$element->elementType->class_name] = $element->getPrefillablePriorities();
            }

            return $priorities;
        }, []);
    }
}
