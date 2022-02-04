<?php
/**
 * (C) Apperta Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * The followings are the available columns in table 'ophtropbooking_waiting_list_contact_rule_institution':.
 *
 * @property int $id
 * @property int $waiting_list_contact_rule_id
 * @property int $institution_id
 */

class OphTrOperationbooking_Waiting_List_Contact_Rule_Institution extends BaseActiveRecordVersioned
{
    public static function model($class_name = __CLASS__)
    {
        return parent::model($class_name);
    }

    public function tableName()
    {
        return 'ophtropbooking_waiting_list_contact_rule_institution';
    }

    public function rules()
    {
        return [
            ['id, waiting_list_contact_rule_id, institution_id', 'safe', 'on' => 'search'],
        ];
    }

    public function relations()
    {
        return [
            'contact_rule' => [self::BELONGS_TO, 'OphTrOperationbooking_Waiting_List_Contact_Rule', 'waiting_list_contact_rule_id'],
            'institution' => [self::BELONGS_TO, 'Institution', 'institution_id'],
        ];
    }
}
