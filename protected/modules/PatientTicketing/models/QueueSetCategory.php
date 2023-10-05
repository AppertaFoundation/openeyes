<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\PatientTicketing\models;

use BaseActiveRecord;
use BaseActiveRecordVersioned;
use CActiveDataProvider;
use CDbCriteria;
use Institution;
use MappedReferenceData;
use OE\factories\models\traits\HasFactory;
use ReferenceData;

class QueueSetCategory extends BaseActiveRecordVersioned
{
    use MappedReferenceData;
    use HasFactory;

    protected function getSupportedLevels(): int
    {
        return ReferenceData::LEVEL_INSTITUTION;
    }

    protected function mappingColumn(int $level): string
    {
        return 'category_id';
    }
    /**
     * Returns the static model of the specified AR class.
     *
     * @return QueueSetCategory|BaseActiveRecord the static model class
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
        return 'patientticketing_queuesetcategory';
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
        return array(
                array('name', 'required'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'queuesets' => array(self::HAS_MANY, QueueSet::class, 'category_id'),
            'institutions' => array(self::MANY_MANY, Institution::class, 'patientticketing_queuesetcategory_institution(category_id, institution_id)'),
            'queuesetcategory_institutions' => array(self::HAS_MANY, QueueSetCategory_Institution::class, 'queueset_id'),
        );
    }

    public function behaviors()
    {
        return array(
                'LookupTable' => 'LookupTable',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('name', $this->name, true);

        return new CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }
}
