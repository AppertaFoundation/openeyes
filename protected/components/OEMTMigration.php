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
abstract class OEMTMigration extends OEMigration
{
    /**
     * A function to determine which tables to make mappings for, and at which level.
     * Array returned should have the following structure:
     * array(
     *    'user' => array('table_name', ...),
     *    'firm' => array('table_name', ...),
     *    'site' => array('table_name', ...),
     *    'subspecialty' => array('table_name', ...),
     *    'specialty' => array('table_name', ...),
     *    'institution' => array('table_name', ...),
     * With each table_name represeting a table to be mapped at that level
     *
     * @return array
     * @throws CException
     */
    abstract protected function getLevelStructuredTables(): array;

    /**
     * @return bool|void
     * @throws CException
     */
    public function safeUp()
    {
        $level_tables = $this->getLevelStructuredTables();

        $institution_list = $this->dbConnection->createCommand()
            ->select('id')
            ->from('institution')
            ->queryColumn();
        $first_institution = array_shift($institution_list);
        foreach ($level_tables as $level => $tables)
        {
            foreach ($tables as $ref_table)
            {
                $new_table_name = $ref_table.'_'.$level;
                $ref_table_id = $ref_table.'_id';
                $level_id = $level.'_id';
                $level_short_string = substr($level, 0, 2);

                //Necessary because some pks in the DB are int(10) unsigned and some are int(11)
                $ref_table_id_type = $this->dbConnection->schema->getTable($ref_table)->getColumn('id')->dbType;
                $level_table_id_type = $this->dbConnection->schema->getTable($level)->getColumn('id')->dbType;

                $this->createOETable(
                    $new_table_name,
                    array(
                        'id' => 'pk',
                        $ref_table_id => $ref_table_id_type,
                        $level_id => $level_table_id_type,
                    ),
                    true
                );

                $this->addForeignKey(
                    $new_table_name.'_'.$level_short_string.'_fk',
                    $new_table_name,
                    $level_id,
                    $level,
                    'id'
                );

                $this->addForeignKey(
                    $new_table_name.'_data_fk',
                    $new_table_name,
                    $ref_table_id,
                    $ref_table,
                    'id'
                );
            }
        }
    }


    /**
     * @return bool|void
     */
    public function safeDown()
    {
        $level_tables = $this->getLevelStructuredTables();

        foreach ($level_tables as $level => $tables)
        {
            foreach ($tables as $ref_table)
            {
                $new_table_name = $ref_table.'_'.$level;
                $level_short_string = substr($level, 0, 2);

                $this->dropForeignKey(
                    $new_table_name.'_'.$level_short_string.'_fk',
                    $new_table_name,
                );

                $this->dropForeignKey(
                    $new_table_name.'_data_fk',
                    $new_table_name,
                );

                $this->dropOETable(
                    $new_table_name,
                    true
                );
            }
        }
    }
}
