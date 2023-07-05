<?php

namespace OEModule\OphCoMessaging\models;

use OE\factories\models\traits\HasFactory;

/**
 * A model representing a mailbox.
 * Mailboxes may be assigned to users, teams, or a combination of the two.
 *
 * The followings are the available columns in table 'mailbox':
 * @property int $id
 * @property string $name
 * @property bool $is_personal
 * @property bool $active
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property User $createdUser
 * @property User $lastModifiedUser
 * @property Team[] $teams
 * @property User[] $users
 * @property Element_OphCoMessaging_Message[] $all_messages
 * @property Element_OphCoMessaging_Message[] $read_messages
 * @property Element_OphCoMessaging_Message[] $unread_messages
 * @property Element_OphCoMessaging_Message[] $sent_messages
 */
class Mailbox extends \BaseActiveRecordVersioned
{
    use HasFactory;

    public const MAX_NAME_PERSONAL = 200;
    public const MAX_NAME_NOT_PERSONAL = 24;

    protected $auto_update_relations = true;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'mailbox';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            ['name', 'required'],
            ['name', 'uniqueIfNotPersonalValidator'],
            ['name', 'lengthIfNotPersonalValidator', 'max' => self::MAX_NAME_NOT_PERSONAL],
            ['is_personal', 'boolean'],
            ['is_personal', 'exactlyOneUserAndNoTeamsIfPersonalValidator'],
            ['name', 'length', 'max' => self::MAX_NAME_PERSONAL],
            ['last_modified_user_id, created_user_id', 'length', 'max' => 10],
            ['last_modified_date, created_date, users, teams', 'safe'],
            // The following rule is used by search().
            ['id, name, is_personal, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on' => 'search'],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return [
            'createdUser' => [self::BELONGS_TO, \User::class, 'created_user_id'],
            'lastModifiedUser' => [self::BELONGS_TO, \User::class, 'last_modified_user_id'],
            'teams' => [self::MANY_MANY, \Team::class, 'mailbox_team(mailbox_id, team_id)'],
            'users' => [self::MANY_MANY, \User::class, 'mailbox_user(mailbox_id, user_id)'],
            'all_messages' => [self::MANY_MANY, Element_OphCoMessaging_Message::class, 'ophcomessaging_message_recipient(mailbox_id, element_id)'],
            'sent_messages' => [self::HAS_MANY, Element_OphCoMessaging_Message::class, 'sender_mailbox_id'],
        ];
    }

    public function uniqueIfNotPersonalValidator($attribute, $params)
    {
        $mailboxes = Mailbox::model()->count(
            'name = :name AND is_personal != 1 AND id != :id',
            [':name' => $this->$attribute, ':id' => $this->id]
        );

        if ($mailboxes > 0) {
            $this->addError($attribute, "Name must be unique to all other shared mailboxes.");
        }
    }

    public function lengthIfNotPersonalValidator($attribute, $params)
    {
        $max = $params['max'];
        if (strlen($this->$attribute) > $max && (int)$this->is_personal !== 1) {
            $this->addError($attribute, "Must be less than $max letters in length.");
        }
    }

    public function exactlyOneUserAndNoTeamsIfPersonalValidator($attribute, $params)
    {
        if ($this->is_personal && count($this->users) !== 1 && count($this->teams) !== 0) {
            $this->addError($attribute, 'A personal mailbox should have exactly one user and no teams.');
        }
    }

    public function behaviors()
    {
        return [
            'LookupTable' => \LookupTable::class
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'is_personal' => 'Is Personal',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
        ];
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     */
    public function search()
    {
        $criteria = new \CDbCriteria();

        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('is_personal', $this->is_personal);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new \CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function isUserAssociatedWith($user)
    {
        $criteria = new \CDbCriteria();

        $criteria->with = [
            'users',
            'teams.users' => ['alias' => 'team_users']
        ];

        $criteria->addCondition('t.id = :mailbox_id');
        $criteria->addCondition('(users_users.user_id = :uid OR team_users.id = :uid)');

        $criteria->params = [':mailbox_id' => $this->id, ':uid' => $user->id];

        return self::model()->exists($criteria);
    }

    /**
     * Get the user for a personal mailbox.
     * Throws an exception if the mailbox is shared.
     *
     * @throws \Exception
     * @return \User
     */
    public function getUserForPersonalMailbox()
    {
        if ($this->is_personal) {
            return $this->users[0];
        } else {
            throw new \Exception("Cannot get a personal mailbox user from a shared mailbox");
        }
    }

    /**
     * Scope to constrain query to mailboxes relevant to the given user id
     *
     * @param string|int $user_id
     * @return self
     */
    public function forUser($user_id): self
    {
        $this->getDbCriteria()
             ->mergeWith([
                 'with' => [
                     'users',
                     'teams.users' => ['alias' => 'team']
                 ],
                 'condition' => '(users_users.user_id = :user_id OR users_team.user_id = :user_id)',
                 'params' => [':user_id' => $user_id]
             ]);

        return $this;
    }

    /**
     * Scope to constrain query to mailboxes included as a sender or receiver of the given message
     *
     * @param string|int $element_id
     * @return self
     */
    public function forMessageSender($element_id): self
    {
        $this->getDbCriteria()
            ->mergeWith([
                'with' => [
                    'sent_messages' => ['alias' => 'sent']
                ],
                'condition' => '(sent.id = :message_id)',
                'params' => [':message_id' => $element_id]
            ]);

        return $this;
    }

    /**
     * Scope to constrain query to mailboxes included as a sender or receiver of the given message
     *
     * @param string|int $element_id
     * @return self
     */
    public function forMessageRecipients($element_id): self
    {
        $this->getDbCriteria()
            ->mergeWith([
                'with' => [
                    'all_messages' => ['alias' => 'all']
                ],
                'condition' => '(element_id = :message_id AND all_messages_all.mailbox_id = t.id)',
                'params' => [':message_id' => $element_id]
            ]);

        return $this;
    }

    /**
     * Scope to constrain query to personal mailboxes for the given user id
     *
     * @param string|int $user_id
     * @return self
     */
    public function forPersonalMailbox($user_id): self
    {
        $this->getDbCriteria()
            ->mergeWith([
                'with' => [
                    'users' => [
                        'condition' => 'user_id = :user_id',
                        'params' => [':user_id' => $user_id]
                    ]
                ],
                'condition' => 'is_personal = 1',
            ]);

        return $this;
    }
}
