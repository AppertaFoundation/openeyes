<?php

/**
 * OpenEyes
 *
 * Copyright OpenEyes Foundation, 2017
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
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
     * @param string $canvas_mnemonic - limit clearance to the given mnemonic
     */
    protected function clearEvent($event_id, $canvas_mnemonic = null)
    {
        $query_string = 'DELETE FROM mview_datapoint_node where event_id = :eid';
        if ($canvas_mnemonic) {
            $query_string .= ' AND canvas_mnemonic = :cmn';
        }

        $query = $this->app->db
            ->createCommand($query_string)
            ->bindParam(':eid', $event_id);

        if ($canvas_mnemonic) {
            $query->bindParam(':cmn', $canvas_mnemonic);
        }
        $query->query();
    }

    /**
     * @param $event_id
     * @param $mnemonic
     * @param $side
     * @param $doodle
     */
    protected function storeDoodle($event_id, $mnemonic, $side, $doodle)
    {
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
     * @throws CException
     */
    public function getCanvasMnemonicForElementType($element_type_id)
    {
        $result = $this->app->db
            ->createCommand('SELECT canvas_mnemonic from eyedraw_canvas WHERE container_element_type_id = :etid')
            ->bindParam(':etid', $element_type_id)
            ->queryScalar();
        if (!$result) {
            throw new CException("Cannot find eyedraw canvas mnemonic for element type id $element_type_id. Have you loaded the latest config?");
        }
        return $result;
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
        $canvas_mnemonic = $this->getCanvasMnemonicForElementType($element->getElementType()->id);
        $this->clearEvent($element->event_id, $canvas_mnemonic);

        foreach ($attributes as $attr => $side) {
            if (!strlen($element->$attr)) {
                continue;
            }

            $cleaned_attribute = htmlspecialchars_decode($element->$attr);
            $ed_json = json_decode($cleaned_attribute);

            if (!is_array($ed_json)) {
                throw new Exception("Could not parse {$attr} as json array {$element->$attr} on canvas {$canvas_mnemonic}");
            }

            foreach ($ed_json as $ed_doodle) {
	            if (!isset($ed_doodle->tags)) {
                    $this->storeDoodle($element->event_id, $canvas_mnemonic, $side, $ed_doodle);
                }
            }
        }
    }

    /**
     * @param $element
     */
    public function removeElementEyedraws($element)
    {
        $canvas_mnemonic = $this->getCanvasMnemonicForElementType($element->getElementType()->id);
        $this->clearEvent($element->event_id, $canvas_mnemonic);
    }

    /**
     * @var array internal caching for patient doodles.
     */
    private $patient_doodles = array();


    /**
     * @param $patient_id
     * @param $canvas_mnemonic
     * @param $side
     * @return mixed
     */
    private function retrieveDoodlesForSide($patient_id, $canvas_mnemonic, $side)
    {
        if (!array_key_exists($patient_id, $this->patient_doodles)) {
            $this->patient_doodles[$patient_id] = array();
        }

        if (!array_key_exists($canvas_mnemonic, $this->patient_doodles[$patient_id])) {
            // init sided structure
            $this->patient_doodles[$patient_id][$canvas_mnemonic] = array('R' => array(), 'L' => array());

            // get init json for any doodles that should always be present on this canvas
            $always_init = $this->getAlwaysInitDoodlesForCanvas($canvas_mnemonic);
            $class_by_laterality = array('R' => array(), 'L' => array());

            // get the carry forward doodles
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
, in_ecd.canvas_mnemonic
, in_ecd.eyedraw_on_canvas_toolbar_location
, in_ecd.eyedraw_on_canvas_toolbar_order
, in_ecd.eyedraw_no_tuple_init_canvas_flag
, in_ecd.eyedraw_carry_forward_canvas_flag
-- All episodes for subject patient (see restriction)
FROM episode in_ep
-- All events for subject patient
JOIN event in_ev
  ON in_ev.episode_id = in_ep.id
-- All EyeDraw data point for subject patient events
JOIN mview_datapoint_node in_mdp
  ON in_mdp.event_id = in_ev.id
-- Look up eyedraw doodle/canvas rules
JOIN eyedraw_canvas_doodle in_ecd
  ON in_ecd.eyedraw_class_mnemonic = in_mdp.eyedraw_class_mnemonic
-- Restrict-join: The doodle/canvas rule lookup required the runtime target canvas mnenonic
-- Restrict-join: "AND NOT" the source canvas that was used to shred the doodle data)
 AND in_ecd.canvas_mnemonic = :cvmnm
-- Look up eyedraw doodle rules
JOIN eyedraw_doodle in_ed
  ON in_ed.eyedraw_class_mnemonic = in_ecd.eyedraw_class_mnemonic
-- Restrict by patient subject
WHERE in_ep.patient_id = :patient_id -- <<<<<<<<<<<<<<<<<<<<<<<<<<< BIND APP DATA HERE
-- Restrict to only doodles that are required to cpy forward to runtime canvas
-- (see above eyedraw_canvas_doodle restrict-join for runtime canvas selection)
AND in_ecd.eyedraw_carry_forward_canvas_flag = 1
-- Restrict: Magic sub-query to eliminate OLDER event data in outer query
-- By identifying NEWER events data within same set-intersection-tuple/laterality in sub-query)
AND in_ev.deleted = 0
AND in_ep.deleted = 0
AND NOT EXISTS (
    SELECT 1
    -- All episodes for subject patient (see restriction)
    FROM episode in2_ep
    -- All events for subject patient
    JOIN event in2_ev
      ON in2_ev.episode_id = in2_ep.id
    -- All EyeDraw data point for subject patient events
    JOIN mview_datapoint_node in2_mdp
      ON in2_mdp.event_id = in2_ev.id
    -- Look up eyedraw doodle (uses short circuit hop join - is safe J.Brown 27/07/2017) to determine set-intersection-tuples
    JOIN eyedraw_doodle in2_ed
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
  AND in2_ev.deleted = 0
  AND in2_ep.deleted = 0
)
ORDER BY
  'a'
, in_mdp.laterality
, in_ed.processed_canvas_intersection_tuple
, in_ev.event_date DESC
, in_ev.created_date DESC
;
EOSQL;
            foreach ($this->app->db
                ->createCommand($query_string)
                ->bindParam(':patient_id', $patient_id)
                ->bindParam(':cvmnm', $canvas_mnemonic)->queryAll() as $result
            ) {
                // store the carried forward doodle data
                $this->patient_doodles[$patient_id][$canvas_mnemonic][$result['laterality']][] = $result['content_json'];
                // store doodle class by laterality
                $class_by_laterality[$result['laterality']][] = $result['eyedraw_class_mnemonic'];
            };

            foreach (array('R', 'L') as $laterality) {
                if (count($class_by_laterality[$laterality]) > 0) {
                    // merge in any missing init doodle json that should always be present
                    // if at least one doodle is present.
                    foreach ($always_init as $always_cls => $always_json) {
                        if (!in_array($always_cls, $class_by_laterality[$laterality])) {
                            $this->patient_doodles[$patient_id][$canvas_mnemonic][$laterality][] = $always_json;
                        }
                    }
                }
            }

        }

        return $this->patient_doodles[$patient_id][$canvas_mnemonic][((int)$side === Eye::LEFT) ? 'L' : 'R'];
    }

    /**
     * @param $doodles
     * @return array
     */
    protected function getInitDoodles($doodles)
    {
        $query = $this->app->db
            ->createCommand()
            ->select('init_doodle_json')
            ->from('eyedraw_doodle')
            ->where(array('in', 'eyedraw_class_mnemonic', $doodles));

        return array_map(function($r) { return $r['init_doodle_json'];}, $query->queryAll());
    }

    /**
     * @param $canvas_mnemonic
     * @return array
     */
    private function getInitDoodlesForCanvas($canvas_mnemonic)
    {
        $query_string = <<<EOSQL
SELECT ed.init_doodle_json
FROM eyedraw_doodle ed
LEFT JOIN eyedraw_canvas_doodle ecd
ON ecd.eyedraw_class_mnemonic = ed.eyedraw_class_mnemonic
WHERE ecd.canvas_mnemonic = :ecdcm
AND ecd.eyedraw_no_tuple_init_canvas_flag = true
EOSQL;

        $results = array();
        foreach ($this->app->db
            ->createCommand($query_string)
            ->bindParam(':ecdcm', $canvas_mnemonic)->queryAll() as $result
        ) {
            if ($result['init_doodle_json'])
                $results[] = $result['init_doodle_json'];
        }

        return $results;
    }

    /**
     * Returns an array of initial json indexed by eyedraw doodle class mnemonics
     *
     * @param $canvas_mnemonic
     * @return array
     */
    private function getAlwaysInitDoodlesForCanvas($canvas_mnemonic)
    {
        $query_string = <<<EOSQL
SELECT ed.eyedraw_class_mnemonic,
ed.init_doodle_json
FROM eyedraw_doodle ed
LEFT JOIN eyedraw_canvas_doodle ecd
ON ecd.eyedraw_class_mnemonic = ed.eyedraw_class_mnemonic
WHERE ecd.canvas_mnemonic = :ecdcm
AND ecd.eyedraw_always_init_canvas_flag = true
EOSQL;
        $results = array();
        foreach ($this->app->db
            ->createCommand($query_string)
            ->bindParam(':ecdcm', $canvas_mnemonic)->queryAll() as $result
        ) {
            if ($result['init_doodle_json']) {
                $results[$result['eyedraw_class_mnemonic']] = $result['init_doodle_json'];
            }
        }

        return $results;
    }

    /**
     * Load all the element attributes up with the appropriate set of doodles.
     *
     * @param \Patient $patient
     * @param $element
     * @param $side
     * @param $attribute
     * @throws CException
     */
    public function loadElementEyedrawDoodles(Patient $patient, &$element, $side, $attribute)
    {
        if (!in_array((int)$side, array(Eye::RIGHT, Eye::LEFT))) {
            $side = Eye::RIGHT;
        }
        $canvas_mnemonic = $this->getCanvasMnemonicForElementType($element->getElementType()->id);

        if ($doodle_data = $this->retrieveDoodlesForSide($patient->id, $canvas_mnemonic, $side)) {
            $element->$attribute = '[' . implode(',', $doodle_data) . ']';
        } else {
            $element->$attribute = '[' . implode(',', $this->getInitDoodlesForCanvas($canvas_mnemonic)) . ']';
        }
    }

    public function getElementEyedrawDoodles(Patient $patient, $element, $side, $attribute)
    {
        $fields = array();
        if (!in_array((int)$side, array(Eye::RIGHT, Eye::LEFT))) {
            $side = Eye::RIGHT;
        }
        $canvas_mnemonic = $this->getCanvasMnemonicForElementType($element->getElementType()->id);

        if ($doodle_data = $this->retrieveDoodlesForSide($patient->id, $canvas_mnemonic, $side)) {
            $fields[$attribute] = '[' . implode(',', $doodle_data) . ']';
        } else {
            $fields[$attribute] = '[' . implode(',', $this->getInitDoodlesForCanvas($canvas_mnemonic)) . ']';
        }
        return $fields;
    }

    /**
     * Add doodles to the given element attribute, unless certain doodles are already defined in that attribute
     *
     * @param $element
     * @param $attribute
     * @param array $doodles - format is [['doodle_class' => className, 'unless' => [ list of ed classes that block addition of this class]]]
     * @throws CException
     */
    public function addElementEyedrawDoodles(&$element, $attribute, $doodles = array())
    {
        $current = json_decode($element->$attribute, true);
        if (!is_array($current)) {
            $current = array();
        }

        $append = array();
        foreach ($doodles as $doodle_spec) {
            $doodle_class = $doodle_spec['doodle_class'];
            $unless = array_key_exists('unless', $doodle_spec) ? $doodle_spec['unless'] : array();

            if (!array_intersect($unless, array_map(function($class) { return $class['subclass']; }, $current))) {
                $init_doodles = $this->getInitDoodles(array($doodle_class));
                if ($init_doodles[0] === '') {
                    throw new CException("Attempt to add eyedraw doodle $doodle_class when no init json defined. Have you loaded the latest config?");
                }
                $append[] = json_decode($init_doodles[0]);
            }
        }

        $element->$attribute = json_encode(array_merge($current, $append));
    }

    public function buildElementEyedrawDoodles($ed_field, $doodles = array())
    {
        $current = json_decode($ed_field, true);
        if (!is_array($current)) {
            $current = array();
        }

        $append = array();
        foreach ($doodles as $doodle_spec) {
            $doodle_class = $doodle_spec['doodle_class'];
            $unless = array_key_exists('unless', $doodle_spec) ? $doodle_spec['unless'] : array();

            if (!array_intersect($unless, array_map(function($class) { return $class['subclass']; }, $current))) {
                $init_doodles = $this->getInitDoodles(array($doodle_class));
                if ($init_doodles[0] === '') {
                    throw new CException("Attempt to add eyedraw doodle $doodle_class when no init json defined. Have you loaded the latest config?");
                }
                $append[] = json_decode($init_doodles[0]);
            }
        }

        return json_encode(array_merge($current, $append));
    }

    public function applyNewElementEyedrawData($element_type_id, $patient_json, $new_json)
    {
        $patient_data = json_decode($patient_json, true);
        $new_data = json_decode($new_json, true);

        // Remap as associative arrays addressed by subclass to make life easier below - any existing data without subclasses will be dropped.
        // Only data with a subclass that is marked as being 'carry forward' is to copied over the new (template) data
        $patient_data = array_reduce(
            $patient_data,
            static function ($into, $data) {
                if (array_key_exists('subclass', $data)) {
                    $into[$data['subclass']] = $data;
                }

                return $into;
            },
            []
        );

        // New data without subclasses is preserved however.
        $new_data = array_reduce(
            $new_data,
            static function ($into, $data) {
                if (array_key_exists('subclass', $data)) {
                    $into[$data['subclass']] = $data;
                } else {
                    $into[] = $data;
                }

                return $into;
            },
            []
        );

        $canvas_mnemonic = $this->getCanvasMnemonicForElementType($element_type_id);

        $carry_forward_doodles = \Yii::app()->db
                               ->createCommand()
                               ->select('eyedraw_class_mnemonic')
                               ->from('eyedraw_canvas_doodle')
                               ->where([
                                   'and',
                                   ['and', 'eyedraw_carry_forward_canvas_flag <> 0', 'canvas_mnemonic = :canvas_mnemonic'],
                                   ['in', 'eyedraw_class_mnemonic', array_keys($patient_data)]
                               ],
                               [':canvas_mnemonic' => $canvas_mnemonic])
                               ->queryColumn();

        foreach ($carry_forward_doodles as $subclass) {
            $new_data[$subclass] = $patient_data[$subclass];
        }

        return json_encode(array_values($new_data));
    }
}
