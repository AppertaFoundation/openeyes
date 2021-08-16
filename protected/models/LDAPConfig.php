<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2020, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * This is the model class for table "ldap_config".
 *
 * The followings are the available columns in table 'ldap_config':
 * @property integer $id
 * @property string $ldap_json
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 */
class LDAPConfig extends BaseActiveRecordVersioned
{
    /**
     * Temporary variables used to set the ldap json value
     */
    public $ldap_method;
    public $ldap_server;
    public $ldap_port;
    public $ldap_admin_dn;
    public $ldap_admin_password;
    public $ldap_dn;
    public $ldap_additional_params = [];
    public $ldap_attributes = [
        'ldap_method', 'ldap_port',
        'ldap_server', 'ldap_admin_dn',
        'ldap_admin_password', 'ldap_dn',
        'ldap_additional_params'
    ];
    public $ldap_json_obscured;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ldap_config';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['last_modified_user_id, created_user_id', 'length', 'max'=>10],
            ['last_modified_date, created_date, description, ldap_json', 'safe'],
            ['ldap_method, ldap_server, ldap_port, ldap_admin_dn, ldap_admin_password, ldap_dn', 'required'],
            ['description', 'unique'],
            [implode(",", $this->ldap_attributes), 'jsonSafeValidator'],
            // The following rule is used by search().
            ['id, description, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'],
        ];
    }

    public function jsonSafeValidator($attribute, $params)
    {
        $illegal_characters = [ "\"", "\f", "\n", "\r", "\t", "\\" ];
        $contained = "";
        foreach ($illegal_characters as $illegal_char) {
            if ($attribute == 'ldap_additional_params') {
                foreach ($this->$attribute as $param) {
                    if (strpos($param['key'], $illegal_char) !== false) {
                        $contained .= " " . htmlspecialchars($illegal_char);
                    }
                    if (strpos($param['value'], $illegal_char) !== false) {
                        $contained .= " " . htmlspecialchars($illegal_char);
                    }
                }
            } else {
                if (strpos($this->$attribute, $illegal_char) !== false) {
                    $contained .= " " . htmlspecialchars($illegal_char);
                }
            }
        }
        if (!empty($contained)) {
            $this->addError($attribute, self::getAttributeLabel($attribute) . " contains the following special characters which must be removed or escaped:" . $contained);
        }
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return [
            'createdUser' => [self::BELONGS_TO, 'User', 'created_user_id'],
            'lastModifiedUser' => [self::BELONGS_TO, 'User', 'last_modified_user_id'],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'description' => 'Description',
            'ldap_json' => 'LDAP Configuration JSON',
            'active' => 'Active',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
        ];
    }

    public function getLDAPParam($param)
    {
        foreach ($this->ldap_additional_params as $available_param) {
            if ($available_param['key'] == $param) {
                return $available_param['value'];
            }
        }
        return null;
    }

    public function afterFind()
    {
        $ldap_attributes = json_decode($this->ldap_json, true);
        if (!empty($ldap_attributes)) {
            foreach ($this->ldap_attributes as $ldap_attribute) {
                $this->$ldap_attribute = $ldap_attributes[$ldap_attribute] ?? $this->$ldap_attribute;
            }
        }
        unset($ldap_attributes['ldap_admin_password']);
        $this->ldap_json_obscured = json_encode($ldap_attributes);
        return parent::afterFind();
    }

    public function beforeValidate()
    {
        $new_ldap_attributes = [];
        foreach ($this->ldap_attributes as $ldap_attribute) {
            $new_ldap_attributes[$ldap_attribute] = $this->$ldap_attribute;
        }
        if (!$this->ldap_additional_params) {
            $this->ldap_additional_params = [];
        }
        $this->ldap_json = json_encode($new_ldap_attributes);

        return parent::beforeValidate();
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
        $criteria=new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('last_modified_user_id', $this->last_modified_user_id, true);
        $criteria->compare('last_modified_date', $this->last_modified_date, true);
        $criteria->compare('created_user_id', $this->created_user_id, true);
        $criteria->compare('created_date', $this->created_date, true);

        return new CActiveDataProvider($this, [
            'criteria'=>$criteria,
        ]);
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Tag the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
