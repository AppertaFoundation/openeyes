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

use OEModule\OphCiExamination\widgets\NinePositions as NinePositionsWidget;

/**
 * Class NinePositions
 *
 * @package OEModule\OphCiExamination\models
 * @property NinePositions_Reading[] $readings
 */
class NinePositions extends \BaseEventTypeElement
{
    use traits\CustomOrdering;
    use traits\HasChildrenWithEventScopeValidation;

    protected $widgetClass = NinePositionsWidget::class;
    protected $auto_update_relations = true;
    protected $auto_validate_relations = true;

    protected const EVENT_SCOPED_CHILDREN = [
        'readings' => 'with_head_posture',
    ];

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'et_ophciexamination_ninepositions';
    }

    public function rules()
    {
        return [
            ['event_id, readings', 'safe'],
            ['readings', 'required']
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
            'readings' => [self::HAS_MANY, NinePositions_Reading::class, 'element_id']
        ];
    }

    public function canCopy()
    {
        return true;
    }

    public function beforeDelete()
    {
        foreach($this->readings as $reading){
            $reading->delete();
        }
        return parent::beforeDelete();
    }
}
