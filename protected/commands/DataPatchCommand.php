<?php

/**
 * DataPatchCommand is a copy of the Yii built in MigrateCommand.
 *
 * The following modification have been made
 *  - Yii::getPathOfAlias removed so the migration can accept external path
 *  - Migration table name holder property $migrationTable became CONST, it cannot be overriden from CLI, cannot run accidentaly on main migration table
 *  - Property migrationPath renamed to dataPatchPath to be more verbose and the default value is empty string, each time the user has to
 *    specify the --dataPatchPath
 */
class DataPatchCommand extends CConsoleCommand
{
    const BASE_MIGRATION = 'm000000_000000_base';

    /**
     * @var string the directory that stores the migrations. This must be specified
     *             in terms of a full path, and the corresponding directory must exist.
     *             /opt/openeyes/Openeyes-Institution/env/50_prod/patch
     */
    public $dataPatchPath = '';
    /**
     * The name of the table for keeping applied migration information.
     * This table will be automatically created if not exists.
     * The table structure is: (version varchar(255) primary key, apply_time integer).
     */
    const migrationTable = 'datapatch_migration';
    /**
     * @var string the application component ID that specifies the database connection for
     *             storing migration information. Defaults to 'db'.
     */
    public $connectionID = 'db';
    /**
     * @var string the path of the template file for generating new migrations. This
     *             must be specified in terms of a full path (e.g. /opt/openeyese/Openeyes-Institution/template).
     *             If not set, an internal template will be used.
     */
    public $templateFile;
    /**
     * @var string the default command action. It defaults to 'up'.
     */
    public $defaultAction = 'up';
    /**
     * @var bool whether to execute the migration in an interactive mode. Defaults to true.
     *           Set this to false when performing migration in a cron job or background process.
     */
    public $interactive = true;

    public function beforeAction($action, $params)
    {
        $yiiVersion = Yii::getVersion();
        echo "\nData Patch  Migration Tool v1.0 (based on Yii v{$yiiVersion} Migration Tool)\n\n";
        echo 'Data Patch Migration Table : '.self::migrationTable."\n\n";
        echo 'Data Patch Path : '.$this->dataPatchPath."\n\n";

        $path = $this->dataPatchPath;
        if ($path === false || !is_dir($path)) {
            echo 'Error: The migration directory does not exist: '.$this->dataPatchPath."\n";
            exit(1);
        }

        return parent::beforeAction($action, $params);
    }

    public function actionUp($args)
    {
        if (($migrations = $this->getNewMigrations()) === array()) {
            echo "No new migration found. Your system is up-to-date.\n";

            return 0;
        }

        $total = count($migrations);
        $step = isset($args[0]) ? (int) $args[0] : 0;
        if ($step > 0) {
            $migrations = array_slice($migrations, 0, $step);
        }

        $n = count($migrations);
        if ($n === $total) {
            echo "Total $n new ".($n === 1 ? 'migration' : 'migrations')." to be applied:\n";
        } else {
            echo "Total $n out of $total new ".($total === 1 ? 'migration' : 'migrations')." to be applied:\n";
        }
        foreach ($migrations as $migration) {
            echo "    $migration\n";
        }
        echo "\n";

        if ($this->confirm('Apply the above '.($n === 1 ? 'migration' : 'migrations').'?')) {
            foreach ($migrations as $migration) {
                if ($this->migrateUp($migration) === false) {
                    echo "\nMigration failed. All later migrations are canceled.\n";

                    return 2;
                }
            }
            echo "\nMigrated up successfully.\n";
        }
    }

    public function actionDown($args)
    {
        $step = isset($args[0]) ? (int) $args[0] : 1;
        if ($step < 1) {
            echo "Error: The step parameter must be greater than 0.\n";

            return 1;
        }

        if (($migrations = $this->getMigrationHistory($step)) === array()) {
            echo "No migration has been done before.\n";

            return 0;
        }
        $migrations = array_keys($migrations);

        $n = count($migrations);
        echo "Total $n ".($n === 1 ? 'migration' : 'migrations')." to be reverted:\n";
        foreach ($migrations as $migration) {
            echo "    $migration\n";
        }
        echo "\n";

        if ($this->confirm('Revert the above '.($n === 1 ? 'migration' : 'migrations').'?')) {
            foreach ($migrations as $migration) {
                if ($this->migrateDown($migration) === false) {
                    echo "\nMigration failed. All later migrations are canceled.\n";

                    return 2;
                }
            }
            echo "\nMigrated down successfully.\n";
        }
    }

