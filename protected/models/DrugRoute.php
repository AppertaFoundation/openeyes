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
 * This is the model class for table "drug_route".
 *
 * The followings are the available columns in table 'drug_route':
 *
 * @property int $id
 * @property string $name
 * @property DrugRouteOption[] $options
 */
class DrugRoute extends BaseActiveRecordVersioned
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'drug_route';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('name', 'required'),
        );
    }

    public function behaviors()
    {
        return array(
            'LookupTable' => 'LookupTable',
        );
    }

    /**
     * Get active options for this route.
     *
     * @param int $id Also retrieve the option matching this id if passed
     *
     * @return DrugRouteOption[]
     */
    public function getOptions($id = null)
    {
        $crit = new CDbCriteria();
        $crit->compare('active', true);
        $crit->compare('id', $id, false, 'OR');
        $crit->compare('drug_route_id', $this->id);

        return DrugRouteOption::model()->findAll($crit);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}
