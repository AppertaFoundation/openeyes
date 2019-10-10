<?php
/**
 * (C) OpenEyes Foundation, 2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
abstract class EpisodeSummaryWidget extends CWidget
{
    public $episode;
    public $event_type;
    public $patient;
    public $subspecialty;

    /**
     * Can this summary block be collapsed?
     *
     * @var bool
     */
    public $collapsible = false;

    /**
     * Is this summary block open on page load? This is only used for collapsible blocks.
     *
     * @var bool
     */
    public $openOnPageLoad = false;

    protected function sortData($item1, $item2){
        if ($item1['x'] == $item2['x']) return 0;
        return $item1['x'] < $item2['x'] ? -1 : 1;
    }

    public function run_right_side(){

    }

    public function run_oescape($widgets_no = 1){

    }

    public function getOpnoteEvent(){
        $opnote_marking = array('right'=>array(), 'left'=>array());
        $special_procedure_list = ["Insertion of aqueous shunt"=>"Aqueous Shunt"];

        $marking_list = array();
        $subspecialty = Subspecialty::model()->findByAttributes(array('name'=>'Glaucoma'));
        if ($subspecialty) {
            foreach (Procedure::model()->getListBySubspecialty($subspecialty->id, false) as $proc_id => $name) {
                $marking_list[] = $name;
            }
        }
        $event_opnpte = EventType::model()->find('class_name=?', array('OphTrOperationnote'));
        $events = Event::model()->getEventsOfTypeForPatient($event_opnpte, $this->patient);
        $eye_side_list = [Eye::LEFT =>['left'], Eye::RIGHT=>['right'], Eye::BOTH=>['left', 'right']];

        foreach ($events as $event) {
            if (($proc_list = $event->getElementByClass('Element_OphTrOperationnote_ProcedureList'))) {
                $timestamp = Helper::mysqlDate2JsTimestamp($event->event_date);
                $eye_side = $eye_side_list[$proc_list->eye->id];
                foreach ($eye_side as $side) {
                    foreach ($proc_list->procedures as $proc) {
                        if (in_array($proc->term, $marking_list)) {
                            if (array_key_exists($proc->term, $special_procedure_list)) {
                                $proc->short_format = $special_procedure_list[$proc->term];
                            }
                            if (empty($opnote_marking[$side])||!array_key_exists($proc->short_format, $opnote_marking[$side])) {
                                $opnote_marking[$side][$proc->short_format] = array();
                            }
                            $opnote_marking[$side][$proc->short_format][]=$timestamp;
                        }
                    }
                }
            }
        }

        return $opnote_marking;
    }


    public function getLaserEvent() {
        $laser_marking = array('right'=>array(), 'left'=>array());

        $event_laser = EventType::model()->find('class_name=?', array('OphTrLaser'));
        $events = Event::model()->getEventsOfTypeForPatient($event_laser, $this->patient);

        foreach ($events as $event) {
            $timestamp = Helper::mysqlDate2JsTimestamp($event->event_date);
            if ($proc_list = $event->getElementByClass('Element_OphTrLaser_Treatment') ) {
                foreach ($proc_list->left_procedures as $left_procedure) {
                    if (empty($laser_marking['left'])||!array_key_exists($left_procedure->short_format, $laser_marking['left'])) {
                        $laser_marking['left'][$left_procedure->short_format] = array();
                    }
                    $laser_marking['left'][$left_procedure->short_format][] = $timestamp;
                }
                foreach ($proc_list->right_procedures as $right_procedure) {
                    if (empty($laser_marking['right'])||!array_key_exists($right_procedure->short_format, $laser_marking['right'])) {
                        $laser_marking['right'][$right_procedure->short_format] = array();
                    }
                    $laser_marking['right'][$right_procedure->short_format][] = $timestamp;
                }
            }
        }
        return $laser_marking;
    }

}