    public function actionRedo($args)
    {
        $step = isset($args[0]) ? (int) $args[0] : 1;
        if ($step < 1) {
            echo "Error: The step parameter must be greater than 0.\n";

            return 1;
        }

        if (($migrations = $this->getMigrationHistory($step)) === array()) {
            echo "No migration has been done before.\n";

            return 0;
        }
        $migrations = array_keys($migrations);

        $n = count($migrations);
        echo "Total $n ".($n === 1 ? 'migration' : 'migrations')." to be redone:\n";
        foreach ($migrations as $migration) {
            echo "    $migration\n";
        }
        echo "\n";

        if ($this->confirm('Redo the above '.($n === 1 ? 'migration' : 'migrations').'?')) {
            foreach ($migrations as $migration) {
                if ($this->migrateDown($migration) === false) {
                    echo "\nMigration failed. All later migrations are canceled.\n";

                    return 2;
                }
            }
            foreach (array_reverse($migrations) as $migration) {
                if ($this->migrateUp($migration) === false) {
                    echo "\nMigration failed. All later migrations are canceled.\n";

                    return 2;
                }
            }
            echo "\nMigration redone successfully.\n";
        }
    }

    public function actionTo($args)
    {
        if (isset($args[0])) {
            $version = $args[0];
        } else {
            $this->usageError('Please specify which version to migrate to.');
        }

        $originalVersion = $version;
        if (preg_match('/^m?(\d{6}_\d{6})(_.*?)?$/', $version, $matches)) {
            $version = 'm'.$matches[1];
        } else {
            echo "Error: The version option must be either a timestamp (e.g. 101129_185401)\nor the full name of a migration (e.g. m101129_185401_create_user_table).\n";

            return 1;
        }

        // try migrate up
        $migrations = $this->getNewMigrations();
        foreach ($migrations as $i => $migration) {
            if (strpos($migration, $version.'_') === 0) {
                return $this->actionUp(array($i + 1));
            }
        }

        // try migrate down
        $migrations = array_keys($this->getMigrationHistory(-1));
        foreach ($migrations as $i => $migration) {
            if (strpos($migration, $version.'_') === 0) {
                if ($i === 0) {
                    echo "Already at '$originalVersion'. Nothing needs to be done.\n";

                    return 0;
                } else {
                    return $this->actionDown(array($i));
                }
            }
        }

        echo "Error: Unable to find the version '$originalVersion'.\n";

        return 1;
    }

    public function actionMark($args)
    {
        if (isset($args[0])) {
            $version = $args[0];
        } else {
            $this->usageError('Please specify which version to mark to.');
        }
        $originalVersion = $version;
        if (preg_match('/^m?(\d{6}_\d{6})(_.*?)?$/', $version, $matches)) {
            $version = 'm'.$matches[1];
        } else {
            echo "Error: The version option must be either a timestamp (e.g. 101129_185401)\nor the full name of a migration (e.g. m101129_185401_create_user_table).\n";

            return 1;
        }

        $db = $this->getDbConnection();

        // try mark up
        $migrations = $this->getNewMigrations();
        foreach ($migrations as $i => $migration) {
            if (strpos($migration, $version.'_') === 0) {
                if ($this->confirm("Set migration history at $originalVersion?")) {
                    $command = $db->createCommand();
                    for ($j = 0;$j <= $i;++$j) {
                        $command->insert(self::migrationTable, array(
                                'version' => $migrations[$j],
                                'apply_time' => time(),
                        ));
                    }
                    echo "The migration history is set at $originalVersion.\nNo actual migration was performed.\n";
                }

                return 0;
            }
        }

        // try mark down
        $migrations = array_keys($this->getMigrationHistory(-1));
        foreach ($migrations as $i => $migration) {
            if (strpos($migration, $version.'_') === 0) {
                if ($i === 0) {
                    echo "Already at '$originalVersion'. Nothing needs to be done.\n";
                } else {
                    if ($this->confirm("Set migration history at $originalVersion?")) {
                        $command = $db->createCommand();
                        for ($j = 0;$j < $i;++$j) {
                            $command->delete(self::migrationTable, $db->quoteColumnName('version').'=:version', array(':version' => $migrations[$j]));
                        }
                        echo "The migration history is set at $originalVersion.\nNo actual migration was performed.\n";
                    }
                }

                return 0;
            }
        }

        echo "Error: Unable to find the version '$originalVersion'.\n";

        return 1;
    }

