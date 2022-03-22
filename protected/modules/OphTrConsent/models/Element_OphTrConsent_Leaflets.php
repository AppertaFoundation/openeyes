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
 * This is the model class for table "et_ophtrconsent_leaflets".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 *
 * The followings are the available model relations:
 * @property ElementType $element_type
 * @property EventType $eventType
 * @property Event $event
 * @property User $user
 * @property User $usermodified
 */
class Element_OphTrConsent_Leaflets extends BaseEventTypeElement
{
    public $service;

    /**
     * Returns the static model of the specified AR class.
     *
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
        return 'et_ophtrconsent_leaflets';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, information, anaesthetic_leaflet', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, event_id', 'safe', 'on' => 'search'),
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
            'element_type' => array(self::HAS_ONE, 'ElementType', 'id', 'on' => "element_type.class_name='".get_class($this)."'"),
            'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'consultant' => array(self::BELONGS_TO, 'User', 'consultant_id'),
            'leaflets' => array(self::HAS_MANY, 'OphTrConsent_Leaflets', 'element_id'),
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
            'information' => 'An information leaflet has been provided',
            'anaesthetic_leaflet' => 'Anaesthetic leaflet has been provided',
            'witness_required' => 'Witness required',
            'interpreter_required' => 'Interpreter required',
            'parent_guardian' => 'Parent/guardian',
            'consultant_id' => 'Consultant',
            'include_supplementary_consent' => 'Include supplementary consent form',
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

    protected function afterSave()
    {
        $existing_leaflet_ids = array();

        foreach ($this->leaflets as $leaflet) {
            $existing_leaflet_ids[] = $leaflet->leaflet_id;
        }

        if (isset($_POST['OphTrConsent_Leaflet']) && is_array($_POST['OphTrConsent_Leaflet'])) {
            foreach ($_POST['OphTrConsent_Leaflet'] as $id) {
                if (!in_array($id, $existing_leaflet_ids)) {
                    $leaflet = new OphTrConsent_Leaflets();
                    $leaflet->element_id = $this->id;
                    $leaflet->leaflet_id = $id;
                    if (!$leaflet->save()) {
                        throw new Exception('Unable to save leaflet: '.print_r($leaflet->getErrors(), true));
                    }
                }
            }
        }

        foreach ($existing_leaflet_ids as $id) {
            if (!isset($_POST['OphTrConsent_Leaflet']) || !in_array($id, $_POST['OphTrConsent_Leaflet'])) {
                $leaflet = OphTrConsent_Leaflets::model()->find('element_id=:element_id and leaflet_id=:leaflet_id', array(':element_id' => $this->id, ':leaflet_id' => $id));
                if (!$leaflet->delete()) {
                    throw new Exception('Unable to delete leaflet: '.print_r($leaflet->getErrors(), true));
                }
            }
        }

        return parent::afterSave();
    }

    /**
     * Get ids of leaflets currently associated with the element.
     */
    public function getLeafletValues()
    {
        $leaflet_values = array();

        foreach ($this->leaflets as $leaflet) {
            $leaflet_values[] = $leaflet->leaflet_id;
        }

        return $leaflet_values;
    }
}
