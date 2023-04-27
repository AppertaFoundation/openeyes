<?php

namespace OEModule\OphCoMessaging\models;

/**
 * A model for associating a mailbox with a team.
 *
 * The followings are the available columns in table 'mailbox_team':
 * @property integer $id
 * @property integer $mailbox_id
 * @property integer $team_id
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property User $createdUser
 * @property User $lastModifiedUser
 * @property Mailbox $mailbox
 * @property Team $team
 */
class MailboxTeam extends \BaseActiveRecordVersioned
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'mailbox_team';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('mailbox_id, team_id', 'required'),
            array('mailbox_id, team_id', 'numerical', 'integerOnly' => true),
            array('last_modified_user_id, created_user_id', 'length', 'max' => 10),
            array('last_modified_date, created_date', 'safe'),
            array('id, mailbox_id, team_id, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'createdUser' => array(self::BELONGS_TO, \User::class, 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, \User::class, 'last_modified_user_id'),
            'mailbox' => array(self::BELONGS_TO, Mailbox::class, 'mailbox_id'),
            'team' => array(self::BELONGS_TO, \Team::class, 'team_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'mailbox_id' => 'Mailbox',
            'team_id' => 'Team',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     */
    public function search()
    {
        $criteria = new \CDbCriteria();

        $criteria->compare('id', $this->id);
        $criteria->compare('mailbox_id', $this->mailbox_id);
        $criteria->compare('team_id', $this->team_id);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new \CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
}
