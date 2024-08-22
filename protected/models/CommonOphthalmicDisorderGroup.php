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
 * This is the model class for table "common_ophthalmic_disorder_group".
 *
 * The followings are the available columns in table 'common_ophthalmic_disorder_group':
 *
 * @property int $id
 * @property string $name
 * @property int $display_order
 * @property Institution $institution
 * @property int $subspecialty_id
 */
class CommonOphthalmicDisorderGroup extends BaseActiveRecordVersioned
{
    use HasFactory;
    use OwnedByReferenceData;

    protected function getSupportedLevelMask(): int
    {
        return ReferenceData::LEVEL_SUBSPECIALTY | ReferenceData::LEVEL_INSTALLATION | ReferenceData::LEVEL_INSTITUTION;
    }

    protected function mappingColumn(int $level): string
    {
        return $this->tableName() . '_id';
    }


    public function tableName()
    {
        return 'common_ophthalmic_disorder_group';
    }

    public function rules()
    {
        return array(
            array('name', 'required'),
            array('institution_id', 'safe'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'institution' => array(self::BELONGS_TO, 'Institution', 'institution_id'),
            'subspecialty' => array(self::BELONGS_TO, 'Subspecialty', 'subspecialty_id'),
        );
    }

    public function defaultScope()
    {
        return array('order' => $this->getTableAlias(true, false) . '.display_order');
    }

    /** Expands the reference level assignment for this group to be part of the name */
    public function getFully_qualified_name()
    {
        $name = $this->name . ' - ';
        if ($this->institution) {
            return $name . $this->institution->short_name;
        }

        return $name . 'All';
    }
}
