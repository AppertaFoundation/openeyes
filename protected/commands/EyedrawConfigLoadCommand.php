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
class EyedrawConfigLoadCommand extends CConsoleCommand
{
    public $defaultAction = 'load';

    const DOODLE_TBL        = 'eyedraw_doodle';
    const CANVS_TBL         = 'eyedraw_canvas';
    const CANVAS_DOODLE_TBL = 'eyedraw_canvas_doodle';

    public function getName()
    {
        return 'Load eyedraw configuration';
    }

    public function getHelp()
    {
        return "yiic eyedrawconfigload --filename=<filename>\n\n".
            "load/update the eyedraw configuration from the definition file <filename>";
    }

    /**
     * Abstraction to the db connection
     * @return mixed
     */
    protected function getDb()
    {
        // TODO: stop using the static Yii call here
        return Yii::app()->db;
    }

    /**
     * Default action to process the given configuration file.
     *
     * @param $filename
     */
    public function actionLoad($filename)
    {
        if (!$filename) {
            $this->usageError('Please supply the path to the eyedraw configuration file.');
        }

        $data = json_decode(file_get_contents($filename));
        if ($data === null) {
            $this->usageError($filename . ' is not in a valid format');
        }

        foreach ($data->canvases as $canvas) {
            $this->processCanvasDefinition($canvas);
        }

        // iterate through the data structure, performing update/insert statements as appropriate
        foreach ($data->doodles as $doodle) {
            $this->processDoodleDefinition($doodle);
        }
    }

    /**
     * @param $canvas
     * @param $element_type
     */
    private function insertOrUpdateCanvas($canvas, $element_type)
    {
        $current = $this->getDb()
            ->createCommand('SELECT count(*) FROM ' . static::CANVS_TBL . ' WHERE container_element_type_id = :eid')
            ->bindValue(':eid', $element_type->id)
            ->queryScalar();
        if ($current) {
            $cmd = $this->getDb()
                ->createCommand('UPDATE '
                    . static::CANVS_TBL .
                    ' SET canvas_mnemonic = :cvmn, canvas_name = :cvname where container_element_type_id = :eid');
        } else {
            $cmd = $this->getDb()
                ->createCommand('INSERT INTO ' . static::CANVS_TBL .
                    '(canvas_mnemonic, canvas_name, container_element_type_id) VALUES (:cvmn, :cvname, :eid)');
        }
        $cmd->bindValue(':cvmn', $canvas->mnemonic)
            ->bindValue(':cvname', $canvas->name)
            ->bindValue(':eid', $element_type->id)
            ->query();
    }

    /**
     * Create or update a doodle definition
     *
     * @param $definition
     */
    private function insertOrUpdateDoodle($definition)
    {
        $current = $this->getDb()
            ->createCommand('SELECT count(*) FROM ' . static::DOODLE_TBL . ' WHERE eyedraw_class_mnemonic = :mnm')
            ->bindValue(':mnm', $definition->mnemonic)
            ->queryScalar();
        if ($current) {
            $cmd = $this->getDb()->createCommand('UPDATE ' . static::DOODLE_TBL . ' SET init_doodle_json = :init '
                . 'WHERE eyedraw_class_mnemonic = :mnm');
        } else {
            $cmd = $this->getDb()->createCommand('INSERT INTO '
                . static::DOODLE_TBL .
                '(eyedraw_class_mnemonic, init_doodle_json) VALUES (:mnm, :init)');
        }
        $cmd->bindValue(':mnm', $definition->mnemonic)
            ->bindValue(':init', json_encode($definition->initial))
            ->query();
    }

    /**
     * @param $mnemonic
     * @return bool
     */
    protected function isCanvasDefined($mnemonic)
    {
        return $this->getDb()
            ->createCommand('SELECT count(*) FROM ' . static::CANVS_TBL
                . ' WHERE canvas_mnemonic = :cvmn')
            ->bindValue(':cvmn', $mnemonic)
            ->queryScalar() > 0;
    }

    /**
     * @param $definition
     * @param $canvas
     */
    private function insertOrUpdateCanvasDoodle($definition, $canvas)
    {
        if (!$this->isCanvasDefined($canvas->mnemonic)) {
            return;
        }

        $mnemonic = $definition->mnemonic;
        $canvas_mnemonic = $canvas->mnemonic;
        $current = $this->getDb()
            ->createCommand('SELECT count(*) FROM ' . static::CANVAS_DOODLE_TBL
                . ' WHERE eyedraw_class_mnemonic = :edmn AND canvas_mnemonic = :cvmn')
            ->bindValue(':edmn', $mnemonic)
            ->bindValue(':cvmn', $canvas_mnemonic)
            ->queryScalar();
        if ($current) {
            $cmd = $this->getDb()->createCommand('UPDATE ' . static::CANVAS_DOODLE_TBL . ' SET  '
                . 'eyedraw_on_canvas_toolbar_location = :tlbloc, '
                . 'eyedraw_on_canvas_toolbar_order = :tlbor, '
                . 'eyedraw_no_tuple_init_canvas_flag = :infl, '
                . 'eyedraw_carry_forward_canvas_flag = :fwdfl '
                . ' WHERE eyedraw_class_mnemonic = :edmn AND canvas_mnemonic = :cvmn');
        } else {
            $cmd = $this->getDb()->createCommand('INSERT INTO ' . static::CANVAS_DOODLE_TBL
                . '(eyedraw_class_mnemonic, canvas_mnemonic, eyedraw_on_canvas_toolbar_location, eyedraw_on_canvas_toolbar_order, '
                . 'eyedraw_no_tuple_init_canvas_flag, eyedraw_carry_forward_canvas_flag)'
                . 'VALUES (:edmn, :cvmn, :tlbloc, :tlbor, :infl, :fwdfl)');
        }
        $cmd->bindValue(':edmn', $mnemonic)
            ->bindValue(':cvmn', $canvas_mnemonic)
            ->bindValue(':tlbloc', $canvas->toolbar_location)
            ->bindValue(':tlbor', $canvas->toolbar_order)
            ->bindValue(':infl', $canvas->init_canvas)
            ->bindValue(':fwdfl', $canvas->carry_forward)
            ->query();
    }

    /**
     *
     * @param $canvas
     */
    protected function processCanvasDefinition($canvas)
    {
        // verify that the element type exists for this definition
        if ($element_type = ElementType::model()->findByAttributes(array('class_name' => $canvas->oe_class))) {
            $this->insertOrUpdateCanvas($canvas, $element_type);
        }
    }

    /**
     * @param $doodle
     */
    protected function processDoodleDefinition($doodle)
    {
        // update or create doodle entry
        $this->insertOrUpdateDoodle($doodle->definition);

        // iterate through doodle canvas definitions. Use the canvas mnemonic to confirm
        // whether or not it should be setup in the db.
        foreach ($doodle->canvases as $canvas) {
            $this->insertOrUpdateCanvasDoodle($doodle->definition, $canvas);
        }
    }

}