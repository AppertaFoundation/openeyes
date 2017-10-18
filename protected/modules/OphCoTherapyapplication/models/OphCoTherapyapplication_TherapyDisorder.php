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
class OphCoTherapyapplication_TherapyDisorder extends BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return the static model class
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
        return 'ophcotherapya_therapydisorder';
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
                'disorder' => array(self::BELONGS_TO, 'Disorder', 'disorder_id'),
        );
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
                array('disorder_id, display_order', 'safe'),
                array('disorder_id, display_order', 'required'),
                // The following rule is used by search().
                // Please remove those attributes that should not be searched.
                array('id, disorder_id, display_order', 'safe', 'on' => 'search'),
        );
    }

    public function getLevel2Disorders()
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'parent_id = :pid';
        $criteria->params = array('pid' => $this->id);
        $criteria->order = 'display_order asc';
        $disorders = array();

        foreach (self::model()->with('disorder')->findAll($criteria) as $therapy_disorder) {
            $disorders[] = $therapy_disorder->disorder;
        }

        return $disorders;
    }
}
