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
        // TODO: implement this function
    }
}