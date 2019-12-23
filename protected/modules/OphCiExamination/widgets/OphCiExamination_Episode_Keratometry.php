<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
use OEModule\OphCiExamination\models;

class OphCiExamination_Episode_Keratometry extends \EpisodeSummaryWidget
{
    public function run()
    {
        $kera_events = [];
        // TODO: should be using API methods for this.
        foreach (Event::model()->getEventsOfTypeForPatient($this->event_type, $this->episode->patient) as $event) {
            $kera_models = models\Element_OphCiExamination_Keratometry::model()->findAll('event_id = ' . $event->id);
            foreach ($kera_models as $kera_model) {
                if ($kera_model) {
                    $kera_events[] = $kera_model->attributes;
                }
            }
        }
        $this->render('OphCiExamination_Episode_Keratometry', array('keratometry' => $kera_events));
    }
}
