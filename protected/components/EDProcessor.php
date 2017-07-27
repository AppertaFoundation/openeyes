<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
class EDProcessor
{
    /**
     * @var CApplication
     */
    protected $app;

    /**
     * EDPostProcessor constructor.
     * @param CApplication|null $app
     */
    public function __construct(CApplication $app = null)
    {
        if ($app === null)
        {
            $app = Yii::app();
        }
        $this->app = $app;
    }

    /**
     * @param int $event_id
     */
    protected function clearEvent($event_id)
    {
        $this->app->db
            ->createCommand('DELETE FROM mview_datapoint_node where event_id = :eid')
            ->bindParam(':eid', $event_id)
            ->query();
    }

    /**
     * @param $event_id
     * @param $mnemonic
     * @param $side
     * @param $doodle
     */
    protected function storeDoodle($event_id, $mnemonic, $side, $doodle)
    {
        OELog::log(print_r($doodle, true));
        $param_map = array(
            'event_id' => $event_id,
            'eyedraw_class_mnemonic' => $doodle->subclass,
            'canvas_mnemonic' => $mnemonic,
            'placement_order' => $doodle->order,
            'laterality' => ($side === Eye::LEFT) ? 'L' : 'R',
            'content_json' => json_encode($doodle)
        );

        $placeholders = array();
        $i = 0;
        foreach ($param_map as $k => $v) {
            $placeholders[':p' . $i] = $v;
            $i++;
        }
        $cmd  = $this->app->db
            ->createCommand('INSERT INTO mview_datapoint_node (' . implode(',', array_keys($param_map)) . ')'
                . ' VALUES (' . implode(',', array_keys($placeholders)) . ')');

        foreach ($placeholders as $k => $v) {
            $cmd->bindValue($k, $v);
        }
        $cmd->query();
    }

    /**
     * @param $element_type_id
     * @return string
     */
    public function getCanvasMnemonicForElementType($element_type_id)
    {
        return $this->app->db
            ->createCommand('SELECT canvas_mnemonic from eyedraw_canvas WHERE container_element_type_id = :etid')
            ->bindParam(':etid', $element_type_id)
            ->queryScalar();
    }

    /**
     * Takes the given element, and iterates through the given attributes to
     * store all the doodles for object persistence
     *
     * @param $element
     * @param array $attributes array(attr_name => side ...)
     * @throws Exception
     */
    public function shredElementEyedraws($element, $attributes=array())
    {
        $this->clearEvent($element->event_id);
        $canvas_mnemonic = $this->getCanvasMnemonicForElementType($element->getElementType()->id);

        foreach ($attributes as $attr => $side) {
            if (!strlen($element->$attr)) {
                continue;
            }

            if (!($ed_json = json_decode($element->$attr))) {
                throw new Exception("Could not parse {$attr} as json" . $element->$attr);
            }

            foreach ($ed_json as $ed_doodle) {
                $this->storeDoodle($element->event_id, $canvas_mnemonic, $side, $ed_doodle);
            }
        }
    }

    /**
     * Load all the element attributes up with the appropriate set of doodles.
     *
     * @param $element
     * @param array $attributes
     */
    public function loadElementEyedrawDoodles($element, $attributes=array())
    {
        $query_string = <<<EOSQL
-- Query NEWEST eyedraw doodle data for set-interection-tuple groups for target patient and runtime canvas
SELECT
  in_ep.patient_id
, in_ev.event_date
, in_ev.created_date
, in_mdp.event_id
, in_mdp.laterality
, in_mdp.eyedraw_class_mnemonic
, in_mdp.placement_order
, in_mdp.content_json
, in_mdp.canvas_mnemonic AS irrelevant_origin_canvas_mnemonic
, in_ed.processed_canvas_intersection_tuple
, in_ecd.eyedraw_class_mnemonic
, in_ecd.canvas_mnenonic
, in_ecd.eyedraw_on_canvas_toolbar_location
, in_ecd.eyedraw_on_canvas_toolbar_order
, in_ecd.eyedraw_no_tuple_init_canvas_flag
, in_ecd.eyedraw_carry_forward_canvas_flag
-- All episodes for subject patient (see restriction)
FROM openeyes.episode in_ep
-- All events for subject patient
JOIN openeyes.event in_ev
  ON in_ev.episode_id = in_ep.id 
-- All EyeDraw data point for subject patient events
JOIN openeyes.mview_datapoint_node in_mdp
  ON in_mdp.event_id = in_ev.id
-- Look up eyedraw doodle/canvas rules
JOIN openeyes.eyedraw_canvas_doodle in_ecd
  ON in_ecd.eyedraw_class_mnemonic = in_mdp.eyedraw_class_mnemonic
-- Restrict-join: The doodle/canvas rule lookup required the runtime target canvas mnenonic 
-- Restrict-join: "AND NOT" the source canvas that was used to shred the doodle data)  
 AND in_ecd.canvas_mnenonic = :cvmnm
-- Look up eyedraw doodle rules
JOIN openeyes.eyedraw_doodle in_ed
  ON in_ed.eyedraw_class_mnemonic = in_ecd.eyedraw_class_mnemonic
-- Restrict by patient subject
WHERE in_ep.patient_id = :patient_id -- <<<<<<<<<<<<<<<<<<<<<<<<<<< BIND APP DATA HERE
-- Restrict to only doodles that are required to cpy forward to runtime canvas
-- (see above eyedraw_canvas_doodle restrict-join for runtime canvas selection)
AND in_ecd.eyedraw_carry_forward_canvas_flag = 1 
-- Restrict: Magic sub-query to eliminate OLDER event data in outer query
-- By identifying NEWER events data within same set-intersection-tuple/laterality in sub-query)
AND NOT EXISTS (
    SELECT 1
    -- All episodes for subject patient (see restriction)
    FROM openeyes.episode in2_ep
    -- All events for subject patient
    JOIN openeyes.event in2_ev 
      ON in2_ev.episode_id = in2_ep.id 
    -- All EyeDraw data point for subject patient events
    JOIN openeyes.mview_datapoint_node in2_mdp
      ON in2_mdp.event_id = in2_ev.id
    -- Look up eyedraw doodle (uses short circuit hop join - is safe J.Brown 27/07/2017) to determine set-intersection-tuples 
    JOIN openeyes.eyedraw_doodle in2_ed
      ON in2_ed.eyedraw_class_mnemonic = in2_mdp.eyedraw_class_mnemonic
    -- Restrict by patient subject (same as outer query)
    WHERE in2_ep.patient_id = in_ep.patient_id
  -- Restrict for same laterality as outer query record
  AND in2_mdp.laterality = in_mdp.laterality
  -- Restrict for same set-intersection-tuple as outer query record
  AND in2_ed.processed_canvas_intersection_tuple = in_ed.processed_canvas_intersection_tuple
  -- Restrict to only those events that are NEWER that outer query event (within set-intersection-tuples)
  -- Thus outer query records excluded as by defininion older it is an OLDER record
  AND (in2_ev.event_date, in2_ev.created_date) > (in_ev.event_date, in_ev.created_date)
)
ORDER BY
  'a'
, in_mdp.laterality
, in_ed.processed_canvas_intersection_tuple
, in_ev.event_date DESC
, in_ev.created_date DESC
;
EOSQL;
        $cmd = $this->app->db
            ->createCommand($query_string)
            ->bindParam(':patient_id', $event->episode->patient_id)
            ->bindParam(':cvmnm', $this->getCanvasMnemonicForElementType($element->getElementType()->id));

    }
}