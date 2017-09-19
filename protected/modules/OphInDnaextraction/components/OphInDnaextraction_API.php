<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class OphInDnaextraction_API extends BaseAPI
{
    public $createOprn = 'OprnEditDNAExtraction';
    public $createOprnArgs = array('user_id', 'firm', 'episode');

    public function getEventsByPatient($patient)
    {
        $events = array();
        $episodes = $patient->episodes;
        foreach ($episodes as $episode) {
            foreach ($this->getEventsInEpisode($patient, $episode) as $key => $event) {
                $events[] = $event;
            }
        }

        return $events;
    }

    public function volumeRemaining($event_id)
    {
        if (!$element = Element_OphInDnaextraction_DnaExtraction::model()->find('event_id = ?', array($event_id))) {
            throw new CHttpException(403, 'Invalid event id.');
        }
        $used_volume = 0;
        $volume = intval($element->volume);
        $transactions_element = Element_OphInDnaextraction_DnaTests::model()->find('event_id = ?', array($event_id));
        $transactions = OphInDnaextraction_DnaTests_Transaction::model()->findAll('element_id = ?', array($transactions_element->id));
        foreach ($transactions as $transaction) {
            $used_volume += $transaction->volume;
        }

        return $volume - $used_volume;
    }
}
