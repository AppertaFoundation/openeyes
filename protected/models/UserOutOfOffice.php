<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2020, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * This is the model class for table "user_out_of_office".
 *
 * The followings are the available columns in table:
 *
 * @property int $id
 * @property int $user_id
 * @property DateTime $from_date
 * @property DateTime $to_date
 * @property int $alternate_user_id
 * @property bool $enabled
 *
 */
class UserOutOfOffice extends BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return UserOutOfOffice the static model class
     */
    public static function model($class_name = null)
    {
        return parent::model($class_name);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'user_out_of_office';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('user_id, enabled', 'required'),
            array('from_date, to_date', 'default', 'setOnEmpty' => true, 'value' => null),
            array('from_date, to_date, alternate_user_id', 'requiredIfEnabled'),
            array('from_date', 'outOfOfficeDurationValidator'),
            array('user_id, from_date, to_date, alternate_user_id, enabled', 'safe'),
            array('user_id, from_date, to_date, alternate_user_id, enabled', 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'user' => array(self::BELONGS_TO, 'User', 'user_id'),
            'alternate_user' => array(self::BELONGS_TO, 'User', 'alternate_user_id'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'from_date' => 'From',
            'to_date' => 'To',
            'alternate_user_id' => 'Alternate User'
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria();

        $criteria->compare('user_id', $this->id, true);
        $criteria->compare('from_date', $this->from_date, true);
        $criteria->compare('to_date', $this->to_date, true);
        $criteria->compare('alternate_user_id', $this->alternate_user_id, true);
        $criteria->compare('enabled', $this->enabled, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * If the out of office notice has been enabled but attributes have not been set
     *
     * @param $attribute
     * @param $params
     */
    public function requiredIfEnabled($attribute, $params)
    {
        if ($this->enabled === '1' && (is_null($this->$attribute) || empty($this->$attribute))) {
            $this->addError($this->getAttributeLabel($attribute), $this->getAttributeLabel($attribute).' cannot be blank');
        }
    }

    public function outOfOfficeDurationValidator($attribute, $params)
    {
        if ($this->enabled === '1' && !empty($this->to_date) && ($this->to_date < $this->from_date)) {
            $this->addError('Out of office duration', 'To date cannot be before '.date('d M Y', strtotime($this->from_date)));
        }
    }

    /**
     * Check if the user is out of office and check for alternate user
     *
     * @param $userid
     * @return string|null
     */
    public function checkUserOutOfOffice($userid)
    {
        $message = null;
        if ($user = self::model()->find('user_id= :user_id', ['user_id' => $userid])) {
            $now = date('Y-m-d');
            if ($user->enabled === '1' && $now > $user->from_date && $now < $user->to_date) {
                $message = $user->user->getFullnameAndTitle()." is currently out of office. You can instead send message to ".$user->alternate_user->getFullnameAndTitle();
            }
        }
        return $message;
    }
}
