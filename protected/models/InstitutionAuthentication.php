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
 * This is the model class for table "institution_authentication".
 *
 * The followings are the available columns in table 'institution_authentication':
 * @property integer $id
 * @property integer $site_id
 * @property integer $institution_id
 * @property string $login_method_code
 * @property string $description
 * @property integer $order
 * @property integer $ldap_config_id
 * @property integer $active
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:

 * @property Site $site
 * @property Institution $institution
 * @property UserAuthenticationMethod $userAuthenticationMethod
 * @property LDAPConfig $LDAPConfig
 */
class InstitutionAuthentication extends BaseActiveRecordVersioned
{
    const NO_MATCH = 0;
    const EXACT_MATCH = 1;
    const PERMISSIVE_MATCH = 2;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'institution_authentication';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        $common_rules = [
            ['institution_id, user_authentication_method, description, active', 'required'],
            ['last_modified_user_id, created_user_id', 'length', 'max'=>10],
            ['last_modified_date, created_date, ldap_config_id, site_id', 'safe'],
            // The following rule is used by search().
            ['id, site_id, institution_id, ldap_config_id, user_authentication_method, description, active, last_modified_user_id, last_modified_date, created_user_id, created_date, active', 'safe', 'on'=>'search'],
        ];

        return $common_rules;
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return [
            'site' => [self::BELONGS_TO, 'Site', 'site_id'],
            'institution' => [self::BELONGS_TO, 'Institution', 'institution_id'],
            'LDAPConfig' => [self::BELONGS_TO, 'LDAPConfig', 'ldap_config_id'],
            'userAuthenticationMethod' => [self::BELONGS_TO, 'UserAuthenticationMethod', 'user_authentication_method'],
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
            'site_id' => 'Site',
            'institution_id' => 'Institution',
            'user_authentication_method' => 'User Authentication Method',
            'description' => 'Description',
            'ldap_config_id' => 'LDAP Config',
            'active' => 'Active',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
        ];
    }

    public static function newFromInstitution($institution_id)
    {
        $institution_authentication = new self();
        $institution_authentication->institution_id = $institution_id;
        return $institution_authentication->institution ? $institution_authentication : null;
    }

    public function match($institution_id, $site_id)
    {
        $match = self::NO_MATCH;
        if ($this->active && $this->institution_id == $institution_id) {
            if ($this->site_id) {
                if ($this->site_id == $site_id) {
                    $match = self::EXACT_MATCH;
                }
            } else {
                $match = self::PERMISSIVE_MATCH;
            }
        }
        return $match;
    }

    public function beforeValidate()
    {
        if ($this->user_authentication_method == 'LDAP') {
            if (!$this->ldap_config_id) {
                $this->addError('ldap_config_id', 'LDAP Configuration must be specified.');
            }
        }
        return parent::beforeValidate();
    }

    public function getFullyQualifiedDescription()
    {
        return $this->description . " [Institution: {$this->institution->short_name}]" .
            ($this->site ? "[Site: {$this->site->short_name}]" : "");
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
        $criteria->compare('site_id', $this->site_id);
        $criteria->compare('institution_id', $this->institution_id);
        $criteria->compare('user_authentication_method', $this->user_authentication_method, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('active', $this->active);
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
