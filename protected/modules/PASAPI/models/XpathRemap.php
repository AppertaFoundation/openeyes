<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\PASAPI\models;

class XpathRemap extends \BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return PasApiAssignment the static model class
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
        return 'pasapi_xpath_remap';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('id, xpath, name, institution_id', 'safe'),
            array('id, xpath, name, institution_id, created_date, last_modified_date, created_user_id, last_modified_user_id',
                'safe', 'on' => 'search', ),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'values' => array(self::HAS_MANY, '\OEModule\PASAPI\models\RemapValue', 'xpath_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'institution' => array(self::BELONGS_TO, 'Institution', 'institution_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'xpath' => 'XPath',
            'name' => 'Name',
            'institution_id' => 'Institution',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new \CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('xpath', $this->xpath, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('institution_id', $this->institution_id, true);

        return new \CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Simple wrapper function for getting the remaps for a specific Xpath.
     *
     * @param string $xpath
     *
     * @return \CActiveRecord[]
     */
    public function findAllByXpath($xpath = '/')
    {
        $condition = 'xpath like :xpath AND institution_id = :institution_id';
        $params = array(':xpath' => "{$xpath}%", ':institution_id' => \Yii::app()->session['selected_institution_id']);

        return $this->findAll($condition, $params);
    }
}
