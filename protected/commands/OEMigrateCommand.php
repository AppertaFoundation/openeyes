<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class OEMigrateCommand extends MigrateCommand
{
    public $testdata = false;

    public $all = false;

    protected ?string $initialMigrationPath = null;

    protected function instantiateMigration($class)
    {
        $migration = parent::instantiateMigration($class);
        if ($this->testdata && $migration instanceof OEMigration) {
            $migration->setTestData(true);
            echo "\nRunning in testdata mode";
        }

        return $migration;
    }

    protected function getNewMigrations()
	{
        if (!$this->all) {
            return parent::getNewMigrations();
        }

        $new_migrations = [];
        foreach ($this->getMigrationPaths() as $path) {
            $new_migrations = array_merge($new_migrations, $this->getNewMigrationsForPath($path));
        }

        usort($new_migrations, function ($a, $b) {
            return ($a->migration < $b->migration) ? -1 : 1;
        });

		return $new_migrations;
	}

    protected function migrateUp($migration)
    {
        // if it's not an object, then the default behaviour of migration should continue
        // where the $migration value is a string that will be resolved through the original
        // migration path
        if (is_object($migration) && property_exists($migration, 'path')) {
            $this->swapMigrationPath($migration->path);
            return parent::migrateUp($migration->migration);
        } else {
            $this->restoreInitialMigrationPath();
        }

        return parent::migrateUp($migration);
    }

    /**
     * Uses the application module configuration to provide a list of all migration paths
     * that are relevant for searching through for new migrations.
     *
     * @return array
     */
    protected function getMigrationPaths(): array
    {
        $modules = array_keys(\Yii::app()->modules);
        return array_merge(
            [\Yii::getPathOfAlias('application.migrations')],
            array_filter(
                array_map(
                    function ($module) {
                        return \Yii::getPathOfAlias('application.modules.' . $module . '.migrations');
                    },
                    $modules
                ),
                function ($path) {
                    return (is_dir($path));
                }
            )
        );
    }

    /**
     * Migrations are wrapped in an anonymous class to allow us to switch the migration path for each
     * migration that we discover and run.
     *
     * @param string $path
     * @return array
     */
    protected function getNewMigrationsForPath(string $path): array
    {
        // we leverage parent behaviour which relies on the migrationPath property
        $this->migrationPath = $path;

        return array_map(
            function ($migration) use ($path) {
                return new class ($path, $migration) {
                    public string $path;
                    public string $migration;

                    public function __construct($path, $migration)
                    {
                        $this->path = $path;
                        $this->migration = $migration;
                    }

                    public function __toString()
                    {
                        return $this->path . "/" . $this->migration;
                    }
                };
            },
            parent::getNewMigrations()
        );
    }

    protected function swapMigrationPath(string $new_migration_path): void
    {
        if ($this->initialMigrationPath === null) {
            $this->initialMigrationPath = $this->migrationPath;
        }
        $this->migrationPath = $new_migration_path;
    }

    protected function restoreInitialMigrationPath(): void
    {
        if ($this->initialMigrationPath === null) {
            $this->initialMigrationPath = $this->migrationPath;
        }
        $this->migrationPath = $this->initialMigrationPath;
    }

    public function getHelp()
    {
        return parent::getHelp() . <<<EOD

 * yiic migrate --all
   The all option will override the migrationPath option and search for migrations from core and across all modules
   These will be sorted to run in date order (as defined by the timestamp prefix of the migration filenames)
EOD;
    }
}
