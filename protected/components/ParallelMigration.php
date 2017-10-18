<?php
/**
 * OpenEyes.
 *
 * 
 * Copyright OpenEyes Foundation, 2017
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class ParallelMigration extends CDbMigration
{
    public $canFork = null;
    public $threads = 8;
    public $pids = array();
    public $lockFP;

    public function checkForkPossible()
    {
        if (function_exists('pcntl_fork')) {
            Yii::app()->db->setActive(false);
            $pid = pcntl_fork();
            if ($pid == 0) {
                exit;
            }
            Yii::app()->db->setActive(true);

            if ($pid != -1) {
                $this->canFork = true;
                echo "********************************************************************************************\n";
                echo "* Looks like pcntl_fork() is available, so we will parallelise this migration to save time *\n";
                echo "********************************************************************************************\n";
            } else {
                echo "********************************************************************************************\n";
                echo "* Looks like pcntl_fork() is available but a test fork failed so we won't be parallelising *\n";
                echo "********************************************************************************************\n";
            }
        }
    }

    public function fork()
    {
        $this->getDbConnection()->setActive(false);
        $pid = pcntl_fork();
        $this->getDbConnection()->setActive(true);
        if ($pid > 0) {
            $this->pids[] = $pid;
        }

        return $pid;
    }

    public function waitForThreads()
    {
        foreach ($this->pids as $pid) {
            pcntl_waitpid($pid, $status);
        }
        $this->pids = array();
    }

    public function parallelise($method, $data)
    {
        if ($this->canFork === null) {
            $this->checkForkPossible();
        }

        if (!$this->canFork) {
            // Fork off
            $this->$method($data);
        } else {
            $workload = array();
            for ($i = 0;$i < $this->threads;++$i) {
                $workload[$i] = array();
            }

            $n = 0;
            foreach ($data as $item) {
                $workload[$n++][] = $item;
                if ($n >= $this->threads) {
                    $n = 0;
                }
            }

            $ok = true;
            foreach ($workload as $i => $data) {
                $pid = $this->fork();
                if ($pid == 0) {
                    $this->$method($data);
                    exit;
                } elseif ($pid == -1) {
                    $ok = false;
                }
            }

            $this->waitForThreads();

            if (!$ok) {
                echo "\nOne or more fork() calls failed, migration aborted.\n";
                exit;
            }
        }
    }
}
