<?php

namespace OEModule\OphDrPGDPSD\models;

use OE\factories\models\traits\HasFactory;
use OphDrPGDPSD_API;
use Team;
use User;

/**
 * This is the model class for table "ophdrpgdpsd_assignedteam".
 *
 * The followings are the available columns in table 'ophdrpgdpsd_assignedteam':
 * @property integer $id
 * @property integer $pgdpsd_id
 * @property integer $team_id
 * @property integer $last_modified_user_id
 * @property string $last_modified_date
 * @property integer $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property User $createdUser
 * @property User $lastModifiedUser
 * @property OphDrPGDPSD_PGDPSD $psd
 * @property Team $team
 */
class OphDrPGDPSD_AssignedTeam extends \BaseActiveRecordVersioned
{
    use HasFactory;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophdrpgdpsd_assignedteam';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('pgdpsd_id', 'required'),
            array('pgdpsd_id', 'numerical', 'integerOnly'=>true),
            array('last_modified_user_id, created_user_id', 'length', 'max'=>10),
            array('last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, pgdpsd_id, team_id, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
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
            'createdUser' => array(self::BELONGS_TO, User::class, 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, User::class, 'last_modified_user_id'),
            'pgdpsd' => array(self::BELONGS_TO, OphDrPGDPSD_PGDPSD::class, 'pgdpsd_id'),
            'team' => array(self::BELONGS_TO, Team::class, 'team_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'pgdpsd_id' => 'PGDPSD',
            'team_id' => 'Team',
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

        $criteria=new \CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('pgdpsd_id', $this->pgdpsd_id);
        $criteria->compare('team_id', $this->team_id, true);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new \CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return PsdUserAssign the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
