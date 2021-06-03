<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2020, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * This is the model for table "sso_default_user_roles".
 *
 * The following are the variable columns in table 'sso_default_user_roles':
 *
 * @property int $sso_user_id
 * @property int $roles
 */

class SsoDefaultRoles extends BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return SsoDefaultRoles the static model class
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
        return 'sso_default_user_roles';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('sso_user_id, roles', 'safe', 'on' => 'search'),
        );
    }

    public function primaryKey()
    {
        return 'roles';
    }

    public function relations()
    {
        return array(
            'sso_default_rights' => array(self::BELONGS_TO, 'sso_default_user_rights', 'sso_user_id'),
            'sso_roles_list' => array(self::HAS_ONE, 'AuthItem', 'name')
        );
    }
}
