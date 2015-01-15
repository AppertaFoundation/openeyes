<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * This is the model class for table "setting_metadata".
 *
 * The followings are the available columns in table 'setting_metadata':
 * @property string $id
 * @property string $element_type_id
 * @property string $display_order
 * @property string $field_type_id
 * @property string $key
 * @property string $name
 * @property string $data
 * @property string $default_value
 */
class SettingMetadata extends BaseActiveRecordVersioned
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return SettingMetadata the static model class
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
		return 'setting_metadata';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('element_type_id, display_order, field_type_id, key, name', 'required'),
			array('element_type_id, display_order, field_type_id, key, name, data, default_value', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, element_type_id, display_order, field_type_id, key, name, data, default_value', 'safe', 'on'=>'search'),
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
			'element_type' => array(self::BELONGS_TO, 'ElementType', 'element_type_id'),
			'field_type' => array(self::BELONGS_TO, 'SettingFieldType', 'field_type_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
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

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('name',$this->name,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	protected function getSettingValue($model, $key, $condition_field, $condition_value, $element_type)
	{
		$criteria = new CDbcriteria;

		if ($condition_field && $condition_value) {
			$criteria->addCondition($condition_field.' = :'.$condition_field);
			$criteria->params[':'.$condition_field] = $condition_value;
		}

		$criteria->addCondition('`key`=:key');
		$criteria->params[':key'] = $key;

		if ($element_type) {
			$criteria->addCondition('element_type_id=:eti');
			$criteria->params[':eti'] = $element_type->id;
		} else {
			$criteria->addCondition('element_type_id is null');
		}

		return $model::model()->find($criteria);
	}

	public function getSetting($key=null, $element_type=null, $return_object=false)
	{
		if (!$key) {
			$key = $this->key;
		}

		if ($element_type) {
			$metadata = SettingMetadata::model()->find('element_type_id=? and `key`=?',array($element_type->id,$key));
		} else {
			$metadata = SettingMetadata::model()->find('element_type_id is null and `key`=?',array($key));
		}

		if (!$metadata) {
			return false;
		}

		$user_id = Yii::app()->session['user'] ? Yii::app()->session['user']->id : null;
		$firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
		$firm_id = $firm ? $firm->id : null;
		$subspecialty_id = $firm ? $firm->subspecialtyID : null;
		$specialty_id = $firm && $firm->specialty ? $firm->specialty->id : null;
		$site = Site::model()->findByPk(Yii::app()->session['selected_site_id']);
		$site_id = $site ? $site->id : null;
		$institution_id = $site ? $site->institution_id : null;

		foreach (array(
			'SettingUser' => 'user_id',
			'SettingFirm' => 'firm_id',
			'SettingSubspecialty' => 'subspecialty_id',
			'SettingSpecialty' => 'specialty_id',
			'SettingSite' => 'site_id',
			'SettingInstitution' => 'institution_id',
			'SettingInstallation' => null,
			) as $class => $field) {

			if ($field) {
				if (${$field}) {
					if ($setting = $this->getSettingValue($class, $key, $field, ${$field}, $element_type)) {
						if ($return_object) {
							return $setting;
						}
						return $this->parseSetting($setting, $metadata);
					}
				}
			} else {
				if ($setting = $this->getSettingValue($class, $key, null, null, $element_type)) {
					if ($return_object) {
						return $setting;
					}
					return $this->parseSetting($setting, $metadata);
				}
			}
		}

		if ($return_object) {
			return false;
		}

		return $metadata->default_value;
	}

	public function getSettingName($key=null)
	{
		if (!$key) {
			$key = $this->key;
		}

		$value = $this->getSetting($key);

		if ($data = @unserialize($this->data)) {
			return $data[$value];
		}

		return $value;
	}

	public function parseSetting($setting, $metadata)
	{
		if (@$data = unserialize($metadata->data)) {
			if (isset($data['model'])) {
				$model = $data['model'];
				return $model::model()->findByPk($setting->value);
			}
		}

		return $setting->value;
	}
}
