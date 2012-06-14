<?php echo '<?php '?>
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * This is the model class for table "<?php if (isset($element)) echo $element['table_name']; ?>".
 *
 * The followings are the available columns in table:
 * @property string $id
 * @property integer $event_id
<?php
if (isset($element)) {
	foreach ($element['fields'] as $field) {
		if ($field['type'] == 'Textbox') {
			echo ' * @property string $' . $field['name'] . "\n";
		} elseif ($field['type'] == 'Textarea') {
			echo ' * @property string $' . $field['name'] . "\n";
		} elseif ($field['type'] == 'Date picker') {
			echo ' * @property string $' . $field['name'] . "\n";
		} elseif ($field['type'] == 'Dropdown list') {
			echo ' * @property integer $' . $field['name'] . "\n";
		} elseif ($field['type'] == 'Checkboxes') {
			echo ' * @property string $' . $field['name'] . "\n";
		} elseif ($field['type'] == 'Radio buttons') {
			echo ' * @property string $' . $field['name'] . "\n";
		} elseif ($field['type'] == 'Boolean') {
			echo ' * @property string $' . $field['name'] . "\n";
		} elseif ($field['type'] == 'EyeDraw') {
			echo ' * @property string $' . $field['name'] . "\n";
		}
	}
}
?>
 *
 * The followings are the available model relations:
 */

class <?php if (isset($element)) echo $element['class_name']; ?> extends BaseEventTypeElement
{
	public $service;

	/**
	 * Returns the static model of the specified AR class.
	 * @return the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '<?php if (isset($element)) echo $element['table_name']; ?>';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('event_id, <?php if (isset($element)) { foreach ($element['fields'] as $field) { echo $field['name'] . ", "; if ($field['type'] == 'EyeDraw' && @$field['extra_report']) { echo $field['name'].'2, '; } } } ?>', 'safe'),
			array('<?php if (isset($element)) { foreach ($element['fields'] as $field) { if ($field['required']) { echo $field['name'] . ", "; } } } ?>', 'required'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, event_id, <?php if (isset($element)) { foreach ($element['fields'] as $field) { echo $field['name'] . ", "; } } ?>', 'safe', 'on' => 'search'),
		);
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'element_type' => array(self::HAS_ONE, 'ElementType', 'id','on' => "element_type.class_name='".get_class($this)."'"),
			'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
			'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
			'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
			'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
			<?php if (isset($element)) foreach ($element['relations'] as $relation) {?>
			'<?php echo $relation['name']?>' => array(self::<?php echo $relation['type']?>, '<?php echo $relation['class']?>', '<?php echo $relation['field']?>'),
			<?php }?>
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'event_id' => 'Event',
<?php
if (isset($element)) {
	foreach ($element['fields'] as $field) {
		echo "'" . $field['name'] . '\' => \'' . $field['label'] . "',\n";
	}
}
?>
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('event_id', $this->event_id, true);

<?php
if (isset($element)) {
	foreach ($element['fields'] as $field) {
		echo '$criteria->compare(\'' . $field['name'] . '\', $this->' . $field['name'] . ');' . "\n";
	}
}
?>
		
		return new CActiveDataProvider(get_class($this), array(
				'criteria' => $criteria,
			));
	}

	/**
	 * Set default values for forms on create
	 */
	public function setDefaultOptions()
	{
		<?php if (isset($element)) {
			foreach ($element['defaults'] as $property => $value) {
				echo '$this->'.$property.' = '.$value.';';
			}
		}?>
	}

	<?php if (@$element['add_selected_eye']) {?>
	public function getSelectedEye() {
		if (Yii::app()->getController()->getAction()->id == 'create') {
			// Get the procedure list and eye from the most recent booking for the episode of the current user's subspecialty
			if (!$patient = Patient::model()->findByPk(@$_GET['patient_id'])) {
				throw new SystemException('Patient not found: '.@$_GET['patient_id']);
			}

			if ($episode = $patient->getEpisodeForCurrentSubspecialty()) {
				if ($booking = $episode->getMostRecentBooking()) {
					return $booking->elementOperation->eye;
				}
			}
		}

		if (isset($_GET['eye'])) {
			return Eye::model()->findByPk($_GET['eye']);
		}

		return new Eye;
	}

	public function getEye() {
		// Insert your code to retrieve the current eye here
		return new Eye;
	}
	<?php }?>

	<?php if (isset($element) && !empty($element['defaults_methods'])) {
		foreach ($element['defaults_methods'] as $default_method) {?>
	public function get<?php echo $default_method['method']?>() {
		$ids = array();
		foreach (<?php echo $default_method['class']?>::model()->findAll('`default` = ?',array(1)) as $item) {
			$ids[] = $item->id;
		}
		return $ids;
	}
		<?}?>
	<?php }?>

	protected function beforeSave()
	{
		return parent::beforeSave();
	}

	protected function afterSave()
	{
		<?php if (isset($element) && !empty($element['after_save'])) {
			foreach ($element['after_save'] as $after_save) {
				if ($after_save['type'] == 'MultiSelect') {?>
		if (!empty($_POST['<?php echo $after_save['post_var']?>'])) {

			$existing_ids = array();

			foreach (<?php echo $after_save['mapping_table_class']?>::model()->findAll('element_id = :elementId', array(':elementId' => $this->id)) as $item) {
				$existing_ids[] = $item-><?php echo $after_save['lookup_table_field_id']?>;
			}

			foreach ($_POST['<?php echo $after_save['post_var']?>'] as $id) {
				if (!in_array($id,$existing_ids)) {
					$item = new <?php echo $after_save['mapping_table_class']?>;
					$item->element_id = $this->id;
					$item-><?php echo $after_save['lookup_table_field_id']?> = $id;

					if (!$item->save()) {
						throw new Exception('Unable to save MultiSelect item: '.print_r($item->getErrors(),true));
					}
				}
			}

			foreach ($existing_ids as $id) {
				if (!in_array($id,$_POST['<?php echo $after_save['post_var']?>'])) {
					$item = <?php echo $after_save['mapping_table_class']?>::model()->find('element_id = :elementId and <?php echo $after_save['lookup_table_field_id']?> = :lookupfieldId',array(':elementId' => $this->id, ':lookupfieldId' => $id));
					if (!$item->delete()) {
						throw new Exception('Unable to delete MultiSelect item: '.print_r($item->getErrors(),true));
					}
				}
			}
		}
				<? }
			}
		}?>

		return parent::afterSave();
	}

	protected function beforeValidate()
	{
		return parent::beforeValidate();
	}
}
<?php echo '?>';?>
