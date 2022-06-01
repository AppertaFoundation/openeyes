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
class OEMigration extends CDbMigration
{
    private $migrationPath;
    private $testdata;
    private $csvFiles;
    private $insertsMap = array();
    private $verbose = true;

    /**
     * Executes a SQL statement.
     * This method executes the specified SQL statement using {@link dbConnection}.
     *
     * @param string $sql the SQL statement to be executed
     * @param array $params input parameters (name=>value) for the SQL execution. See {@link CDbCommand::execute} for more details.
     * @param string $message optional message to display instead of SQL
     */
    public function execute($sql, $params = array(), $message = null)
    {
        $message = ($message) ? $message : strtok($sql, "\n") . '...';
        $this->migrationEcho("		> execute SQL: $message ...");
        $time = microtime(true);
        $this->getDbConnection()->createCommand($sql)->execute($params);
        $this->migrationEcho(' done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n");
    }

    /**
     * @param array $consolidated_migrations
     *
     * @return bool
     */
    protected function consolidate($consolidated_migrations)
    {
        sort($consolidated_migrations);

        // Check for existing migrations
        $existing_migrations = $this->getDbConnection()->createCommand()
            ->select('version')
            ->from('tbl_migration')
            ->where(array('in', 'version', $consolidated_migrations))
            ->queryColumn();
        if (count($existing_migrations) == 0) {
            return false;
        } else {
            // Database has existing migrations, so check that last migration step to be consolidated was applied
            if (count($existing_migrations) == count($consolidated_migrations)) {
                // All previous migrations were applied, safe to consolidate
                $this->migrationEcho('Consolidating old migration data...');
                $deleted = $this->getDbConnection()->createCommand()
                    ->delete('tbl_migration', array('in', 'version', $consolidated_migrations));
                $this->migrationEcho("removed $deleted rows\n");
            } else {
                // Database is not migrated up to the consolidation point, cannot migrate
                $this->migrationEcho('In order to run this migration, you must migrate have migrated up to at least ' . end($consolidated_migrations) . "\n");
                $this->migrationEcho("This requires a pre-consolidation version of the code\n");
                throw new CException('Previous migrations missing or incomplete, migration not possible');
            }
        }

        return true;
    }

    protected function getDataDirectory($migrations_path, $data_directory = null)
    {
        if (!$data_directory) {
            $data_directory = get_class($this);
        }
        return $migrations_path . '/data/' . $data_directory . '/';
    }

    /**
     * Initialise tables with default data
     * Filenames must to be in the format "nn_tablename.csv", where nn is the processing order
     * FIXME: This needs to be refactored to use SQL rather than relying on models.
     */
    public function initialiseData($migrations_path, $update_pk = null, $data_directory = null)
    {
        $data_path = $this->getDataDirectory($migrations_path, $data_directory);
        $this->csvFiles = glob($data_path . '*.csv');

        if ($this->testdata) {
            echo "\nRunning test data import\n";
            $testdata_path = $migrations_path . DIRECTORY_SEPARATOR . 'testdata' . DIRECTORY_SEPARATOR . $data_directory . DIRECTORY_SEPARATOR;
            $testdataCsvFiles = glob($testdata_path . '*.csv');
            //echo "\nCSV FIles: " . var_export($this->csvFiles,true);
            //echo "\nCSV TEST FIles: " . var_export($testdataCsvFiles,true);
            $this->csvFiles = array_udiff($this->csvFiles, $testdataCsvFiles, 'self::compare_file_basenames');
            //echo "\nCSVFIles after diff : " . var_export($this->csvFiles,true);
            $this->csvFiles = array_merge_recursive($this->csvFiles, $testdataCsvFiles);
            //echo "\nIMPORTING CSVFIles in testdatamode : " . var_export($this->csvFiles,true);
        }

        foreach ($this->csvFiles as $file_path) {
            $table = substr(substr(basename($file_path), 0, -4), 3);
            $this->migrationEcho("Importing $table data...\n");
            $fh = fopen($file_path, 'r');
            $columns = fgetcsv($fh);
            $lookup_columns = array();
            foreach ($columns as &$column) {
                if (strpos($column, '=>') !== false) {
                    $column_parts = explode('=>', $column);
                    $column = trim($column_parts[0]);
                    $lookup_parts = explode('.', $column_parts[1]);
                    $lookup_table = trim($lookup_parts[0]);
                    $field = trim($lookup_parts[1]);
                    $lookup_columns[$column] = array('table' => $lookup_table, 'field' => $field);
                }
            }
            $row_count = 0;
            while (($record = fgetcsv($fh)) !== false) {
                ++$row_count;
                //echo "\nReading line " . $row_count . "\n";
                $data = array_combine($columns, $record);

                // Process lookup columns
                foreach ($lookup_columns as $lookup_column => $lookup) {
                    $lookup_table = $lookup['table'];
                    $field = $lookup['field'];
                    $lookup_value = $data[$lookup_column];
                    if ($this->testdata && ($lookup_table == 'episode' || $lookup_table == 'event')) {
                        $data[$lookup_column] = $this->getInsertReferentialObjectValue($lookup_table, $lookup_value);
                    } else {
                        $lookup_record = $this->dbConnection->createCommand()
                            ->select('*')
                            ->from($lookup_table)
                            ->where("$field = :value", array(':value' => $lookup_value))
                            ->queryRow();

                        $data[$lookup_column] = $lookup_record['id'];
                    }
                }

                // Process NULLs
                foreach ($data as &$value) {
                    if (strtolower($value) == 'null') {
                        $value = null;
                    }
                }
                //echo "\nTrying migration insert/update table: " . $table . " vals: " . var_export($data, true) . "\n";
                if ($update_pk) {
                    $pk = $data[$update_pk];
                    $existing = $this->getDbConnection()->createCommand()
                        ->select($update_pk)
                        ->from($table)
                        ->where($update_pk . ' = ?')
                        ->queryScalar(array($pk));

                    if ($existing) {
                        $this->update($table, $data, $update_pk . '= :pk', array(':pk' => $pk));
                    } else {
                        $this->insert($table, $data);
                        $this->insertsMap[$table][$row_count] = $this->getInsertId($table, $data);
                    }
                } else {
                    $this->insert($table, $data);
                    $this->insertsMap[$table][$row_count] = $this->getInsertId($table, $data);
                }
            }
            fclose($fh);
            $this->migrationEcho("$row_count records, done.\n");
        }
    }

    /**
     * @param $migrationName
     * @param $tables
     * @return OEMigrationResult
     * @throws OEMigrationException
     */
    public function exportData($migrationName, $tables)
    {
        if (!is_writable($this->getMigrationPath())) {
            throw new OEMigrationException('Migration folder is not writable/accessible: ' . $this->getMigrationPath());
        }

        if (!is_array($tables) || count($tables) == 0) {
            throw new OEMigrationException('No tables to export in the current database');
        }

        $migrationResult = new OEMigrationResult();
        $migrationResult->tables = array();
        foreach ($tables as $table) {
            $migrationResult->tables[@$table->name] = $this->exportTable($migrationName, $table);
        }
        $migrationResult->result = true;

        return $migrationResult;
    }

    public function getMigrationPath()
    {
        if (!isset($this->migrationPath)) {
            $this->migrationPath = 'application.migrations';
        }

        return Yii::getPathOfAlias($this->migrationPath);
    }

    public function setMigrationPath($path = null)
    {
        if (is_null($path)) {
            $path = 'application.migrations';
        }
        $this->migrationPath = $path;
    }

    public function setTestData($val)
    {
        $this->testdata = $val;
    }

    /**
     * @param string $migrationName - name of the migration, a folder with name will be created under data
     * @param CDbTableSchema $table - name of the table being exported
     *
     * @return int - return totRows
     *
     * @throws OEMigrationException
     */
    private function exportTable($migrationName, $table)
    {
        if (!is_subclass_of($table, 'CDbTableSchema')) {
            throw new OEMigrationException('Not a CDbTableSchema child class');
        }

        $dataPath = $this->getMigrationPath() . DIRECTORY_SEPARATOR . 'data';
        //create data folder if does not exist
        if (!file_exists($dataPath)) {
            $dataDirCreated = mkdir($dataPath);
            if (!$dataDirCreated) {
                throw new OEMigrationException('Data folder could not be created');
            }
        }
        $dataMigPath = $dataPath . DIRECTORY_SEPARATOR . $migrationName;
        //create data migration folder if does not exist
        if (!file_exists($dataMigPath)) {
            $dataMigDirCreated = mkdir($dataMigPath);
            if (!$dataMigDirCreated) {
                throw new OEMigrationException('Data migration folder could not be created');
            }
        }

        $columns = implode(',', $table->getColumnNames());

        $rowsQuery = $this->getDbConnection()->createCommand()
            ->select($columns)->from($table->name)->queryAll();

        $data = array();
        $data[] = $table->getColumnNames();
        $data = array_merge($data, $rowsQuery);

        $file = fopen($dataMigPath . DIRECTORY_SEPARATOR . '01_' . $table->name . '.csv', 'w');
        //i dont like manual file opening with no exceptions - might need refactoring later
        foreach ($data as $row) {
            fputcsv($file, $row);
        }

        fclose($file);

        return count($rowsQuery);
    }

    /**
     * Create a table with the standard OE columns and options.
     *
     * @param string $name
     * @param array $columns
     * @param bool $versioned
     */
    protected function createOETable($name, array $columns, $versioned = false, $fk_prefix = null)
    {
        $fk_prefix = is_null($fk_prefix) ? substr($name, 0, 56) : $fk_prefix;

        $columns = array_merge(
            $columns,
            array(
                'last_modified_user_id' => 'int unsigned not null default 1',
                'last_modified_date' => 'datetime not null default "1901-01-01 00:00:00"',
                'created_user_id' => 'int unsigned not null default 1',
                'created_date' => 'datetime not null default "1901-01-01 00:00:00"',
                "constraint {$fk_prefix}_lmui_fk foreign key (last_modified_user_id) references user (id)",
                "constraint {$fk_prefix}_cui_fk foreign key (created_user_id) references user (id)",
            )
        );

        $this->createTable($name, $columns, 'engine=InnoDB charset=utf8 collate=utf8_unicode_ci');

        if ($versioned) {
            foreach ($columns as $n => &$column) {
                if ($column == 'pk') {
                    $column = 'integer not null';
                }
                if (preg_match('/^constraint/i', $column)) {
                    unset($columns[$n]);
                }
                $column = str_ireplace(' unique', '', $column);
            }

            $columns = array_merge(
                $columns,
                array(
                    'version_date' => 'datetime not null',
                    'version_id' => 'pk',
                )
            );

            $this->createTable("{$name}_version", $columns, 'engine=InnoDB charset=utf8 collate=utf8_unicode_ci');
        }
    }

    /**
     * Convenience function to drop OE tables from db - versioned defaults to false to mirroe createOETable.
     *
     * @param      $name
     * @param bool $versioned
     */
    protected function dropOETable($name, $versioned = false)
    {
        if ($versioned) {
            $this->dropTable("{$name}_version");
        }

        $this->dropTable($name);
    }

    /**
     * Create a version table for the specified existing OE table.
     *
     * @param string $base_name Base table name
     */
    protected function versionExistingTable($base_name)
    {
        $res = $this->dbConnection->createCommand('show create table ' . $this->dbConnection->quoteTableName($base_name))->queryRow();
        $sql = $res['Create Table'];
        $start = strpos($sql, '(');
        $end = strrpos($sql, ')');
        $defs = explode("\n", trim(substr($sql, $start + 1, $end - $start - 1)));
        foreach ($defs as $n => &$def) {
            if (preg_match('/(?:PRIMARY|FOREIGN) KEY/', $def)) {
                unset($defs[$n]);
                continue;
            }
            $def = rtrim($def, ',');
            $def = str_replace('AUTO_INCREMENT', '', $def);
            $def = str_replace('UNIQUE', '', $def);
        }
        $defs[] = 'version_date datetime not null';
        $defs[] = 'version_id int unsigned not null auto_increment primary key';

        $this->createTable("{$base_name}_version", $defs, 'engine=InnoDB charset=utf8 collate=utf8_unicode_ci');
    }

    /**
     * @param string $event_type Class name of event type
     * @param string $name Name of event
     * @param array $params Supported values and defaults are: class_name, display_order (1), default (false), required (false), parent_name (null)
     *
     * @return int Element type ID
     */
    protected function createElementType($event_type, $name, array $params = array())
    {
        $event_type_id = $this->getIdOfEventTypeByClassName($event_type);

        $row = array(
            'name' => $name,
            'class_name' => $params['class_name'] ?? "Element_{$event_type}_" . str_replace(' ',
                    '', $name),
            'event_type_id' => $event_type_id,
            'display_order' => isset($params['display_order']) ? $params['display_order'] : 1,
            'default' => isset($params['default']) ? $params['default'] : 0,
            'required' => isset($params['required']) ? $params['required'] : 0,
            'group_title' => isset($params['group_title']) ? $params['group_title'] : '',
        );

        if (isset($params['group_name'])) {
            $row['element_group_id'] = $this->getIdOfElementGroupByName($params['group_name'], $event_type_id);

            if (!isset($params['group_title'])){
                $row['group_title'] = $params['group_name'];
            }
        }



        $this->insert('element_type', $row);

        return $this->dbConnection->lastInsertID;
    }

    /**
     * @param $event_type
     * @param $element_type_class
     * @throws CException
     */
    protected function deleteElementType($event_type, $element_type_class)
    {
        $event_type_id = $this->getIdOfEventTypeByClassName($event_type);
        $element_type_id = $this->getIdOfElementTypeByClassName($element_type_class, $event_type_id);
        $this->delete('ophciexamination_element_set_item', 'element_type_id = :element_type_id',
            [':element_type_id' => $element_type_id]);
        $this->delete('element_type', 'id = :id',
            [':id' => $element_type_id]);
    }


    /**
     * @description used within subclasses to find out the element_type id based on Class Name
     *
     * @param $class_name
     * @param $event_type_id
     * @return mixed - the value of the id. False is returned if there is no value.
     * @throws CException
     */
    protected function getIdOfElementTypeByClassName($class_name, $event_type_id = null)
    {
        $where = 'class_name=:class_name';
        $params = [':class_name' => $class_name];
        if ($event_type_id !== null) {
            $where .= ' AND event_type_id = :event_type_id';
            $params[':event_type_id'] = $event_type_id;
        }

        return $this->dbConnection->createCommand()
            ->select('id')
            ->from('element_type')
            ->where($where, $params)
            ->queryScalar();
    }

    /**
     * @description used within subclasses to find out the element_group id based on Name
     *
     * @param string $name
     * @param int|null $event_type_id
     * @return int - the value of the id. False is returned if there is no value.
     * @throws CException
     */
    protected function getIdOfElementGroupByName($name, $event_type_id = null)
    {
        $query = $this->dbConnection->createCommand()
            ->select('id')
            ->from('element_group');
        if ($event_type_id !== null) {
            $query->where('name = :name and event_type_id = :event_type_id', [
                ':name' => $name,
                ':event_type_id' => $event_type_id
            ]);
        } else {
            $query->where('name = :name', array(':name' => $name));
        }

        return $query->queryScalar();
    }

    /**
     * @param $name
     * @param $event_type
     * @param int $display_order
     * @return mixed|string
     */
    protected function createElementGroupForEventType($name, $event_type, $display_order = 1)
    {
        $event_type_id = $this->getIdOfEventTypeByClassName($event_type);

        $row = array(
            'name' => $name,
            'display_order' => $display_order,
            'event_type_id' => $event_type_id,
        );

        $exists = !empty($this->dbConnection->createCommand("SELECT id
                                                            FROM element_group
                                                            WHERE `name` = :group_name
                                                                AND event_type_id = :event_type_id")->queryScalar([':group_name' => $name, ':event_type_id' => $event_type_id]));
        // If the element group already exists then just update the display_order
        // Else insert the new row
        if ($exists){
            $this->update('element_group', $row, '`name` = :group_name AND event_type_id = :event_type_id', [':group_name' => $name, ':event_type_id' => $event_type_id]);
        }
        else {
            $this->insert('element_group', $row);
        }

        return $this->dbConnection->lastInsertID;
    }

    /**
     * @param $name
     * @param $event_type
     * @throws CException
     */
    protected function deleteElementGroupForEventType($name, $event_type)
    {
        $event_type_id = $this->getIdOfEventTypeByClassName($event_type);
        $this->delete(
            'element_group',
            'name = :name AND event_type_id = :event_type_id', [
            ':name' => $name,
            ':event_type_id' => $event_type_id
        ]);
    }

    /**
     * Get the id of the event type
     *
     * @param $className
     * @return mixed - the value of the id. False is returned if there is no value.
     * @throws CException
     */
    protected function getIdOfEventTypeByClassName($className)
    {
        return $this->dbConnection->createCommand()
            ->select('id')
            ->from('event_type')
            ->where('class_name=:class_name', array(':class_name' => $className))
            ->queryScalar();
    }

    /**
     * @param $name
     * @return string
     * @throws CException
     */
    protected function getIdOfSubspecialtyByName($name)
    {
        return $this->dbConnection->createCommand()
            ->select('id')
            ->from('subspecialty')
            ->where('LOWER(name)=:name', [':name' => strtolower($name)])
            ->queryScalar();
    }

    /**
     * @param $eventTypeName - string
     * @param $eventTypeClass - string
     * @param $eventTypeGroup - string
     *
     * @return mixed - the id value of the event_type. False is returned if there is no value.
     *
     * @throws OEMigrationException
     */
    protected function insertOEEventType($eventTypeName, $eventTypeClass, $eventTypeGroup)
    {
        // Get the event group id for this event type
        $group_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('event_group')
            ->where('code=:code', array(':code' => $eventTypeGroup))
            ->queryScalar();

        if ($group_id === false) {
            throw new OEMigrationException('Group id could not be found for $eventTypeGroup: ' . $eventTypeGroup);
        }

        // Create the new event_type (if not already present)
        $event_type_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('event_type')
            ->where('class_name = :class_name', array(':class_name' => $eventTypeClass))
            ->queryScalar();
        if ($event_type_id) {
            $this->migrationEcho('Updating event_type, event_type_name: ' . $eventTypeName . ' event_type_class: ' . $eventTypeClass . ' event_type_group: ' . $eventTypeGroup . "\n");
            $this->update(
                'event_type',
                array(
                    'name' => $eventTypeName,
                    'event_group_id' => $group_id,
                ),
                'id = :event_type_id',
                array(':event_type_id' => $event_type_id)
            );
        } else {
            $this->migrationEcho('Inserting event_type, event_type_name: ' . $eventTypeName . ' event_type_class: ' . $eventTypeClass . ' event_type_group: ' . $eventTypeGroup . "\n");
            $this->insert(
                'event_type',
                array(
                    'name' => $eventTypeName,
                    'event_group_id' => $group_id,
                    'class_name' => $eventTypeClass,
                )
            );
            $event_type_id = $this->dbConnection->createCommand()
                ->select('id')
                ->from('event_type')
                ->where('class_name = :class_name', array(':class_name' => $eventTypeClass))
                ->queryScalar();
            if (!$event_type_id) {
                throw new CException('Failed to insert event type');
            }
        }

        return $event_type_id;
    }

    /**
     * @param array $element_types
     * @param int $event_type_id
     *
     * @return array - list of the element_types ids inserted
     */
    protected function insertOEElementType(array $element_types, $event_type_id)
    {
        $display_order = 1;
        $element_type_ids = array();
        foreach ($element_types as $element_type_class => $element_type_data) {
            $default = isset($element_type_data['default']) ? $element_type_data['default'] : 1;
            $confirmedDisplayOrder = isset($element_type_data['display_order']) ?
                $element_type_data['display_order'] : $display_order * 10;
            $required = isset($element_type_data['required']) ? $element_type_data['required'] : null;

            $to_insert = array(
                'name' => $element_type_data['name'],
                'class_name' => $element_type_class,
                'event_type_id' => $event_type_id,
                'display_order' => $confirmedDisplayOrder,
                'default' => $default,
                'required' => $required,
            );

            $element_type = $this->dbConnection->schema->getTable('element_type');
            if (isset($element_type->columns['element_group_id'])) {
                //this is needed to se the parent id for those elements set as children elements of another element type
                $thisGroupId = isset($element_type_data['element_group_id']) ? $element_type_data['element_group_id'] : null;
                $to_insert['element_group_id'] = $thisGroupId;
            } else {
                if (isset($element_type->columns['parent_element_type_id'])) {
                    $thisParentId = isset($element_type_data['parent_element_type_id']) ? $this->getIdOfElementTypeByClassName($element_type_data['parent_element_type_id']) : null;
                    $to_insert['parent_element_type_id'] = $thisParentId;
                }
            }

            $this->insert(
                'element_type',
                $to_insert
            );

            $this->migrationEcho(
                'Added element type, element_type_class: ' . $element_type_class . ' element type properties: '
                . var_export($element_type_data, true) . ' event_type_id: ' . $event_type_id . " \n"
            );

            // Insert element type id into element type array
            $element_type_ids[] = $this->dbConnection->createCommand()
                ->select('id')
                ->from('element_type')
                ->where('class_name=:class_name', array(':class_name' => $element_type_class))
                ->queryScalar();

            ++$display_order;
        }

        return $element_type_ids;
    }

    /**
     * @description method needed to delete records from multi key tables
     *
     * @param string $tableName
     * @param array $fieldsValsArray
     *                                example of fieldsValsArray
     *                                $fieldsValsArray should look like
     *
     * array(
     *        array('column_name'=>'value', 'column_name'=>'val'),
     * )
     */
    protected function deleteOEFromMultikeyTable($tableName, array $fieldsValsArray)
    {
        foreach ($fieldsValsArray as $fieldsValArray) {
            $fieldsList = '';
            $fieldsValArrayMap = array();
            $isFirst = true;
            foreach ($fieldsValArray as $fieldKey => $fieldVal) {
                $fieldsList .= ($isFirst ? ' and ' : '');
                $fieldsList .= $fieldKey . "=:$fieldKey ";

                $fieldsValArrayMap[":$fieldKey "] = $fieldVal;

                $isFirst = false;
            }
            $this->delete($tableName, $fieldsList, $fieldsValArrayMap);
            $this->migrationEcho(
                "\nDeleted  in table : $tableName. Fields : "
                . $fieldsList . ' value: ' . var_export($fieldsValArrayMap, true) . "\n"
            );
        }
    }

    /**
     * @param $tableName
     * @param $columnName
     * @param $columnType
     * @param bool $versioned
     */
    public function alterOEColumn($tableName, $columnName, $columnType, $versioned = false)
    {
        $this->alterColumn($tableName, $columnName, $columnType);

        if ($versioned) {
            if ($this->verifyTableVersioned($tableName)) {
                $this->alterColumn($tableName . '_version', $columnName, $columnType);
            }
        }
    }

    public function createArchiveTable($table)
    {
        $this->migrationEcho("Creating archive table for $table->name ...\n");

        $a = $this->dbConnection->createCommand("show create table $table->name;")->queryRow();

        $create = $a['Create Table'];

        $create = preg_replace('/CREATE TABLE `(.*?)`/', "CREATE TABLE `{$table->name}_version`", $create);

        preg_match_all('/  KEY `(.*?)`/', $create, $m);

        foreach ($m[1] as $key) {
            $_key = $key;

            if (strlen($_key) <= 60) {
                $_key = 'acv_' . $_key;
            } else {
                $_key[0] = 'a';
                $_key[1] = 'c';
                $_key[2] = 'v';
                $_key[3] = '_';
            }

            $create = preg_replace("/KEY `{$key}`/", "KEY `$_key`", $create);
        }

        preg_match_all('/CONSTRAINT `(.*?)`/', $create, $m);

        foreach ($m[1] as $key) {
            $_key = $key;

            if (strlen($_key) <= 60) {
                $_key = 'acv_' . $_key;
            } else {
                $_key[0] = 'a';
                $_key[1] = 'c';
                $_key[2] = 'v';
                $_key[3] = '_';
            }

            $create = preg_replace("/CONSTRAINT `{$key}`/", "CONSTRAINT `$_key`", $create);
        }

        $this->dbConnection->createCommand($create)->query();

        $this->alterColumn("{$table->name}_version", 'id', 'int(10) unsigned NOT NULL');
        $this->dropPrimaryKey('id', "{$table->name}_version");

        $this->createIndex("{$table->name}_aid_fk", "{$table->name}_version", 'id');
        $this->addForeignKey("{$table->name}_aid_fk", "{$table->name}_version", 'id', $table->name, 'id');

        $this->addColumn("{$table->name}_version", 'version_date', "datetime not null default '1900-01-01 00:00:00'");

        $this->addColumn("{$table->name}_version", 'version_id', 'int(10) unsigned NOT NULL');
        $this->addPrimaryKey('version_id', "{$table->name}_version", 'version_id');
        $this->alterColumn("{$table->name}_version", 'version_id', 'int(10) unsigned NOT NULL AUTO_INCREMENT');
    }

    private function compare_file_basenames($a, $b)
    {
        $afile = basename($a);
        $bfile = basename($b);
        if ($afile == $bfile) {
            return 0;
        } elseif ($afile > $bfile) {
            return 1;
        }

        return -1;
    }

    public function insertIfNotExist($table, $attributes = [])
    {
        $command = Yii::app()->db->createCommand()
            ->select('id')
            ->from($table);

        $command->where("1=1");
        foreach ($attributes as $attr => $val) {
            $command->andWhere("{$attr} = :{$attr}", [":$attr" => $val]);
        }

        $is_exist = $command->queryRow();
        if (!$is_exist) {
            $this->insert($table, $attributes);
        } else {
            echo "\n    > SKIP insert into $table ... value already exist\n";
        }
    }

    /**
     * @description - return csvFiles array of files that will be imported
     *
     * @return null|array
     */
    public function getCsvFiles()
    {
        return $this->csvFiles ? $this->csvFiles : null;
    }

    public function getInsertId($table)
    {
        $schema = $this->dbConnection->getSchema()->getTable($table);
        if (!$schema) {
            throw new OEMigrationException('Table ' . $table . ' does not exist');
        }
        if ($schema->primaryKey != 'id') {
            return;
        }

        return $this->dbConnection->getLastInsertID($schema->sequenceName);
    }

    public function getInsertReferentialObjectValue($object_type, $pointer)
    {
        if (isset($this->insertsMap[$object_type][$pointer])) {
            return $this->insertsMap[$object_type][$pointer];
        }

        return;
    }

    protected function migrationEcho($msg)
    {
        if ($this->verbose) {
            echo $msg;
        }
    }

    public function setVerbose($verbose = true)
    {
        $this->verbose = $verbose;
    }

    /**
     * @param $event_type_id
     * @param $code
     * @param $method
     * @param $description
     * @param $global_scope
     * @throws Exceptio
     */
    public function registerShortcode($event_type_id, $code, $method, $description, $global_scope = 1)
    {
        if (!preg_match('/^[a-zA-Z]{3}$/', $code)) {
            throw new Exception("Invalid shortcode: $code");
        }

        $default_code = $code;

        if ($this->dbConnection->createCommand()->select('*')->from('patient_shortcode')->where('code = :code',
            array(':code' => strtolower($code)))->queryRow()) {
            $n = '00';
            while ($this->dbConnection->createCommand()->select('*')->from('patient_shortcode')->where('code = :code',
                array(':code' => 'z' . $n))->queryRow()) {
                $n = str_pad((int)$n + 1, 2, '0', STR_PAD_LEFT);
            }
            $code = "z$n";

            echo "Warning: attempt to register duplicate shortcode '$default_code', replaced with 'z$n'\n";
        }

        if (!$this->dbConnection->createCommand()->select('id')->from('event_type')->where('id = :id',
            array(':id' => $event_type_id))->queryScalar()) {
            $event_type_id = null;
        }

        $cols = array(
            'event_type_id' => $event_type_id,
            'code' => $code,
            'default_code' => $default_code,
            'method' => $method,
            'description' => $description
        );

        // global scope was added later to the table. Uses of this method in
        // migrations before this column was added will fail if we attempt to
        // set a column that does not exist. It only has an effect if set to
        // false (defaults to true in the table), so we use that as an
        // indicator that the call should set the value.
        if (!$global_scope) {
            $cols['global_scope'] = 0;
        }

        $this->insert('patient_shortcode', $cols);
    }

    /**
     * Create $dest table and duplicate data from $source into it
     *
     * @param $source
     * @param $dest
     * @param $cols
     */
    public function duplicateTable($source, $dest, $cols)
    {
        $this->createOETable($dest, array_merge(
            array('id' => 'pk', 'active' => 'boolean default true'),
            $cols
        ), true);
        $source_rows = $this->dbConnection->createCommand()
            // force the id to ensure maintaining it
            ->select(array_merge(array('id'), array_keys($cols)))
            ->from($source)
            ->queryAll();
        foreach ($source_rows as $row) {
            $this->insert($dest, $row);
        }
    }

    public function setEventTypeRBACSuffix($class_name, $rbac_operation_suffix)
    {
        $event_type_id = $this->dbConnection->createCommand()
            ->select('id')
            ->from('event_type')
            ->where('class_name = :class_name', array(':class_name' => $class_name))
            ->queryScalar();

        $this->update('event_type', array('rbac_operation_suffix' => $rbac_operation_suffix), "id = $event_type_id");
    }

    public function addRole($role_name)
    {
        $this->insert('authitem', array('name' => $role_name, 'type' => 2));
    }

    public function addTask($task_name)
    {
        $this->insert('authitem', array('name' => $task_name, 'type' => 1));
    }

    public function addOperation($oprn_name)
    {
        $this->insert('authitem', array('name' => $oprn_name, 'type' => 0));
    }

    public function addTaskToRole($task_name, $role_name)
    {
        $this->insert('authitemchild', array('parent' => $role_name, 'child' => $task_name));
    }

    public function addOperationToTask($oprn_name, $task_name)
    {
        $this->insert('authitemchild', array('parent' => $task_name, 'child' => $oprn_name));
    }

    /**
     * Add a column to an OE table and its corresponding versioning table.
     *
     * @param $table string Table name (do not include 'version' at the end unless it appears twice in the table's name).
     * @param $column string Column name.
     * @param $type string Column type.
     * @param bool $versioned
     */
    public function addOEColumn($table, $column, $type, $versioned = false)
    {
        $this->addColumn($table, $column, $type);
        if ($versioned && $this->verifyTableVersioned($table)) {
            $this->addColumn($table . '_version', $column, $type);
        }
    }

    /**
     * Drop a column from an OE table and its corresponding versioning table.
     *
     * @param $table string Table name (do not include 'version' at the end unless it appears twice in the table's name).
     * @param $column string Column name
     * @param bool $versioned
     */
    public function dropOEColumn($table, $column, $versioned = false)
    {
        $this->dropColumn($table, $column);
        if ($versioned && $this->verifyTableVersioned($table)) {
            $this->dropColumn($table . '_version', $column);
        }
    }

    /**
     * Rename a column on an OE table and its corresponding versioning table
     *
     * @param $table
     * @param $name
     * @param $newName
     * @param bool $versioned
     */
    public function renameOEColumn($table, $name, $newName, $versioned = false)
    {
        $this->renameColumn($table, $name, $newName);
        if ($versioned && $this->verifyTableVersioned($table)) {
            $this->renameColumn($table . '_version', $name, $newName);
        }
    }

    public function removeOperationFromTask($oprn_name, $task_name)
    {
        $this->delete('authitemchild', 'parent = :task_name and child = :oprn_name',
            array(":task_name" => $task_name, ':oprn_name' => $oprn_name));
    }

    public function removeTaskFromRole($task_name, $role_name)
    {
        $this->delete('authitemchild', 'parent = :role_name and child = :task_name',
            array(":role_name" => $role_name, ':task_name' => $task_name));
    }

    public function removeRole($role_name)
    {
        $this->delete('authitem', "name = :name and type=2", array(':name' => $role_name));
    }

    public function removeTask($task_name)
    {
        $this->delete('authitem', "name = :name and type=1", array(':name' => $task_name));
    }

    public function removeOperation($oprn_name)
    {
        $this->delete('authitem', "name = :name and type=0", array(':name' => $oprn_name));
    }

    /**
     * Returns search index id
     *
     * @param string $term
     * @param null $parent_id
     * @return int|null
     * @throws CException
     */
    public function getSearchIndexByTerm(string $term, $parent_id = null): ?int
    {
        $params[] = $term;
        if ($parent_id) {
            $params[] = $parent_id;
        }

        $id = $this->getDbConnection()->createCommand()
            ->select('id')
            ->from('index_search')
            ->where('primary_term = ?' . ($parent_id ? ' AND parent = ?' : ''))
            ->queryScalar($params);

        return is_numeric($id) ? $id : null;
    }

    /**
     * Checks if a named column exists on a named table
     * @param $table_name Name of he table to check for the column on
     * @param $column_name Name of the column to check for
     * @return bool true if the column exists
     */
    protected function verifyColumnExists($table_name, $column_name){
        $cols = $this->dbConnection->createCommand("SHOW COLUMNS FROM `" . $table_name . "` LIKE '" .$column_name ."'")->queryScalar([ ':table_name' => $table_name ]);
        return !empty($cols);
    }

    /**
     * Checks if a named foreign keys exists on a named table
     * @param $table_name Name of he table to check the FK on
     * @param $key_name Name of the FK contraint to check for
     * @return bool true if the FK exists
     */
    protected function verifyForeignKeyExists($table_name, $key_name){
        $fk_exists = $this->dbConnection->createCommand('   SELECT count(*)
                                                            FROM information_schema.table_constraints
                                                            WHERE table_schema = DATABASE()
                                                                AND table_name = :table_name
                                                                AND constraint_name = :key_name
                                                                AND constraint_type = "FOREIGN KEY"')->queryScalar([ ':table_name' => $table_name, ':key_name' => $key_name ]);

        return !empty($fk_exists);
    }

    /**
     * Checks if a named table exisst in the schema
     * @param $table_name Name of he table to check for
     * @return bool true if the table exists
     */
    protected function verifyTableExists($table_name)
    {
        return $this->dbConnection->schema->getTable($table_name) !== null;
    }

    /**
     * @param $table_name
     * @param bool $warn
     * @return bool
     */
    protected function verifyTableVersioned($table_name, $warn = true)
    {
        if ($this->dbConnection->schema->getTable($table_name . '_version') !== null) {
            return true;
        }
        if ($warn) {
            $this->migrationEcho(
                "\nWarning: $table_name specified as versioned but the version table does not exist"
            );
        }
        return false;
    }

    /**
     * remove the given setting for the given class from metadata and the various context
     * setting tables
     *
     * @param $keyName
     * @param null $element_cls
     * @throws CException
     */
    protected function removeOESettingForElementType($keyName, $element_cls = null)
    {
        $conditions = "`key`= ?";
        $params = [$keyName];

        if ($element_cls) {
            $conditions .= ' AND element_type_id = ?';
            $params[] = $this->getIdOfElementTypeByClassName($element_cls);
        }

        foreach (array_keys(SettingMetadata::$CONTEXT_CLASSES) as $cls) {
            $context_table_name = $cls::model()->tableName();
            if ($this->verifyTableExists($context_table_name)) {
                $this->delete($context_table_name, $conditions, $params);
            }
        }

        $this->delete('setting_metadata', $conditions, $params);
    }

    protected function renameOETable($current_name, $new_name, $versioned = false)
    {
        $this->renameTable($current_name, $new_name);

        if ($versioned && $this->verifyTableVersioned($current_name)) {
            $this->renameTable($current_name . '_version', $new_name . '_version');
        }
    }

    /**
     * Gets the settings field type id by name
     *
     * @param $field_type
     * @return int
     * @throws CException
     */
    protected function getSettingFieldIdByName(string $name)
    {
        return $this->dbConnection->createCommand()
            ->select('id')
            ->from('setting_field_type')
            ->where('name = :name', [':name' => $name])
            ->queryScalar();
    }

    protected function isColumnExist(string $table, string $column): bool
    {
        return isset($this->dbConnection->schema->getTable($table, true)->columns[$column]);
    }

    public function createOrReplaceView($view_name, $select)
    {
        $this->dbConnection->createCommand('create or replace view ' . $view_name . ' as ' . $select)->execute();
    }

    public function dropView($view_name)
    {
        $this->dbConnection->createCommand('drop view ' . $view_name)->execute();
    }
}
