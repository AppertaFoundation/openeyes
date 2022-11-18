<?php
/**
 * OpenEyes.
 *
 *
 * Copyright OpenEyes Foundation, 2017
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * This was the original manager for a basic pub/sub model in OpenEyes.
 * It's now been superceded by the OESysEvent module and will be removed in
 * version 7 of OpenEyes.
 *
 * @deprecated since 6.5.0
 */
class OEEventManager extends CApplicationComponent
{
    public $observers;

    public function dispatch($event_id, $params = array())
    {
        $observers = isset($this->observers[$event_id]) ? $this->observers[$event_id] : array();
        foreach ($observers as $observer) {
            $class_name = $observer['class'];
            $method = $observer['method'];
            $object = new $class_name();
            $return = $object->$method($params);
        }
    }
}
