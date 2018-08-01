<?php

/**
 * This is the model class for table "user_hotlist_item".
 *
 * The followings are the available columns in table 'user_hotlist_item':
 * @property integer $id
 * @property string $patient_id
 * @property integer $is_open
 * @property string $user_comment
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property User $createdUser
 * @property User $lastModifiedUser
 * @property Patient $patient
 */
class UserHotlistItem extends BaseActiveRecordVersioned
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'user_hotlist_item';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('patient_id', 'required'),
            array('is_open', 'numerical', 'integerOnly' => true),
            array('patient_id, last_modified_user_id, created_user_id', 'length', 'max' => 10),
            array('user_comment, last_modified_date, created_date', 'safe'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'patient' => array(self::BELONGS_TO, 'Patient', 'patient_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'patient_id' => 'Patient',
            'is_open' => 'Is Open',
            'user_comment' => 'User Comment',
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
     * @return UserHotlistItem the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * Gets a string representation of the time that passed since this item was updated
     *
     * @return string The interval since update as a string
     */
    public function getIntervalString()
    {
        $interval = (new DateTime())->diff(new DateTime($this->last_modified_date));

        $format_keys = ['y' => 'y', 'm' => 'm', 'd' => 'd', 'h' => 'h', 'i' => 'm'];
        $format = [];
        foreach ($format_keys as $interval_key => $title) {
            if ($interval->$interval_key) {
                $format[] = '%' . $interval_key . ' ' . $title;
            }
        }

        $result = $interval->format(implode($format));

        return $result === '' ? 'less than a minute' : $result;
    }

    /**
     * Gets a value indicating whether this item was updated today
     *
     * @return bool True if this ite was updated today, otherwise false.
     */
    public function wasUpdatedToday()
    {
        return new DateTime($this->last_modified_date) > new DateTime('midnight');
    }

    /**
     * Gets all hotlist items for the current user with the given state and date
     *
     * @param int $is_open 1 if only open items should be found, 0 if only closed items should be found
     * @param string $date The date string (YYYY-mm-dd) that the hotlist items should have been last modified on
     * @return UserHotlistItem[] The hotlist items that match the given state and date
     */
    public function getHotlistItems($is_open, $date = null)
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'created_user_id = :user_id AND is_open = :is_open';
        $criteria->params = array(':user_id' => Yii::app()->user->id, ':is_open' => $is_open);

        if ($date) {
            $criteria->condition .= ' AND DATE(last_modified_date) = DATE(:date)';
            $criteria->params[':date'] = $date;
        }

        $criteria->order = 'last_modified_date DESC';

        return $this->findAll($criteria);
    }
}
