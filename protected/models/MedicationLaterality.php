<?php

/**
 * This is the model class for table "medication_laterality".
 *
 * The followings are the available columns in table 'medication_laterality':
 * @property integer $id
 * @property string $name
 * @property string $deleted_date
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property User $lastModifiedUser
 * @property User $createdUser
 */
class MedicationLaterality extends BaseActiveRecordVersioned
{

    const LEFT = 1;
    const RIGHT = 2;
    const BOTH = 3;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'medication_laterality';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name', 'required'),
            array('name', 'length', 'max'=>40),
            array('last_modified_user_id, created_user_id', 'length', 'max'=>10),
            array('deleted_date, last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, name, deleted_date, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
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
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
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
            'deleted_date' => 'Deleted Date',
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

        $criteria->compare('id',$this->id);
        $criteria->compare('name',$this->name,true);
        $criteria->compare('deleted_date',$this->deleted_date,true);
        $criteria->compare('last_modified_user_id',$this->last_modified_user_id,true);
        $criteria->compare('last_modified_date',$this->last_modified_date,true);
        $criteria->compare('created_user_id',$this->created_user_id,true);
        $criteria->compare('created_date',$this->created_date,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return MedicationLaterality the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @param array $ids
     * @return $this
     *
     * Returns active items or those that match $ids
     */

    public function activeOrPk($ids = [])
    {
        $crit = new CDbCriteria();
        $crit->condition = "deleted_date IS NULL";
        $crit->addInCondition('id', $ids, 'OR');
        $this->getDbCriteria()->mergeWith($crit);

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
