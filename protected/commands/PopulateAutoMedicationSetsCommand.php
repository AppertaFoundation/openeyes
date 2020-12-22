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
    private $lock_name = "PopulateAutoMedicationSetsCommand_lock";

    public function getHelp()
    {
        return "Re-populate the automatic Medication Sets based on the stored criteria\n";
    }

    public function run($args)
    {
        $t = microtime(true);
        echo "[" . (date("Y-m-d H:i:s")) . "] PopulateAutoMedicationSets ... ";

        if ($this->_isRunning() || !$this->acquireLock()) {
            echo "Another process is already being run." . PHP_EOL;
            exit(1);
        }

        register_shutdown_function(function () {
            $this->releaseLock();
        });

        //populate whole set unless we are given specific set number to reduce time on rebuilding sets on admin page
        if (empty($args)) {
            MedicationSet::populateAutoSets();
        } else {
            $medication_set = MedicationSet::model()->findByPk($args[0]);
            if ($medication_set && $medication_set->automatic) {
                $medication_set->populateAuto();
            }
        }

        echo "OK - took: " . (microtime(true) - $t) . "\n";
        exit(0);
    }

    private function _isRunning()
    {
        return (bool) Yii::app()->db->createCommand(
            'SELECT IS_USED_LOCK(:name)'
        )->bindValue(':name', $this->lock_name)
        ->queryScalar();
    }

    /**
     * @return bool acquiring result
     */
    private function acquireLock()
    {
        return (bool) Yii::app()->db->createCommand(
            'SELECT GET_LOCK(:name, :timeout)',
        )->bindValue(':name', $this->lock_name)
        ->bindValue(':timeout', 0) // timeout = 0 means that the method will return false immediately in case lock is used
        ->queryScalar();
    }

    /**
     * @return bool release result
     */
    private function releaseLock()
    {
        return (bool) Yii::app()->db->createCommand(
            'SELECT RELEASE_LOCK(:name)'
        )->bindValue(':name', $this->lock_name)
        ->queryScalar();
    }

    public function actionCheckRunning()
    {
        return $this->_isRunning();
    }
}
