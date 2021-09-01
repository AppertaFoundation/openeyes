<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Class QueryIterator
 *
 * Usage :
 * Create "createCommand" :
 * $command = Yii::app()->db->createCommand()
 *              ->select('*')
 *              ->from('patient');
 *
 * $iterator = new QueryIterator($command, $by = 2000);
 * // or $iterator->by(2000);
 *
 * foreach ($iterator as $chunk) {
 *      foreach ($chunk as $patient) {
 *           echo '<pre>' . print_r($patient, true) . '</pre>';
 *      }
 * }
 *
 */
class QueryIterator implements Iterator {
    private $position = 0;
    public $command = null;
    public $limit = 2000;
    private $total_row_count;
    private $required_steps = 0;

    public function by($step)
    {
        $this->limit = $step;
        $this->required_steps = ceil($this->total_row_count / $this->limit);
    }

    public function __construct($command, $by = 2000) {
        $this->position = 0;
        $this->command = $command;

        $this->limit = $by;
        $this->total_row_count = $this->getRowsCount();
        $this->required_steps = ceil($this->total_row_count / $this->limit);
    }

    public function rewind() {
        $this->position = 0;
    }

    public function current() {
        $command = clone $this->command;
        return $command
            ->limit($this->limit)
            ->offset($this->position*$this->limit)->queryAll();
    }

    public function key() {
        return $this->position;
    }

    public function next() {
        ++$this->position;
    }

    public function valid() {
        return $this->position < $this->required_steps;
    }

    private function getRowsCount()
    {
        $table = str_replace('`', '', $this->command->from);
        $sql = "SELECT table_rows
                FROM information_schema.tables
                WHERE table_type = 'BASE TABLE' AND table_name = :table";

        $command = \Yii::app()->db->createCommand($sql);
        return $command->queryScalar([':table' => $table]);
    }
}
