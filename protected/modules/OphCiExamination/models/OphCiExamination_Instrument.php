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

namespace OEModule\OphCiExamination\models;

use BaseActiveRecordVersioned;

use MappedReferenceData;
use ReferenceData;
use LookupTable;

use OE\factories\models\traits\HasFactory;

use Institution;

/**
 * This is the model class for table "ophciexamination_instrument".
 *
 * @property int $id
 * @property string $name
 * @property string $short_name
 * @property int $display_order
 */
class OphCiExamination_Instrument extends BaseActiveRecordVersioned
{
    use MappedReferenceData;
    use HasFactory;

    protected function getSupportedLevels(): int
    {
        return ReferenceData::LEVEL_INSTITUTION | ReferenceData::LEVEL_INSTALLATION;
    }

    protected function mappingColumn(int $level): string
    {
        return 'instrument_id';
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophciexamination_instrument';
    }

    public function defaultScope()
    {
        return ['order' => $this->getTableAlias(true, false).'.display_order'];
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            ['name', 'required'],
            ['name, short_name, active', 'validateInstrumentNotChangedWhenSharedUnlessInstallationAdmin',  'except' => 'installationAdminSave'],
            ['id, name, short_name', 'safe', 'on' => 'search'],
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return [
            'scale' => [self::BELONGS_TO, OphCiExamination_Qualitative_Scale::class, 'scale_id'],
            'instrument_institution' => [self::HAS_MANY, OphCiExamination_Instrument_Institution::class, 'instrument_id'],
            'institutions' => [self::MANY_MANY, Institution::class, 'ophciexamination_instrument_institution(instrument_id,institution_id)'],
        ];
    }

    public function behaviors()
    {
        return [
            'LookupTable' => LookupTable::class,
        ];
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
        $criteria->compare('short_name', $this->short_name, true);

        return new \CActiveDataProvider(get_class($this), [
            'criteria' => $criteria,
        ]);
    }

    public function validateInstrumentNotChangedWhenSharedUnlessInstallationAdmin($attribute, $params)
    {
        if ($this->isAttributeDirty($attribute) && count($this->institutions) > 1) {
            $this->addError('name', 'Cannot change the ' . $attribute . ' of a shared instrument unless you are an installation admin');
        }
    }
}
