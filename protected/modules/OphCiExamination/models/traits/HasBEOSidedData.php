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

namespace OEModule\OphCiExamination\models\traits;

use OEModule\OphCiExamination\models\interfaces\BEOSidedData;

/**
 * Trait HasBEOSidedData
 *
 * Provides functionality for elements supporting beo records.
 * Use in conjunction with BEOSidedData Interface.
 *
 * @package OEModule\OphCiExamination\models\traits
 */
trait HasBEOSidedData
{
    use HasSidedData {
        sideStrings as baseSideStrings;
        sideAttributeValidation as baseSideAttributeValidation;
    }

    public function sideStrings()
    {
        return array_merge(['beo'], $this->baseSideStrings());
    }

    public function hasBeo()
    {
        return $this->eye_id && ((int)$this->eye_id & BEOSidedData::BEO) === BEOSidedData::BEO;
    }

    public function setHasBeo()
    {
        $this->eye_id |= BEOSidedData::BEO;
    }

    public function setDoesNotHaveBeo()
    {
        if ($this->hasBeo()) {
            $this->eye_id ^= BEOSidedData::BEO;
        }
    }

    public function sideAttributeValidation($attribute, $params)
    {
        if ((int)$this->$attribute < 1 || (int)$this->$attribute > 7) {
            $this->addError($attribute, $params['message'] ?? '{attribute} is invalid.');
        }
    }
}
