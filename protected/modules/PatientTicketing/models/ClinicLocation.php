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

use BaseActiveRecordVersioned;
use Institution;
use MappedReferenceData;
use ReferenceData;

class ClinicLocation extends BaseActiveRecordVersioned
{
    use MappedReferenceData;

    protected function getSupportedLevels(): int
    {
        return ReferenceData::LEVEL_INSTITUTION;
    }

    protected function mappingColumn(int $level): string
    {
        return 'queueset_id';
    }
    public function tableName()
    {
        return 'patientticketing_clinic_location';
    }

    public function rules()
    {
        return array(
            array('name', 'required'),
        );
    }

    public function relations()
    {
        return array(
            'clinic_location_institutions' => array(self::HAS_MANY, ClinicLocation_Institution::class, 'clinic_location_id'),
            'institutions' => array(self::MANY_MANY, Institution::class, 'patientticketing_clinic_location_institution(clinic_location_id, institution_id)')
        );
    }
}
