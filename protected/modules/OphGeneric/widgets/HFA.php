<?php
/**
 * OpenEyes.
 *
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

use OELog;
use OEModule\OphGeneric\models\HFA as HFAElement;
use OEModule\OphGeneric\components\EventManager;

class HFA extends \BaseEventElementWidget
{
    public static $moduleName = 'OphGenericModule';

    protected function getNewElement()
    {
        return new HFAElement();
    }

    protected function getView()
    {
        if ($this->element->isNewRecord) {
            return 'HFA_event_edit';
        }

        if (EventManager::forEvent($this->element->event)->isManualEvent()) {
            return parent::getView();
        }

        return 'HFA_event_view';
    }

    protected function ensureRequiredDataKeysSet(&$data)
    {
        $data['hfaEntry'] = array_values(
            array_filter(
                $data['hfaEntry'] ?? [],
                function ($entry) {
                    return !empty($entry['mean_deviation']) || !empty($entry['visual_field_index']);
                }
            )
        );

        $data['eye_id'] = count($data['hfaEntry']) > 1
            ? \Eye::BOTH
            : (
                count($data['hfaEntry']) > 0
                    ? $data['hfaEntry'][0]['eye_id']
                    : null
                );
    }
}
