<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */


namespace OEModule\OphCoCvi\models;

/**
 * This is the model class for table "ophcocvi_clericinfo_patient_factor".
 *
 * The followings are the available columns in table:
 * @property string $id
 * @property string $name
 * @property string $code
 * @property boolean $require_comments
 * @property string $comments_label
 * @property integer $display_order
 * @property boolean $active
 *
 * The followings are the available model relations:
 *
 * @property User $user
 * @property User $usermodified
 */

class OphCoCvi_ClericalInfo_PatientFactor extends \BaseActiveRecordVersioned
{
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
        return 'ophcocvi_clericinfo_patient_factor';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('name, comments_only, yes_no_only', 'safe'),
            array('name', 'required'),
            array('name, comments_label', 'length', 'max' => 255),
            array('code', 'length', 'max' => 20),
            array('comments_label', 'RequiredIfFieldValidator', 'field' => 'require_comments', 'value' => 1, 'message' => 'A comments label is required if comments are allowed for a factor.'),
            array('id, name', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
        );
    }

    /**
     * Add Lookup behaviour
     *
     * @return array
     */
    public function behaviors()
    {
        return array(
            'LookupTable' => 'LookupTable',
        );
    }

    /**
     * always order by display_order
     *
     * @return array
     */
    public function defaultScope()
    {
        return array('order' => $this->getTableAlias(true, false).'.display_order asc');
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'code' => 'Code',
            'active' => 'Active',
            'require_comments' => 'Comments allowed',
            'comments_only' => 'Show textarea only ',
            'yes_no_only' => 'Show only Yes/No options'

        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new CDbCriteria;
        $criteria->compare('id', $this->id, true);
        $criteria->compare('name', $this->name, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }
}
