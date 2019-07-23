<?php
/**
 * ____________________________________________________________________________.
 *
 * This file is part of OpenEyes.
 *
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU
 * General Public License as published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file
 * titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * _____________________________________________________________________________
 * http://www.openeyes.org.uk   info@openeyes.org.uk
 *
 * @author Bill Aylward <bill.aylward@openeyes.org.uk>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3.0
 * @license http://www.openeyes.org.uk/licenses/oepl-1.0.html OEPLv1.0
 *
 * @version 0.9
 * Creation date: 27 December 2011
 *
 * @copyright Copyright (c) 2012 OpenEyes Foundation, Moorfields Eye hospital
 */

/**
 * This is the template for generating migrations scripts for creating a new element's table.
 * - $this: the ModelCode object
 * - $tableName: the table name for this class (prefix is already removed if necessary)
 * - $elementFields: The text block containing element fields
 * - $migrationName: the migration name
 * - $authorName: Name of the file's author
 * - $authorEmail: Email address of the file's author.
 */
?>
<?php echo "<?php\n"; ?>
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
class <?php echo "$migrationName";?> extends CDbMigration
{
    public function up()
    {
        $this->createTable(
            '<?php echo "$tableName";?>',
            array(
                'id' => 'integer UNSIGNED NOT NULL AUTO_INCREMENT',
                'event_id' => 'integer UNSIGNED',
                'created_user_id' => 'integer UNSIGNED NOT NULL DEFAULT "1"',
                'created_date' => 'datetime NOT NULL',
                'last_modified_user_id' => 'integer UNSIGNED NOT NULL DEFAULT "1"',
                'last_modified_date' => 'datetime NOT NULL',

                /*
                 * Enter your element table's fields here
                 * In order to ensure RDMS independence, use the following datatypes
                 *
                 * pk: a generic primary key type
                 * string: string type
                 * text: text type (long string)
                 * integer: integer type
                 * float: floating number type
                 * decimal: decimal number type
                 * datetime: datetime type
                 * timestamp: timestamp type
                 * time: time type
                 * date: date type
                 * binary: binary data type
                 * boolean: boolean type
                 * money: money/currency type
                 */
<?php
    $lines = preg_split('/\r\n|\r|\n/', "$elementFields");
foreach ($lines as $line) {
    echo "
                $line";
}
?>


                'PRIMARY KEY (`id`)',
                'UNIQUE KEY `event_id` (`event_id`)'),
                'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
            );
    }

    public function down()
    {
        $this->dropTable('<?php echo "$tableName";?>');
    }
}
