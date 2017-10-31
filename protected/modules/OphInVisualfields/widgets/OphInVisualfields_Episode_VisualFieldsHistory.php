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
class OphInVisualfields_Episode_VisualFieldsHistory extends EpisodeSummaryWidget
{
    public function run()
    {
        $data = array();

        $events = Event::model()->findAll(
            array(
                'join' => 'inner join episode ep on ep.id = t.episode_id',
                'condition' => 't.event_type_id = :event_type_id and ep.patient_id = :patient_id',
                'order' => 't.event_date',
                'params' => array(
                    ':event_type_id' => $this->event_type->id,
                    ':patient_id' => $this->episode->patient->id,
                ),
            )
        );

        if ($events) {
            $data['start_date'] = strtotime(reset($events)->created_date);
            $data['end_date'] = strtotime(end($events)->created_date);
        }

        $data['elements'] = array();
        $element_ids = array();
        foreach ($events as $event) {
            if ($element = $event->getElementByClass('Element_OphInVisualfields_Image')) {
                $data['elements'][] = $element;
                $element_ids[] = $element->id;
            } else {
                Yii::log("Visual Field Event $event->id has no Image element");
            }
        }

        Yii::app()->assetManager->registerScriptFile('jquery-mousewheel/jquery.mousewheel.js', 'application.assets.components');

        Yii::app()->assetManager->registerScriptFile('js/module.js', 'application.modules.OphInVisualfields.assets');

        Yii::app()->clientScript->registerScript(
            'OphInVisualfields_Episode_VisualFieldsHistory_element_ids',
            'var OphInVisualfields_Episode_VisualFieldsHistory_element_ids = '.CJSON::encode($element_ids),
            CClientScript::POS_END
        );

        $this->render(__CLASS__, $data);
    }
}
