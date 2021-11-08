<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */


namespace OEModule\OphCiExamination\models;


use OEModule\OphCiExamination\widgets\FreehandDraw as FreehandDrawWidget;

/**
 * Class FreehandDraw
 *
 * @package OEModule\OphCiExamination\models
 *
 * @property int $id
 * @property int $event_id
 *
 * @property \Event $event
 * @property FreehandDraw_Entry[] $entries
 * @property \EventType $eventType
 * @property \User $user
 * @property \User $usermodified
 */
class FreehandDraw extends \BaseEventTypeElement
{
    use traits\CustomOrdering;
    protected $auto_update_relations = true;
    protected $widgetClass = FreehandDrawWidget::class;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return the static model class
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
        return 'et_ophciexamination_freehand_draw';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            ['event_id, entries', 'safe'],
            ['id, event_id', 'safe', 'on' => 'search'],
        ];
    }

    /**
     * @inherit
     */
    public function relations()
    {
        return [
            'eventType' => [self::BELONGS_TO, 'EventType', 'event_type_id'],
            'event' => [self::BELONGS_TO, 'Event', 'event_id'],
            'user' => [self::BELONGS_TO, 'User', 'created_user_id'],
            'usermodified' => [self::BELONGS_TO, 'User', 'last_modified_user_id'],
            'entries' => [
                self::HAS_MANY,
                'OEModule\OphCiExamination\models\FreehandDraw_Entry',
                'element_id',
            ],
        ];
    }
}
