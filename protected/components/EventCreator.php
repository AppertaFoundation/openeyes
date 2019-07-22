<?php
/**
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class EventCreator extends \CModel
{
    /**
     * @var \Episode
     */
    public $episode;

    /**
     * @var \Event
     */
    public $event;

    public $elements = [];

    public $patient;

    /**
     * @inheritDoc
     */
    public function attributeNames()
    {
        return [
            'event_type_id' => 'Event Type'
        ];
    }

    public function rules()
    {
        return [];
    }

    public function __construct($episode, $event_type_id)
    {
        $this->episode = $episode;
        $this->event = $this->createDefaultEvent($event_type_id);
    }

    protected function createDefaultEvent($event_type_id)
    {
        $event = new \Event();
        $event->event_type_id = $event_type_id;
        $event->episode_id = $this->episode->id;
        $event->created_user_id = \Yii::app()->user->id;
        $event->last_modified_user_id = \Yii::app()->user->id;
        $event->last_modified_date = date('Y-m-d H:i:s');
        $event->created_date = date('Y-m-d H:i:s');
        $event->is_automated = 1;
        $event->automated_source = 'Auto generated event from EventCreator';

        return $event;
    }

    public function save()
    {
        if ($this->event->save()) {
            return $this->saveElements($this->event->id);
        } else {
            $this->addErrors($this->event->getErrors());
            \OELog::log("Event: " . print_r($this->event->getErrors(), true));
        }

        return false;
    }
}
