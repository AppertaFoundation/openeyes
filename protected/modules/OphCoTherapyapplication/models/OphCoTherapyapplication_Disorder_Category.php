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

/**
 * Enables categorisation of the disorders so that default selection of choices can be made in the decision tree depending on what diagnosis
 * has been made for a given eye.
 */
class OphCoTherapyapplication_Disorder_Category extends BaseActiveRecordVersioned
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
        return 'ophcotherapya_disorder_category';
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
                'therapydisorders' => array(self::MANY_MANY, 'OphCoTherapyapplication_TherapyDisorder', 'ophcotherapya_therapydisorder_category(category_id,therapydisorder_id)'),
        );
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
                array('label', 'safe'),
                array('label', 'required'),
                // The following rule is used by search().
                // Please remove those attributes that should not be searched.
                array('id, label', 'safe', 'on' => 'search'),
        );
    }
}
