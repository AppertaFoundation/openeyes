<?php

/**
 * (C) Copyright Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphGeneric\widgets;

use OEModule\OphGeneric\models\Attachment;
use OEModule\OphGeneric\components\EventManager;

class AttachmentElement extends \BaseEventElementWidget
{
    public static $moduleName = 'OphGeneric';
    // Future development will be to add a dedicated print view to handle printing the attachments in a similar manner to the OphCoDocument Event
    // For now this element does not have a dedicated print view
    // protected $print_view = 'AttachmentElement_event_print';

    protected function getNewElement()
    {
        return new Attachment();
    }

    protected function getView()
    {
        if ($this->element->isNewRecord || EventManager::forEvent($this->element->event)->isManualEvent())  {
            return 'NoManualData_event_view';
        }

        return parent::getView();
    }
}
