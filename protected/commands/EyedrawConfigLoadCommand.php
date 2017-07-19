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
        return "yiic eyedrawconfigload <filename>\n\n".
            "load/update the eyedraw configuration from the definition file <filename>";
    }

    public function actionLoad($filename)
    {
        if (!$filename) {
            $this->usageError('Please supply the path to the eyedraw configuration file.');
        }

        $data = json_decode(file_get_contents($filename));

        foreach ($data['canvases'] as $canvas) {
            $this->processCanvasDefinition($canvas);
        }

        // iterate through the data structure, performing update/insert statements as appropriate
        foreach ($data['doodles'] as $doodle) {
            $this->processDoodleDefinition($doodle);
        }
    }

    protected function processCanvasDefinition($canvas)
    {
        // verify that the element type exists for this definition

        // insert/update as appropriate
    }

    protected function processDoodleDefinition($doodle)
    {
        // update or create doodle entry

        // iterate through doodle canvas definitions. Use the canvas mnemonic to confirm
        // whether or not it should be setup in the db.
    }

}