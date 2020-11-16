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

namespace OEModule\OphCoMessaging\models;

/**
 * This is the model class for table "ophcomessaging_message_copyto_users"
 *
 * The following are the available columns in table:
 *
 * @property string $id
 * @property string $element_id
 * @property string $user_id
 */

class OphCoMessaging_Message_CopyTo_Users extends \BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return the static model class
     */
    public static function model($classname = __CLASS__)
    {
        return parent::model($classname);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophcomessaging_message_copyto_users';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('element_id, user_id', 'safe'),
            array('element_id, user_id', 'required'),
            array('id, element_id, user_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules
     */
    public function relations()
    {
        return array(
            'element' => array(self::BELONGS_TO, 'OEModule\\OphCoMessaging\\models\\Element_OphCoMessaging_Message', 'element_id'),
            'user' => array(self::BELONGS_TO, 'User', 'user_id'),
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return \CActiveDataProvider
     */
    public function search()
    {
        $criteria = new \CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('element_id', $this->element_id, true);
        $criteria->compare('user_id', $this->user_id, true);

        return new \CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }
}
