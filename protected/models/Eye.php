<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OE\factories\models\traits\HasFactory;

/**
 * This is the model class for table "eye".
 *
 * The followings are the available columns in table 'eye':
 *
 * @property int $id
 * @property string $name
 * @property string $ShortName
 */
class Eye extends BaseActiveRecord
{
    use HasFactory;

    const LEFT = 1;
    const RIGHT = 2;
    const BOTH = 3;

    public static function getIdFromName($side)
    {
        return strtolower($side) === "left" ? self::LEFT : (
                strtolower($side) === "right" ? self::RIGHT : (
                strtolower($side) === "both" ? self::BOTH : null
            )
        );
    }

    /**
     * Simple helper method to ensure that any change to Eye names doesn't affect the method names derived from
     * decisions based on the Eye.
     *
     * @param $id
     * @return mixed
     */
    public static function methodPostFix($id)
    {
        return array(
            static::LEFT => 'Left',
            static::RIGHT => 'Right',
            static::BOTH => 'Both'
        )[$id];
    }
    /**
     * Returns the static model of the specified AR class.
     *
     * @return Eye the static model class
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
        return 'eye';
    }

    public function defaultScope()
    {
        return array('order' => $this->getTableAlias(true, false) . '.display_order');
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
        );
    }

    public function getShortName()
    {
        return substr($this->name, 0, 1);
    }

    public function getAdjective()
    {
        if ($this->id == self::BOTH) {
            return 'Bilateral';
        }

        return $this->name;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}
