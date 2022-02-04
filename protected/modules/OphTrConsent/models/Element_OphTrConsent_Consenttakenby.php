<?php

/**
 * This is the model class for table "et_ophtrconsent_consenttakenby".
 *
 * The followings are the available columns in table 'et_ophtrconsent_consenttakenby':
 *
 * @property int    $id
 * @property string $event_id
 * @property string $name_hp
 * @property int    $second_op
 * @property string $sec_op_hp
 * @property string $consultant_id
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date          *
 *                                         The followings are the available model relations:
 * @property User   $consultant
 * @property User   $createdUser
 * @property Event  $event
 * @property User   $lastModifiedUser
 */
class Element_OphTrConsent_Consenttakenby extends BaseEventTypeElement
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophtrconsent_consenttakenby';
    }

    /**
     * @return array validation rules for model attributes
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['second_op', 'numerical', 'integerOnly' => true],
            ['consultant_id, last_modified_user_id, created_user_id', 'length', 'max' => 10],
            ['name_hp, second_op, sec_op_hp', 'length', 'max' => 255],
            ['name_hp, sec_op_hp, last_modified_date, created_date', 'safe'],
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            ['id, event_id, name_hp, second_op, sec_op_hp, consultant_id, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on' => 'search'],
        ];
    }

    /**
     * @return array relational rules
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return [
            'consultant' => [self::BELONGS_TO, 'User', 'consultant_id'],
            'createdUser' => [self::BELONGS_TO, 'User', 'created_user_id'],
            'event' => [self::BELONGS_TO, 'Event', 'event_id'],
            'lastModifiedUser' => [self::BELONGS_TO, 'User', 'last_modified_user_id'],
            'user' => [self::BELONGS_TO, 'User', 'created_user_id'],
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
            'name_hp' => 'Name of the Health Professional',
            'second_op' => 'Have you sought a second opinion ?',
            'sec_op_hp' => 'Health professional who provided the second opinion',
            'consultant_id' => 'Consultant',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
        ];
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
     *                             based on the search/filter conditions
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id);
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('name_hp', $this->name_hp, true);
        $criteria->compare('second_op', $this->second_op);
        $criteria->compare('sec_op_hp', $this->sec_op_hp, true);
        $criteria->compare('consultant_id', $this->consultant_id, true);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new CActiveDataProvider($this, [
            'criteria' => $criteria,
        ]);
    }

    protected function afterValidate()
    {
        if (empty($this->name_hp)) {
            $this->addError('name_hp', 'Please select a Health Professional');
        }
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     *
     * @param string $className active record class name
     *
     * @return Element_OphTrConsent_Consenttakenby the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
