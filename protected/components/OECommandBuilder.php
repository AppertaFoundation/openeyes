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
class OECommandBuilder extends CDbCommandBuilder
{
    public function createInsertFromTableCommand($table_version, $table, $criteria)
    {
        $this->ensureTable($table);
        $this->ensureTable($table_version);

        $columns = array();

        foreach (array_keys($table_version->columns) as $column) {
            if (!in_array($column, array('version_date', 'version_id'))) {
                $columns[] = $column;
            }
        }

        $sql = "INSERT INTO {$table_version->rawName} (`".implode('`,`', $columns).'`,`version_date`,`version_id`) '.
            "SELECT {$table->rawName}.*, ".$this->dbConnection->quoteValue(date('Y-m-d H:i:s')).", NULL FROM {$table->rawName}";

        $sql = $this->applyJoin($sql, $criteria->join);
        $sql = $this->applyCondition($sql, $criteria->condition);
        $sql = $this->applyOrder($sql, $criteria->order);
        $sql = $this->applyLimit($sql, $criteria->limit, $criteria->offset);

        $command = $this->getDbConnection()->createCommand($sql);
        $this->bindValues($command, $criteria->params);

        return $command;
    }
}
