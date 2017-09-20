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
 * This is the model class for table "ophtrintravitinjection_anaestheticagent".
 *
 * The followings are the available columns in table 'ophtrintravitinjection_anaestheticagent':
 *
 * @property int $id
 * @property int $anaesthetic_agent_id
 *
 * The followings are the available model relations:
 * @property anaestheticagent $anaesthetic_agent
 */
class OphTrIntravitrealinjection_AnaestheticAgent extends BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return ElementAnaesthetic the static model class
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
        return 'ophtrintravitinjection_anaestheticagent';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('anaesthetic_agent_id', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, anaesthetic_agent_id', 'safe', 'on' => 'search'),
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
            'anaesthetic_agent' => array(self::BELONGS_TO, 'AnaestheticAgent', 'anaesthetic_agent_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'anaesthetic_agent_id' => 'Type',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('anaesthetic_agent_id', $this->anaesthetic_agent_id);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * get the default anaesthetic agent (if set).
     *
     * @return OphTrIntravitrealinjection_AnaestheticAgent|null
     */
    public static function getDefault()
    {
        if ($defmap = self::model()->find('is_default = ?', array(true))) {
            return $defmap->anaesthetic_agent;
        };
    }
}
