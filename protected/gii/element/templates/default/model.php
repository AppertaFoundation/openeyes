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
 * This is the template for generating the model class of a specified element.
 * - $this: the ModelCode object
 * - $tableName: the table name for this class (prefix is already removed if necessary)
 * - $modelClass: the model class name
 * - $baseClass: The immediate superclass of the model
 * - $columns: list of table columns (name=>CDbColumnSchema)
 * - $labels: list of attribute labels (name=>label)
 * - $rules: list of validation rules
 * - $ignore: Array of fields to ignore when generating code
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
 * This is the model class for table "<?php echo $tableName; ?>".
 *
 * The followings are the available columns in table '<?php echo $tableName; ?>':
<?php foreach($columns as $column): ?>
 * @property <?php echo $column->type.' $'.$column->name."\n"; ?>
<?php endforeach; ?>
 *
 */
class <?php echo $modelClass; ?> extends <?php echo $this->baseClass."\n"; ?>
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return <?php echo $modelClass; ?> the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '<?php echo $tableName; ?>';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// Only define rules for those attributes with user inputs.
		return array(
<?php foreach($rules as $rule): ?>
			<?php echo $rule.",\n"; ?>
<?php endforeach; ?>

			// Remove attributes that should not be searched.
			array('<?php
				$oe_array_keys = array();
				foreach (array_keys($columns) as $column) {
					if(in_array($column, $ignore))
						continue;
					$oe_array_keys[] = $column;
				}
				echo implode(', ', $oe_array_keys); ?>', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
<?php foreach($labels as $name=>$label): ?>
			<?php echo "'$name' => '$label',\n"; ?>
<?php endforeach; ?>
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;

<?php
foreach ($columns as $name=>$column) {
	if(in_array($name, $ignore))
		continue;
	if ($column->type==='string') {
		echo "\t\t\$criteria->compare('$name',\$this->$name,true);\n";
	} else {
		echo "\t\t\$criteria->compare('$name',\$this->$name);\n";
	}
}
?>

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}
