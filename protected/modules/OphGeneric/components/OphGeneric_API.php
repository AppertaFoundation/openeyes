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
 * @copyright Copyright (C) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphGeneric\components;

use OEModule\OphGeneric\models\HFA;

class OphGeneric_API extends \BaseAPI
{
    public $show_if_both_eyes_are_null = false;
    const BODY_SITE_SNOMED_LEFT_EYE = 8966001;
    const BODY_SITE_SNOMED_RIGHT_EYE = 18944008;
    const BODY_SITE_SNOMED_BOTH_EYES = 40638003;

    public function getLaterality($event_id)
    {
        $left_eye = false;
        $right_eye = false;

        $event_attachment_groups = \EventAttachmentGroup::model()->findAll('event_id = ?', [$event_id]);
        foreach ($event_attachment_groups as $event_attachment_group) {
                $event_attachment_items = \EventAttachmentItem::model()->findAll(
                    'event_attachment_group_id = :event_attachment_group_id',
                    [
                    ':event_attachment_group_id' => $event_attachment_group->id,
                    ]
                );
            foreach ($event_attachment_items as $event_attachment_item) {
                $attachmentData = $event_attachment_item->attachmentData;
                if ($attachmentData) {
                    $body_site_snomed_type = $attachmentData->body_site_snomed_type;
                    switch ($body_site_snomed_type) {
                        case self::BODY_SITE_SNOMED_LEFT_EYE:
                            $left_eye = true;
                            break;
                        case self::BODY_SITE_SNOMED_RIGHT_EYE:
                            $right_eye = true;
                            break;
                        case self::BODY_SITE_SNOMED_BOTH_EYES:
                            $left_eye = true;
                            $right_eye = true;
                            break;
                    }
                }
            }
        }

        $eye_name = null;
        if ($left_eye && $right_eye) {
            $eye_name = 'Both';
        } elseif ($left_eye) {
            $eye_name = 'Left';
        } elseif ($right_eye) {
            $eye_name = 'Right';
        }

        return \Eye::model()->findByAttributes(['name' => $eye_name]);
    }

    public function getVfiFor(\Patient $patient, $use_context = false)
    {
        $hfas = $this->getElements(HFA::class, $patient, $use_context);

        return array_map([$this, 'formatHfaForVfiResults'], $hfas);
    }

    public function getElements($element, \Patient $patient, $use_context = false, $before = null, $criteria = null): array
    {
        return parent::getElements($this->namespaceElementName($element), $patient, $use_context, $before, $criteria);
    }

    protected function formatHfaForVfiResults(HFA $element)
    {
        $result = [];
        foreach ($element->hfaEntry as $entry) {
            $side = ((int) $entry->eye_id) === \Eye::RIGHT ? 'right' : 'left';
            $result[$side . '_vfi'] = $entry->visual_field_index;
        }
        $result['eventdate'] = $element->event->event_date;

        return $result;
    }

    private function namespaceElementName($element)
    {
        if (strpos($element, 'models') == 0) {
            $element = 'OEModule\OphGeneric\\' . $element;
        }
        return $element;
    }
}
