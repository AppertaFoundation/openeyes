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
 * This is the model class for table "finding".
 *
 * @property int $id
 * @property string $name
 * @property int $display_order
 * @active integer $active
 */
class Finding extends \BaseActiveRecordVersioned
{
    use HasFactory;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return OphCiExamination_FurtherFindings the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    protected $auto_update_relations = true;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'finding';
    }

    public function defaultScope()
    {
        return array('order' => $this->getTableAlias(true, false).'.display_order');
    }

    /**
     * @return array validation rules for model OphCiExamination_FurtherFindings.
     */
    public function rules()
    {
        return array(
                array('name, display_order', 'required'),
                array('subspecialties', 'safe'),
                array('id, name, display_order, active', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'subspecialties' => array(self::MANY_MANY, 'Subspecialty',
                'findings_subspec_assignment(finding_id, subspecialty_id)', ),
        );
    }

    public function behaviors()
    {
        return array(
            'LookupTable' => 'LookupTable',
        );
    }

    /**
     * @return string
     */
    public function canAutocomplete()
    {
        return true;
    }

    public function getTerm()
    {
        return $this->name;
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new \CDbCriteria();
        $criteria->compare('id', $this->id, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('display_order', $this->display_order, true);
        $criteria->compare('active', $this->active, true);

        return new \CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }

    /**
     * @param Subspecialty $subspecialty
     *
     * @return Finding
     */
    public function bySubspecialty(Subspecialty $subspecialty)
    {
        $criteria = array(
            'join' => 'left join findings_subspec_assignment fsa on fsa.finding_id = t.id',
            'condition' => 'fsa.subspecialty_id is null',
        );
        if ($subspecialty) {
            $criteria['condition'] .= ' OR fsa.subspecialty_id = :subspecialty_id';
            $criteria['params'] = array(':subspecialty_id' => $subspecialty->id);
        }
        $this->getDbCriteria()->mergeWith($criteria);

        return $this;
    }
}
