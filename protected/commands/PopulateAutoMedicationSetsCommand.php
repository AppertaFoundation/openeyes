<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class PopulateAutoMedicationSetsCommand extends CConsoleCommand
{
    private $_pid;
    private $_pidfile = '/tmp/oe_populatesets.pid';

    public function getHelp()
    {
        return "Re-populate the automatic Medication Sets based on the stored criteria\n";
    }

    public function run($args)
    {
        $this->_pid = getmypid();

        if ($this->_isRunning()) {
            echo "Another process is already being run." . PHP_EOL;
            exit(1);
        }

        $this->_savePid();
        MedicationSet::populateAutoSets();
        exit(0);
    }

    private function _isRunning()
    {
        if (file_exists($this->_pidfile)) {
            $pid = filter_var(file_get_contents($this->_pidfile), FILTER_SANITIZE_NUMBER_INT);
            return file_exists("/proc/$pid");
        } else {
            return false;
        }
    }

    public function actionCheckRunning()
    {
        return $this->_isRunning();
    }

    private function _savePid()
    {
        file_put_contents($this->_pidfile, $this->_pid);
    }
}