    public function actionHistory($args)
    {
        $limit = isset($args[0]) ? (int) $args[0] : -1;
        $migrations = $this->getMigrationHistory($limit);
        if ($migrations === array()) {
            echo "No migration has been done before.\n";
        } else {
            $n = count($migrations);
            if ($limit > 0) {
                echo "Showing the last $n applied ".($n === 1 ? 'migration' : 'migrations').":\n";
            } else {
                echo "Total $n ".($n === 1 ? 'migration has' : 'migrations have')." been applied before:\n";
            }
            foreach ($migrations as $version => $time) {
                echo '    ('.date('Y-m-d H:i:s', $time).') '.$version."\n";
            }
        }
    }

    public function actionNew($args)
    {
        $limit = isset($args[0]) ? (int) $args[0] : -1;
        $migrations = $this->getNewMigrations();
        if ($migrations === array()) {
            echo "No new migrations found. Your system is up-to-date.\n";
        } else {
            $n = count($migrations);
            if ($limit > 0 && $n > $limit) {
                $migrations = array_slice($migrations, 0, $limit);
                echo "Showing $limit out of $n new ".($n === 1 ? 'migration' : 'migrations').":\n";
            } else {
                echo "Found $n new ".($n === 1 ? 'migration' : 'migrations').":\n";
            }

            foreach ($migrations as $migration) {
                echo '    '.$migration."\n";
            }
        }
    }

    public function actionCreate($args)
    {
        if (isset($args[0])) {
            $name = $args[0];
        } else {
            $this->usageError('Please provide the name of the new migration.');
        }

        if (!preg_match('/^\w+$/', $name)) {
            echo "Error: The name of the migration must contain letters, digits and/or underscore characters only.\n";

            return 1;
        }

        $name = 'm'.gmdate('ymd_His').'_'.$name;
        $content = strtr($this->getTemplate(), array('{ClassName}' => $name));
        $file = $this->dataPatchPath.DIRECTORY_SEPARATOR.$name.'.php';

        if ($this->confirm("Create new migration '$file'?")) {
            file_put_contents($file, $content);
            echo "New migration created successfully.\n";
        }
    }

    public function confirm($message, $default = false)
    {
        if (!$this->interactive) {
            return true;
        }

        return parent::confirm($message, $default);
    }

    protected function migrateUp($class)
    {
        if ($class === self::BASE_MIGRATION) {
            return;
        }

        echo "*** applying $class\n";
        $start = microtime(true);
        $migration = $this->instantiateMigration($class);
        if ($migration->up() !== false) {
            $this->getDbConnection()->createCommand()->insert(self::migrationTable, array(
                        'version' => $class,
                        'apply_time' => time(),
                ));
            $time = microtime(true) - $start;
            echo "*** applied $class (time: ".sprintf('%.3f', $time)."s)\n\n";
        } else {
            $time = microtime(true) - $start;
            echo "*** failed to apply $class (time: ".sprintf('%.3f', $time)."s)\n\n";

            return false;
        }
    }

    protected function migrateDown($class)
    {
        if ($class === self::BASE_MIGRATION) {
            return;
        }

        echo "*** reverting $class\n";
        $start = microtime(true);
        $migration = $this->instantiateMigration($class);
        if ($migration->down() !== false) {
            $db = $this->getDbConnection();
            $db->createCommand()->delete(self::migrationTable, $db->quoteColumnName('version').'=:version', array(':version' => $class));
            $time = microtime(true) - $start;
            echo "*** reverted $class (time: ".sprintf('%.3f', $time)."s)\n\n";
        } else {
            $time = microtime(true) - $start;
            echo "*** failed to revert $class (time: ".sprintf('%.3f', $time)."s)\n\n";

            return false;
        }
    }

    protected function instantiateMigration($class)
    {
        $file = $this->dataPatchPath.DIRECTORY_SEPARATOR.$class.'.php';
        require_once $file;
        $migration = new $class();
        $migration->setDbConnection($this->getDbConnection());

        return $migration;
    }

    /**
     * @var CDbConnection
     */
    private $_db;
    protected function getDbConnection()
    {
        if ($this->_db !== null) {
            return $this->_db;
        } elseif (($this->_db = Yii::app()->getComponent($this->connectionID)) instanceof CDbConnection) {
            return $this->_db;
        }

        echo "Error: CMigrationCommand.connectionID '{$this->connectionID}' is invalid. Please make sure it refers to the ID of a CDbConnection application component.\n";
        exit(1);
    }

