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
class EDPostProcessor
{
    /**
     * @var CApplication
     */
    protected $app;

    public function __construct(CApplication $app = null)
    {
        if ($app === null)
        {
            $app = Yii::app();
        }
        $this->app = $app;
    }

    /**
     * @param Event $event
     */
    protected function clearEvent(Event $event)
    {
        $this->app->db()
            ->createCommand('DELETE FROM mview_datapoint_node where event_id = :eid')
            ->bindParam(':eid', $event->id)
            ->query();
    }

    protected function
    protected function storeDoodle(Event $event, $mnemonic, $side, $doodle)
    {
        $param_map = array(
            'event_id' => $event->id,
            'eyedraw_class_mnemonic' => $doodle->subClass,
            'canvas_mnemonic' => $mnemonic,
            'placement_order' => $doodle->order,
            'laterality' => $side,
            'content_json' => json_encode($doodle)
        );

        $this->app->db()
            ->createCommand('INSERT INTO mview_datapoint_node (' . implode(',', array_keys($param_map)) . ')'
                . ' VALUES (' . implode(',', array_map( ')');
    }

    public function getCanvasMnemonicForElementType(ElementType $element_type)
    {
        return $this->app->db()
            ->createCommand('SELECT canvas_mnemonic from eyedraw_canvas WHERE container_element_type_id = :etid')
            ->bindParam(':etid', $element_type->id)
            ->queryScalar();
    }

    /**
     * @param $element
     * @param array $attributes
     * @throws Exception
     */
    public function shredElementEyedraws($element, $attributes=array())
    {
        $this->clearEvent($element->event);
        $canvas_mnemonic = $this->getCanvasMnemonicForElementType($element->getElementType());

        foreach ($attributes as $attr) {
            if (!($ed_json = json_decode($element->$attr))) {
                throw new Exception("Could not parse {$attr} as json");
            }

            foreach ($ed_json as $ed_doodle) {
                $this->storeDoodle($element->event, $canvas_mnemonic, $ed_doodle);
            }
        }

    }
}