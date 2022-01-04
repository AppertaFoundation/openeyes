<?php

/**
 * This is the model class for table "team_team_assign".
 *
 * The followings are the available columns in table 'team_team_assign':
 * @property integer $id
 * @property integer $parent_team_id
 * @property integer $child_team_id
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property Team $childTeam
 * @property User $createdUser
 * @property User $lastModifiedUser
 * @property Team $parentTeam
 */
class TeamTeamAssign extends BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return TeamTeamAssign the static model class
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
        return 'team_team_assign';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('parent_team_id, child_team_id', 'required'),
            array('parent_team_id, child_team_id', 'numerical', 'integerOnly'=>true),
            array('last_modified_user_id, created_user_id', 'length', 'max'=>10),
            array('last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            array('id, parent_team_id, child_team_id', 'safe', 'on'=>'search'),
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
            'childTeam' => array(self::BELONGS_TO, 'Team', 'child_team_id'),
            'parentTeam' => array(self::BELONGS_TO, 'Team', 'parent_team_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'parent_team_id' => 'Parent Team',
            'child_team_id' => 'Child Team',
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

        $criteria=new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('parent_team_id', $this->parent_team_id);
        $criteria->compare('child_team_id', $this->child_team_id);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
}
