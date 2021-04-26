<?php

/**
 * This is the model class for table "case_search_saved_search".
 *
 * The followings are the available columns in table 'case_search_saved_search':
 * @property int $id
 * @property string $name
 * @property string $search_criteria
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property User $created_user
 * @property User $last_modified_user
 */
class SavedSearch extends BaseActiveRecordVersioned
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'case_search_saved_search';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, search_criteria', 'required'),
            array('name', 'length', 'max' => 50),
            array('last_modified_user_id, created_user_id', 'length', 'max' => 10),
            array('last_modified_date, created_date', 'safe'),
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
            'created_user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'last_modified_user' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
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
            'search_criteria' => 'Search Criteria',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return BaseActiveRecord|SavedSearch the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
