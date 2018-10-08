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
     * @param array  $params input parameters (name=>value) for the SQL execution. See {@link CDbCommand::execute} for more details.
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

    /**
     * Initialise tables with default data
     * Filenames must to be in the format "nn_tablename.csv", where nn is the processing order
     * FIXME: This needs to be refactored to use SQL rather than relying on models.
     */
    public function initialiseData($migrations_path, $update_pk = null, $data_directory = null)
    {
        if (!$data_directory) {
            $data_directory = get_class($this);
        }
        $data_path = $migrations_path . '/data/' . $data_directory . '/';
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
     * @param string         $migrationName - name of the migration, a folder with name will be created under data
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
     * @param array  $columns
     * @param bool   $versioned
     */
    protected function createOETable($name, array $columns, $versioned = false)
    {
        $fk_prefix = substr($name, 0, 56);

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
     * @param array  $params Supported values and defaults are: class_name, display_order (1), default (false), required (false), parent_name (null)
     *
     * @return int Element type ID
     */
    protected function createElementType($event_type, $name, array $params = array())
    {
        $row = array(
            'name' => $name,
            'class_name' => isset($params['class_name']) ? $params['class_name'] : "Element_{$event_type}_" . str_replace(' ', '', $name),
            'event_type_id' => $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name = ?', array($event_type))->queryScalar(),
            'display_order' => isset($params['display_order']) ? $params['display_order'] : 1,
            'default' => isset($params['default']) ? $params['default'] : false,
            'required' => isset($params['required']) ? $params['required'] : false,
        );

        if (isset($params['parent_name'])) {
            $parent_class = "Element_{$event_type}_{$params['parent_name']}";
            $row['parent_element_type_id'] = $this->getIdOfElementTypeByClassName($parent_class);
        } elseif (isset($params['parent_class'])) {
            // introduced for supporting elements that are a little more flexible on class name vs name
            $row['parent_element_type_id'] = $this->getIdOfElementTypeByClassName($params['parent_class']);
        }

        $this->insert('element_type', $row);

        return $this->dbConnection->lastInsertID;
    }

    /**
     * @description used within subclasses to find out the element_type id based on Class Name
     *
     * @param $className - string
     *
     * @return mixed - the value of the id. False is returned if there is no value.
     */
    protected function getIdOfElementTypeByClassName($className)
    {
        return $this->dbConnection->createCommand()
            ->select('id')
            ->from('element_type')
            ->where('class_name=:class_name', array(':class_name' => $className))
            ->queryScalar();
    }

    /**
     * Get the id of the event type
     *
     * @param $className
     * @return mixed - the value of the id. False is returned if there is no value.
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
     * @param int   $event_type_id
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
            //this is needed to se the parent id for those elements set as children elements of another element type
            $thisParentId = isset($element_type_data['parent_element_type_id']) ?
                $this->getIdOfElementTypeByClassName($element_type_data['parent_element_type_id']) : null;
            $required = isset($element_type_data['required']) ? $element_type_data['required'] : null;

            $this->insert(
                'element_type',
                array(
                    'name' => $element_type_data['name'],
                    'class_name' => $element_type_class,
                    'event_type_id' => $event_type_id,
                    'display_order' => $confirmedDisplayOrder,
                    'default' => $default,
                    'parent_element_type_id' => $thisParentId,
                    'required' => $required,
                )
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
     * @param array  $fieldsValsArray
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

    public function createArchiveTable($table)
    {
        $this->migrationEcho("Creating archive table for $table->name ...\n");

        $a = Yii::app()->db->createCommand("show create table $table->name;")->queryRow();

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

        Yii::app()->db->createCommand($create)->query();

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

    private function migrationEcho($msg)
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

        if ($this->dbConnection->createCommand()->select('*')->from('patient_shortcode')->where('code = :code', array(':code' => strtolower($code)))->queryRow()) {
            $n = '00';
            while ($this->dbConnection->createCommand()->select('*')->from('patient_shortcode')->where('code = :code', array(':code' => 'z'.$n))->queryRow()) {
                $n = str_pad((int) $n + 1, 2, '0', STR_PAD_LEFT);
            }
            $code = "z$n";

            echo "Warning: attempt to register duplicate shortcode '$default_code', replaced with 'z$n'\n";
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

    public function removeOperationFromTask($oprn_name, $task_name)
    {
        $this->delete('authitemchild', 'parent = :task_name and child = :oprn_name', array(":task_name" => $task_name, ':oprn_name' => $oprn_name));
    }

    public function removeTaskFromRole($task_name, $role_name)
    {
        $this->delete('authitemchild', 'parent = :role_name and child = :task_name', array(":role_name" => $role_name, ':task_name' => $task_name));
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
}
