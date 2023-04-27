<?php

use OE\factories\models\traits\HasFactory;

/**
 * This is the model class for table "worklist_wait_time".
 *
 * The followings are the available columns in table 'worklist_wait_time':
 * @property integer $id
 * @property integer $wait_minutes
 * @property string $label
 * @property integer $display_order
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property User $createdUser
 * @property User $lastModifiedUser
 */
class WorklistWaitTime extends BaseActiveRecordVersioned
{
    use HasFactory;
    use MappedReferenceData;

    public function getSupportedLevels(): int
    {
        return
            ReferenceData::LEVEL_SITE |
            ReferenceData::LEVEL_SPECIALTY |
            ReferenceData::LEVEL_SUBSPECIALTY |
            ReferenceData::LEVEL_FIRM;
    }

    public function mappingColumn(int $level): string
    {
        return $this->tableName() . '_id';
    }
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'worklist_wait_time';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('wait_minutes', 'numerical', 'integerOnly'=>true),
            array('last_modified_user_id, created_user_id', 'length', 'max'=>10),
            array('display_order, last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, wait_minutes, label', 'safe', 'on'=>'search'),
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
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'sites' => array(self::MANY_MANY, 'Site', $this->tableName().'_site('.$this->tableName().'_id, site_id)'),
            'specialtys' => array(self::MANY_MANY, 'Specialty', $this->tableName().'_specialty('.$this->tableName().'_id, specialty_id)'),
            'subspecialtys' => array(self::MANY_MANY, 'Subspecialty', $this->tableName().'_subspecialty('.$this->tableName().'_id, subspecialty_id)'),
            'firms' => array(self::MANY_MANY, 'Firm', $this->tableName().'_firm('.$this->tableName().'_id, firm_id)'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'wait_minutes' => 'Wait Minutes',
            'label' => 'Label',
            'display_order' => 'Display Order',
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

        $criteria->compare('id', $this->id);
        $criteria->compare('wait_minutes', $this->wait_minutes);
        $criteria->compare('label', $this->label);
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
     * @return WorklistWaitTime the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
