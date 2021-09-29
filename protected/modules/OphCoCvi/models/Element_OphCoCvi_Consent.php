<?php
/**
 * (C) Copyright Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCoCvi\models;

/**
 * Class Element_OphCoCvi_Consent
 *
 * @property int $id
 * @property int $event_id
 *
 * @property Event $event
 * @property OphCoCvi_Signature[] $signatures
 */
class Element_OphCoCvi_Consent extends \BaseEventTypeElement
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return static the static model class
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
        return 'et_ophcocvi_consent';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('event_id, consented_to_gp, consented_to_la, consented_to_rcop', 'safe'),
            array('id, event_id, consented_to_gp, consented_to_la, consented_to_rcop', 'safe', 'on' => 'search'),
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
            'event' => array(self::BELONGS_TO, \Event::class, 'event_id'),
            'user' => array(self::BELONGS_TO, \User::class, 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, \User::class, 'last_modified_user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'event_id' => 'Event',
            "consented_to_gp" => 'GP',
            "consented_to_la" => "Local Authority",
            "consented_to_rcop" => "Royal College of Ophthalmologists",
            "consented_for" => "Consented for"
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
        $criteria->compare('consented_to_gp', $this->consented_to_gp);
        $criteria->compare('consented_to_la', $this->consented_to_la,);
        $criteria->compare('consented_to_rcop', $this->consented_to_rcop);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function getConsented_for()
    {
        $items = [];
        foreach (["gp", "la", "rcop"] as $item) {
            if($this->{"consented_to_$item"}) {
                $items[] = $this->getAttributeLabel("consented_to_$item");
            }
        }

        return empty($items) ? "-" : implode(", ", $items);
    }
}
