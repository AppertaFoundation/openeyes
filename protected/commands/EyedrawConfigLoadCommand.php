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
/**
 * Class EyedrawConfigLoadCommand
 *
 * Loader command for handling the eyedraw doodle configuration for object persistence.
 * Initial implementation is using a basic json config file that is expected to be superceded
 * by an XML document to define this information.
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

        if (file_exists($filename)) {
            $data = simplexml_load_file($filename);
        } else {
            $this->usageError($filename.' does not exist');
        }

        if ($data === null) {
            $this->usageError($filename . ' is not in a valid format');
        }


        // iterate through the data structure, performing update/insert statements as appropriate

        foreach ($data->CANVAS_LIST->CANVAS as $canvas){
            $this->processCanvasDefinition($canvas);
        }

        foreach ($data->DOODLE_LIST->DOODLE as $doodle){
            $this->processDoodleDefinition($doodle);
        }

        foreach ($data->DOODLE_USAGE_LIST->DOODLE_USAGE as $canvas_doodle){
            $this->processCanvasDoodleDefinition($canvas_doodle);
        }

        Yii::app()->db->createCommand('UPDATE openeyes.eyedraw_doodle ed SET ed.processed_canvas_intersection_tuple = (SELECT GROUP_CONCAT(DISTINCT ecd.canvas_mnemonic ORDER BY ecd.canvas_mnemonic) FROM openeyes.eyedraw_canvas_doodle ecd WHERE ecd.eyedraw_class_mnemonic = ed.eyedraw_class_mnemonic GROUP BY ecd.eyedraw_class_mnemonic) WHERE ed.eyedraw_class_mnemonic != "*"')->query();
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
        $cmd->bindValue(':cvmn', $canvas->CANVAS_MNEMONIC)
        ->bindValue(':cvname', $canvas->CANVAS_NAME)
        ->bindValue(':eid', $element_type->id)
        ->query();
    }
    /**
     * Create or update a doodle definition
     *
     * @param $doodle
     */
    private function insertOrUpdateDoodle($doodle)
    {
        $current = $this->getDb()
        ->createCommand('SELECT count(*) FROM ' . static::DOODLE_TBL . ' WHERE eyedraw_class_mnemonic = :mnm')
        ->bindValue(':mnm', $doodle->EYEDRAW_CLASS_MNEMONIC)
        ->queryScalar();
        if ($current) {
            $cmd = $this->getDb()->createCommand('UPDATE ' . static::DOODLE_TBL . ' SET init_doodle_json = :init '
                . 'WHERE eyedraw_class_mnemonic = :mnm');
        } else {
            $cmd = $this->getDb()->createCommand('INSERT INTO '
                . static::DOODLE_TBL .
                '(eyedraw_class_mnemonic, init_doodle_json) VALUES (:mnm, :init)');
        }
        $cmd->bindValue(':mnm', $doodle->EYEDRAW_CLASS_MNEMONIC)
        ->bindValue(':init', json_encode($doodle->INIT_DOODLE_JSON)) //check this
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
     * @param $canvas_doodle
     */
    private function insertOrUpdateCanvasDoodle($canvas_doodle)
    {

        if (!$this->isCanvasDefined($canvas_doodle->CANVAS_MNEMONIC)) {
            // if the element is not part of the configuration (module not included)
            // then we don't load the canvas, and therefore don't load the canvas doodle
            return;
        }

        $cmd1 = $this->getDb()
        ->createCommand('INSERT INTO openeyes.eyedraw_canvas_doodle ('
            .'eyedraw_class_mnemonic, '
            .'canvas_mnemonic, '
            .'eyedraw_on_canvas_toolbar_location, '
            .'eyedraw_on_canvas_toolbar_order, '
            .'eyedraw_no_tuple_init_canvas_flag, '
            .'eyedraw_carry_forward_canvas_flag) '
            .'SELECT '
            .':ecm, :cm, :eoctl, :eocto, :enticf, :ecfcf FROM (SELECT 1) v '
            .'WHERE NOT EXISTS ('
            .'SELECT 1 FROM openeyes.eyedraw_canvas_doodle s '
            .'WHERE s.eyedraw_class_mnemonic = :ecmm '
            .'AND s.canvas_mnemonic = :cmm)')
        ->bindValue(':ecm', $canvas_doodle->EYEDRAW_CLASS_MNEMONIC)
        ->bindValue(':cm', $canvas_doodle->CANVAS_MNEMONIC)
        ->bindValue(':eoctl', $canvas_doodle->ON_TOOLBAR_LOCATION)
        ->bindValue(':eocto', NULL) //NULL for now
        ->bindValue(':enticf', NULL) //NULL for now
        ->bindValue(':ecfcf', NULL)
        ->bindValue(':ecmm', $canvas_doodle->EYEDRAW_CLASS_MNEMONIC)
        ->bindValue(':cmm', $canvas_doodle->CANVAS_MNEMONIC)
        //->bindValue(':ecfcf', $canvas_doodle->EYEDRAW_ON_CANVAS_TOOLBAR_ORDER == '' ? "NULL" : EYEDRAW_ON_CANVAS_TOOLBAR_ORDER)
        ->query();

        $cmd2 = $this->getDb()
        ->createCommand('UPDATE openeyes.eyedraw_canvas_doodle u '
            .'SET u.eyedraw_on_canvas_toolbar_location = :eoctl, '
            .'u.eyedraw_on_canvas_toolbar_order = :eocto, '
            .'u.eyedraw_no_tuple_init_canvas_flag = :enticf, '
            .'u.eyedraw_carry_forward_canvas_flag = :ecfcf '
            .'WHERE u.eyedraw_class_mnemonic = :ecm '
            .'AND u.canvas_mnemonic = :cm')
        ->bindValue(':eoctl', $canvas_doodle->ON_TOOLBAR_LOCATION)
        ->bindValue(':eocto', NULL) //NULL for now
        ->bindValue(':enticf', NULL) //NULL for now
        ->bindValue(':ecfcf', NULL)
        //->bindValue(':ecfcf', $canvas_doodle->EYEDRAW_ON_CANVAS_TOOLBAR_ORDER == '' ? "NULL" : EYEDRAW_ON_CANVAS_TOOLBAR_ORDER)
        ->bindValue(':ecm', $canvas_doodle->EYEDRAW_CLASS_MNEMONIC)
        ->bindValue(':cm', $canvas_doodle->CANVAS_MNEMONIC)
        ->query();
    }
    /**
     *
     * @param $canvas
     */
    protected function processCanvasDefinition($canvas)
    {
        // verify that the element type exists for this definition
        if ($element_type = ElementType::model()->findByAttributes(array('class_name' => $canvas->OE_ELEMENT_CLASS_NAME))) {
            $this->insertOrUpdateCanvas($canvas, $element_type);
        }
    }
    /**
     * @param $doodle
     */
    protected function processDoodleDefinition($doodle)
    {
        $this->insertOrUpdateDoodle($doodle);
    }
    /**
     * @param $canvas_doodle
     */
    protected function processCanvasDoodleDefinition($canvas_doodle) //must be run last to prevent error
    {
        //Use the canvas mnemonic to confirm whether or not it should be setup in the db.
        $this->insertOrUpdateCanvasDoodle($canvas_doodle);
    }
}
