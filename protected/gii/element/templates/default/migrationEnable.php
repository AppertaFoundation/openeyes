<?php
/**
 * ____________________________________________________________________________
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
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file
 * titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * _____________________________________________________________________________
 * http://www.openeyes.org.uk   info@openeyes.org.uk
 *
 * @author Bill Aylward <bill.aylward@openeyes.org.uk>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3.0
 * @license http://www.openeyes.org.uk/licenses/oepl-1.0.html OEPLv1.0
 * @version 0.9
 * Creation date: 27 December 2011
 * @copyright Copyright (c) 2012 OpenEyes Foundation, Moorfields Eye hospital
 * @package Clinical
 */

/**
 * This is the template for generating migrations scripts for enabling a new element.
 * - $this: the ModelCode object
 * - $elementName: the name of the element
 * - $tableName: the table name for the element
 * - $className: the class name for the model
 * - $migrationName: the migration name
 * - $eventName: name of the enclosing event
 * - $subSubspecialtyName: name of the subsubspecialty for testing
 * - $authorName: Name of the file's author
 * - $authorEmail: Email address of the file's author
 */
?>
<?php echo "<?php\n"; ?>
/**
 * ____________________________________________________________________________
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
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file
 * titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * _____________________________________________________________________________
 * http://www.openeyes.org.uk   info@openeyes.org.uk
 *
 * @author <?php echo $authorName; ?> <<?php echo $authorEmail; ?>>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3.0
 * @license http://www.openeyes.org.uk/licenses/oepl-1.0.html OEPLv1.0
 * @version 0.9
 * Creation date: <?php echo date("j F Y")."\n";?>
 * @copyright Copyright (c) 2012 OpenEyes Foundation, Moorfields Eye hospital
 * @package Clinical
 *
 * This is a migration script to enable the table table "<?php echo $tableName; ?>".
 *
 * Run using the command './yiic migrate' from the protected directory
 *
 */
class <?php echo "$migrationName";?> extends CDbMigration
{
	public function up()
	{
		// Put new element into element_type table
		$this->insert('element_type', array(
				'name' => '<?php echo "$elementName";?>',
				'class_name' => '<?php echo "$className";?>'
		));

		// Get relevant event and element type for ids
		$eventType = EventType::model()->find('name=:name',array(':name'=>'<?php echo "$eventName";?>'));
		$elementType = ElementType::model()->find('name=:name',array(':name'=>'<?php echo "$elementName";?>'));

		// Insert new element into possible_element_type table
		$this->insert('possible_element_type', array(
			'event_type_id' => $eventType->id,
			'element_type_id' => $elementType->id,
			'num_views' => 1,
			'display_order' => 1
			));

		// Get id of last entry into possible_element_type
		$possibleElementType = PossibleElementType::model()->find(
        	'event_type_id=:event_type_id and element_type_id=:element_type_id',
            array(':event_type_id'=>$eventType->id,':element_type_id'=>$elementType->id
            ));

		// Get subspecialty ***TODO*** build selection of subspecialty into Gii
		$subspecialty = Subspecialty::model()->find('name=:name',array(':name'=>'<?php echo "$subSubspecialtyName";?>'));

		// Insert entry into site_element
		$this->insert('site_element_type', array(
			'possible_element_type_id' => $possibleElementType->id,
			'subspecialty_id' => $subspecialty->id,
			'view_number' => 1,
			'required' => 1
			));
	}

	public function down()
	{
		// Get relevant ids
		$eventType = EventType::model()->find('name=:name',array(':name'=>'<?php echo "$eventName";?>'));
		$elementType = ElementType::model()->find('name=:name',array(':name'=>'<?php echo "$elementName";?>'));
		$subspecialty = Subspecialty::model()->find('name=:name',array(':name'=>'<?php echo "$subSubspecialtyName";?>'));
		$possibleElementType = PossibleElementType::model()->find(
        	'event_type_id=:event_type_id and element_type_id=:element_type_id',
            array(':event_type_id'=>$eventType->id,':element_type_id'=>$elementType->id
            ));

		// Remove entries in site_element_type table (for all specialties)
		$this->delete('site_element_type', 'possible_element_type_id = :possible_element_type_id',
			array(':possible_element_type_id' => $possibleElementType->id)
			);

		// Remove possible_element_type entry
        $this->delete('possible_element_type', 'id = :id',
        	array(':id' => $possibleElementType->id)
            );

        // Remove element_type entry
		$this->delete('element_type', 'name = :name', array(':name' => '<?php echo "$elementName";?>'));

		// Reset autoincrement
		$this->execute('ALTER TABLE `site_element_type` AUTO_INCREMENT = 1;');
		$this->execute('ALTER TABLE `possible_element_type` AUTO_INCREMENT = 1;');
		$this->execute('ALTER TABLE `element_type` AUTO_INCREMENT = 1;');
	}
}
