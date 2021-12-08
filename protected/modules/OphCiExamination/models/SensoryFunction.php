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

use OEModule\OphCiExamination\widgets\SensoryFunction as SensoryFunctionWidget;

/**
 * Class SensoryFunction
 *
 * @package OEModule\OphCiExamination\models
 * @property array $entries
 */
class SensoryFunction extends \BaseEventTypeElement
{
    use traits\CustomOrdering;
    use traits\HasChildrenWithEventScopeValidation;

    protected $widgetClass = SensoryFunctionWidget::class;
    protected $auto_update_relations = true;
    protected $auto_validate_relations = true;

    protected const EVENT_SCOPED_CHILDREN = [
        'entries' => 'with_head_posture'
    ];

    public function tableName()
    {
        return 'et_ophciexamination_sensoryfunction';
    }

    public function rules()
    {
        return [
            ['event_id, entries', 'safe'],
            ['entries', 'required']
        ];
    }

    /**
     * @return array
     */
    public function relations()
    {
        return [
            'event' => [self::BELONGS_TO, 'Event', 'event_id'],
            'user' => [self::BELONGS_TO, 'User', 'created_user_id'],
            'usermodified' => [self::BELONGS_TO, 'User', 'last_modified_user_id'],
            'entries' => [self::HAS_MANY, SensoryFunction_Entry::class, 'element_id']
        ];
    }

    public function getLetter_string()
    {
        $prefix = "Sensory Function:";
        if (count($this->entries) === 0) {
            return "$prefix No entries";
        }

        return implode("\n",
            array_map(function ($entry) use ($prefix) {
                return "$prefix $entry";
            }, $this->entries));
    }

    public function beforeDelete()
    {
        foreach($this->entries as $entry){
            $entry->delete();
        }
        return parent::beforeDelete();
    }
}