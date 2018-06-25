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

        $marking_list = array(
            'Phacoemulsification',
            'Phacoemulsification and Intraocular lens',
            'Trabeculectomy',
            'Argon laser trabeculoplasty',
            'Selective laser trabeculoplasty',
            'Panretinal photocoagulation',
            'Cycloablation',
            'Cyclodialysis cleft repair',
            'Peripheral iridectomy'
        );


        $event_opnpte = EventType::model()->find('class_name=?', array('OphTrOperationnote'));
        $events = Event::model()->getEventsOfTypeForPatient($event_opnpte ,$this->episode->patient);

        foreach ($events as $event) {
            if (($proc_list = $event->getElementByClass('Element_OphTrOperationnote_ProcedureList'))) {
                $timestamp = Helper::mysqlDate2JsTimestamp($event->event_date);
                $eye_side = array();
                switch ($proc_list->eye->name) {
                    case 'Left':
                        array_push($eye_side, 'left');
                        break;
                    case 'Right':
                        array_push($eye_side, 'right');
                        break;
                    case 'Both':
                        array_push($eye_side, 'left');
                        array_push($eye_side, 'right');
                        break;
                    default:
                        break;
                }
                foreach ($eye_side as $side){
                    foreach ($proc_list->procedures as $proc){
                        if (in_array($proc->term, $marking_list)){
                            if (empty($opnote_marking[$side])||!in_array($proc->short_format, $opnote_marking[$side])){
                                $opnote_marking[$side][$proc->short_format] = array();
                            }
                            array_push($opnote_marking[$side][$proc->short_format], $timestamp);
                        }
                    }
                }
            }
        }

        return $opnote_marking;
    }

    public function getLaserEvent() {
        $laser_marking = array('right'=>array(), 'left'=>array());

        $laser_list = array(
            'Capsulotomy (YAG)',
            'Cycloablation',
            'Peripheral iridectomy',
        );

        $event_laser = EventType::model()->find('class_name=?', array('OphTrLaser'));
        $events = Event::model()->getEventsOfTypeForPatient($event_laser ,$this->episode->patient);

        foreach ($events as $event) {
            $timestamp = Helper::mysqlDate2JsTimestamp($event->event_date);
            if ($proc_list = $event->getElementByClass('Element_OphTrLaser_Treatment') ){
                foreach ($proc_list->left_procedures as $left_procedure) {
                    if (in_array($left_procedure->term, $laser_list)){
                        if (empty($laser_marking['left'])||!in_array($left_procedure->short_format, $laser_marking['left'])){
                            $laser_marking['left'][$left_procedure->short_format] = array();
                        }
                        array_push($laser_marking['left'][$left_procedure->short_format], $timestamp);
                    }
                }
                foreach ($proc_list->right_procedures as $right_procedure) {
                    if (in_array($right_procedure->term, $laser_list)){
                        if (empty($laser_marking['right'])||!in_array($right_procedure->short_format, $laser_marking['right'])){
                            $laser_marking['right'][$right_procedure->short_format] = array();
                        }
                        array_push($laser_marking['right'][$right_procedure->short_format], $timestamp);
                    }
                }
            }
        }

        return $laser_marking;
    }

}