    protected function getMigrationHistory($limit)
    {
        $db = $this->getDbConnection();
        if ($db->schema->getTable(self::migrationTable, true) === null) {
            $this->createMigrationHistoryTable();
        }

        return CHtml::listData($db->createCommand()
                ->select('version, apply_time')
                ->from(self::migrationTable)
                ->order('version DESC')
                ->limit($limit)
                ->queryAll(), 'version', 'apply_time');
    }

    protected function createMigrationHistoryTable()
    {
        $db = $this->getDbConnection();
        echo 'Creating migration history table "'.self::migrationTable.'"...';
        $db->createCommand()->createTable(self::migrationTable, array(
                'version' => 'string NOT NULL PRIMARY KEY',
                'apply_time' => 'integer',
        ));
        $db->createCommand()->insert(self::migrationTable, array(
                'version' => self::BASE_MIGRATION,
                'apply_time' => time(),
        ));
        echo "done.\n";
    }

    protected function getNewMigrations()
    {
        $applied = array();
        foreach ($this->getMigrationHistory(-1) as $version => $time) {
            $applied[substr($version, 1, 13)] = true;
        }

        $migrations = array();
        $handle = opendir($this->dataPatchPath);
        while (($file = readdir($handle)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $path = $this->dataPatchPath.DIRECTORY_SEPARATOR.$file;
            if (preg_match('/^(m(\d{6}_\d{6})_.*?)\.php$/', $file, $matches) && is_file($path) && !isset($applied[$matches[2]])) {
                $migrations[] = $matches[1];
            }
        }
        closedir($handle);
        sort($migrations);

        return $migrations;
    }

    public function getHelp()
    {
        return <<<EOD
USAGE
  yiic migrate [action] [parameter]

DESCRIPTION
  This command provides support for Data Patch database migrations. The optional
  'action' parameter specifies which specific migration task to perform.
  It can take these values: up, down, to, create, history, new, mark.
  If the 'action' parameter is not given, it defaults to 'up'.
  Each action takes different parameters. Their usage can be found in
  the following examples.
            
  NOTE: always have to provide the dataPatchPath parameter like
        --dataPatchPath=/opt/openeyes/Openeyes-Institution/env/50_prod/patch

EXAMPLES
 * yiic DataPatch --dataPatchPath=/opt/openeyes/OpenEyes-Institution/env/50_prod/patch
   Applies ALL new migrations. This is equivalent to 'yiic DataPatch up'.

 * yiic DataPatch create create_user_table --dataPatchPath=/opt/openeyes/OpenEyes-Institution/env/50_prod/patch
   Creates a new migration named 'create_user_table'.

 * yiic DataPatch up 3 --dataPatchPath=/opt/openeyes/OpenEyes-Institution/env/50_prod/patch
   Applies the next 3 new migrations.

 * yiic DataPatch down --dataPatchPath=/opt/openeyes/OpenEyes-Institution/env/50_prod/patch
   Reverts the last applied migration.

 * yiic DataPatch down 3 --dataPatchPath=/opt/openeyes/OpenEyes-Institution/env/50_prod/patch
   Reverts the last 3 applied migrations.

 * yiic DataPatch to 101129_185401 --dataPatchPath=/opt/openeyes/OpenEyes-Institution/env/50_prod/patch
   Migrates up or down to version 101129_185401.

 * yiic migrate mark 101129_185401 --dataPatchPath=/opt/openeyes/OpenEyes-Institution/env/50_prod/patch
   Modifies the migration history up or down to version 101129_185401.
   No actual migration will be performed.

 * yiic migrate history --dataPatchPath=/opt/openeyes/OpenEyes-Institution/env/50_prod/patch
   Shows all previously applied migration information.

 * yiic migrate history 10 --dataPatchPath=/opt/openeyes/OpenEyes-Institution/env/50_prod/patch
   Shows the last 10 applied migrations.

 * yiic migrate new --dataPatchPath=/opt/openeyes/OpenEyes-Institution/env/50_prod/patch
   Shows all new migrations.

 * yiic migrate new 10 --dataPatchPath=/opt/openeyes/OpenEyes-Institution/env/50_prod/patch
   Shows the next 10 migrations that have not been applied.

EOD;
    }

    protected function getTemplate()
    {
        if ($this->templateFile !== null) {
            return file_get_contents($this->templateFile.'.php');
        } else {
            return <<<EOD
<?php

class {ClassName} extends CDbMigration
{	
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
		//Yii::app()->db->createCommand('alter table example add column email varchar(255);')->execute();
	}

	public function safeDown()
	{
	}
}
EOD;
        }
    }
}
