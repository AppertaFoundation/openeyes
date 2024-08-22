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

namespace OEModule\OphCiExamination\models;

use OEModule\OphCiExamination\models\interfaces\SidedData;
use OEModule\OphCiExamination\models\traits\HasSidedData;

/**
 * Class StrabismusManagement_Entry
 *
 * @package OEModule\OphCiExamination\models
 * @property int $id
 * @property string $treatment
 * @property string $treatment_options
 * @property string $treatment_reason
 * @property int $eye_id
 */
class StrabismusManagement_Entry extends \BaseElement implements SidedData
{
    use HasSidedData;

    public function tableName()
    {
        return 'ophciexamination_strabismusmanagement_entry';
    }

    public function rules()
    {
        return [
            ['eye_id, treatment, treatment_options, treatment_reason', 'safe'],
            ['treatment', 'required']
        ];
    }

    public function relations()
    {
        return [
            'element' => [self::BELONGS_TO, StrabismusManagement::class, 'element_id'],
            'user' => [self::BELONGS_TO, \User::class, 'created_user_id'],
            'usermodified' => [self::BELONGS_TO, \User::class, 'last_modified_user_id'],
        ];
    }

    public function __toString()
    {
        return sprintf(
            "%s %s",
            $this->getLateralityString(),
            implode(
                " ",
                array_filter(
                    $this->getAttributes(['treatment', 'treatment_options', 'treatment_reason']),
                    function ($value) {
                        return strlen(trim($value)) > 0;
                    }
                )
            )
        );
    }

    public function sidedFields(?string $side = null): array
    {
        return [];
    }

    public function sidedDefaults(): array
    {
        return [];
    }

    protected function getLateralityString()
    {
        if (!$this->hasRight()) {
            return 'Left';
        }
        if (!$this->hasLeft()) {
            return 'Right';
        }
        return 'Bilateral';
    }

    public function __clone()
    {
        $this->unsetAttributes(['id', 'element_id']);
        $this->setIsNewRecord(true);
    }
}
