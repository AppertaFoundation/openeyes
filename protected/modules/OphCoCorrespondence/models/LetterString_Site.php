<?php
/**
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * The followings are the available columns in table 'ophcocorrespondence_letter_string_site':.
 *
 * @property int $id
 * @property int $letter_string_id
 * @property int $site_id
 */

class LetterString_Site extends BaseActiveRecordVersioned
{
    public static function model($class_name = __CLASS__)
    {
        return parent::model($class_name);
    }

    public function tableName()
    {
        return 'ophcocorrespondence_letter_string_site';
    }

    public function rules()
    {
        return [
            ['id, letter_string_id, site_id', 'safe', 'on' => 'search'],
        ];
    }

    public function relations()
    {
        return [
            'letterstring' => [self::BELONGS_TO, 'LetterString', 'letter_string_id'],
            'site' => [self::BELONGS_TO, 'Site', 'site_id'],
        ];
    }
}
