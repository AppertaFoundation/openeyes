<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class DocumentTarget extends BaseActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return Site the static model class
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
        return 'document_target';
    }

    public function behaviors()
    {
        return array();
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('document_instance_id, contact_type, contact_id, contact_name, contact_modified, address, emailemail, ToCc', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('document_instance_id, contact_type, contact_id, contact_name, contact_modified, address, emailemail', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'created_user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'document_instance' => array(self::BELONGS_TO, 'DocumentInstance', 'document_instance_id'),
            'contact' => array(self::BELONGS_TO, 'Contact', 'contact_id'),
            'document_output' => array(self::HAS_MANY, 'DocumentOutput', 'document_target_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array();
    }

    public function beforeSave()
    {
        // This check is only for the other and internal referral letter types
        if ($this->contact_id === '' && ($this->contact_type === 'INTERNALREFERRAL' || $this->contact_type === 'OTHER')) {
            $this->contact_id = null;
        }

        return parent::beforeSave();
    }

    public function getContactTypes()
    {
        $option_array = [];

        $result = Yii::app()->db->createCommand('SHOW COLUMNS FROM document_target LIKE "contact_type"')->queryRow();
        if ($result['Type']) {
            $option_array = explode("','", preg_replace("/(enum)\('(.+?)'\)/", "$2", $result['Type']));
        }

        return array_combine($option_array, $option_array);
    }

    /**
     * Gets the Commissioning Body Service with the same contact id of target
     *
     * @return array|null
     */
    public function getCommissioningBodyService() {
        return CommissioningBodyService::model()->find('contact_id=:contact_id', [':contact_id' => $this->contact_id]);
    }
}
