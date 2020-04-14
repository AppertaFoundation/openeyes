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

class OphCoCorrespondenceLetterSettings extends BaseActiveRecordVersioned
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophcocorrespondence_letter_settings';
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
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('display_order', $this->display_order);
        $criteria->compare('field_type_id', $this->field_type_id, true);
        $criteria->compare('key', $this->key, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('data', $this->data, true);
        $criteria->compare('default_value', $this->default_value, true);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return OphcocorrespondenceInternalReferralSettings the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    protected function getSettingValue($model, $key, $condition_field, $condition_value, $element_type)
    {
        $criteria = new CDbcriteria();

        if ($condition_field && $condition_value) {
            $criteria->addCondition($condition_field.' = :'.$condition_field);
            $criteria->params[':'.$condition_field] = $condition_value;
        }

        $criteria->addCondition('`key`=:key');
        $criteria->params[':key'] = $key;

        return $model::model()->find($criteria);
    }

    public function getSetting($key, $element_type = null, $return_object = false)
    {
        $metadata = self::model()->find('`key`=?', array($key));

        if (!$metadata) {
            return false;
        }
        foreach (array('OphCoCorrespondenceLetterSettingValue' => null) as $class => $field) {
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

    public function getSettingName($key = null)
    {
        if (!$key) {
            $key = $this->key;
        }

        $value = $this->getSetting($key);

        if ($value == '') {
            $value = $this->default_value;
        }

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
