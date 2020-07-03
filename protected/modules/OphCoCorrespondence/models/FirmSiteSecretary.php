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

/**
 * The followings are the available columns in table 'ophcocorrespondence_firm_site_secretary':.
 *
 * @property string $id
 * @property int $event_id
 * @property int $firm_id
 * @property int $site_id
 * @property string direct_line
 *
 * The followings are the available model relations:
 * @property Event $event
 */
class FirmSiteSecretary extends BaseEventTypeElement
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @param string $className
     * @return FirmSiteSecretary the static model class
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
        return 'ophcocorrespondence_firm_site_secretary';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('firm_id, site_id, direct_line, fax', 'safe'),
            array('firm_id, site_id', 'required'),
            array('firm_id', 'ext.validators.UniqueSiteFirmValidator', 'message' => 'Only one contact can be added for each firm and site'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('firm_id, site_id, direct_line', 'safe', 'on' => 'search'),
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
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'firm' => array(self::BELONGS_TO, 'Firm', 'firm_id'),
            'site' => array(self::BELONGS_TO, 'Site', 'site_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('event_id', $this->event_id, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * @param $firmId
     *
     * @return FirmSiteSecretary[]
     */
    public function findSiteSecretaryForFirm($firmId)
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'firm_id = :firm_id';
        $criteria->params = array(':firm_id' => (int) $firmId);

        return self::model()->findAll($criteria);
    }
}
