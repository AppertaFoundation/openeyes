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
class MigrateProtectedFilesToDBCommand extends CConsoleCommand
{
    /**
     * Query protected_file table and only the files under protected/files/
     * will be migrated into database
     */
    public function run($args)
    {
        // check if file_content column exists. This may be redundant for OE 4.0+
        echo "\t* Checking if the file_content column exists in the table protected_file or not...";
        $result = Yii::app()->db->createCommand()
            ->select('*')
            ->from('information_schema.COLUMNS')
            ->where('table_name = :table_name AND column_name = :column_name', array(':table_name' => 'protected_file', ':column_name' => 'file_content'))
            ->queryRow();
        if (!$result) {
            echo "\r\n";
            echo "\t* The file_content column does not exist in the table protected_file, nothing will be changed...";
            echo "\r\n";
            return;
        }
        echo "Done";
        echo "\r\n";
        echo "\t* Querying the protected_file table...";
        $all_files = Yii::app()->db->createCommand()
            ->select('id, uid, file_content')
            ->from('protected_file')
            ->queryAll();
        echo "Done";
        echo "\r\n";
        
        echo "\t* Migrating protected file contents into database...";
        echo "\r\n";

        // loop through all the files records and only matched files will be migrated into database
        foreach ($all_files as $file) {
            // Save the contents of the files on the application server to the database.
            $path = Yii::app()->basePath.'/files/'. $file['uid'][0] .'/'. $file['uid'][1] .'/'. $file['uid'][2] . '/' . $file['uid'];
            
            if (file_exists($path)) {
                echo "\t\t+ Migrating $path...";
                $file_contents = file_get_contents($path);
                Yii::app()->db->createCommand()
                ->update(
                    'protected_file',
                    array(
                        'file_content' => $file_contents,
                    ),
                    'id = :id',
                    array(':id' => $file['id'])
                );
                echo "Done";
                echo "\r\n";
            }
        }
        echo "\t* Migration Finished";
        echo "\r\n";
    }
}
