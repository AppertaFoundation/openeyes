<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class BaseSetting extends BaseActiveRecord
{
    /**
     * additional setting tables
     * $class => $field
     * @var array
     */
    protected $setting_tables = [];



    public function getSettingTables()
    {
        return $this->setting_tables;
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'display_order' => 'Display Order',
            'field_type_id' => 'Field Type',
            'key' => 'Key',
            'name' => 'Name',
            'data' => 'Data',
            'default_value' => 'Default Value',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
        );
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('field_type_id, key, name, data, default_value', 'required'),
            array('display_order', 'numerical', 'integerOnly'=>true),
            array('field_type_id, last_modified_user_id, created_user_id', 'length', 'max'=>10),
            array('key, name, default_value', 'length', 'max'=>64),
            array('data', 'length', 'max'=>4096),
            array('last_modified_date, created_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, display_order, field_type_id, key, name, data, default_value, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
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
            'field_type' => array(self::BELONGS_TO, 'SettingFieldType', 'field_type_id'),
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
        );
    }

    protected function getSettingValue($model, $key, $condition_field, $condition_value, $element_type)
    {
        $criteria = new CDbCriteria();

        if ($condition_field && $condition_value) {
            $criteria->addCondition($condition_field.' = :'.$condition_field);
            $criteria->params[':'.$condition_field] = $condition_value;
        }

        $criteria->addCondition('`key`=:key');
        $criteria->params[':key'] = $key;

        return $model::model()->find($criteria);
    }

    public function getSetting($key = null, $element_type = null, $return_object = false)
    {
        if (!$key) {
            $key = $this->key;
        }

        $metadata = self::model()->find('`key`=?', array($key));

        if (!$metadata) {
            return false;
        }

        $user_id = Yii::app()->session['user']->id ?? null;
        $firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
        $firm_id = $firm->id ?? null;
        $subspecialty_id = $firm->subspecialtyID ?? null;
        $specialty_id = $firm && $firm->specialty ? $firm->specialty->id : null;
        $site = Site::model()->findByPk(Yii::app()->session['selected_site_id']);
        $site_id = $site->id ?? null;
        $institution_id = $site->institution_id ?? null;

        foreach ($this->getSettingTables() as $class => $field) {
            if ($field) {
                if (${$field} && $setting = $this->getSettingValue($class, $key, $field, ${$field}, $element_type)) {
                    if ($return_object) {
                        return $setting;
                    }

                    return $this->parseSetting($setting, $metadata);
                }
            } elseif ($setting = $this->getSettingValue($class, $key, null, null, $element_type)) {
                if ($return_object) {
                    return $setting;
                }

                return $this->parseSetting($setting, $metadata);
            }
        }

        if ($return_object) {
            return false;
        }

        return $metadata->default_value;
    }



    public function getSettingName($key = null)
    {
        if (!$key) {
            $key = $this->key;
        }

        $value = $this->getSetting($key);

        if ($value === '') {
            $value = $this->default_value;
        }

        if ($data = @unserialize($this->data)) {
            return $data[$value];
        }

        return $value;
    }

    public function parseSetting($setting, $metadata)
    {
        $data = unserialize($metadata->data);
        if ($data && isset($data['model'])) {
            $model = $data['model'];

            return $model::model()->findByPk($setting->value);
        }

        return $setting->value;
    }
}
