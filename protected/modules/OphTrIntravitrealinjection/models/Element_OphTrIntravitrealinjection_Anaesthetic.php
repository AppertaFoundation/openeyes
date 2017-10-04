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
 * This is the model class for table "et_ophtrintravitinjection_anaesthetic".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property int $left_anaesthetictype_id
 * @property int $left_anaestheticdelivery_id
 * @property int $left_anaestheticagent_id
 * @property int $right_anaesthetictype_id
 * @property int $right_anaestheticdelivery_id
 * @property int $right_anaestheticagent_id
 *
 * The followings are the available model relations:
 * @property ElementType $element_type
 * @property EventType $eventType
 * @property Event $event
 * @property User $user
 * @property User $usermodified
 * @property AnaestheticType $left_anaesthetictype
 * @property AnaestheticDelivery $left_anaestheticdelivery
 * @property AnaestheticAgent $left_anaestheticagent
 * @property AnaestheticType $right_anaesthetictype
 * @property AnaestheticAgent $right_anaestheticagent
 * @property AnaestheticDelivery $right_anaestheticdelivery
 */
class Element_OphTrIntravitrealinjection_Anaesthetic extends SplitEventTypeElement
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
        return 'et_ophtrintravitinjection_anaesthetic';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, eye_id, left_anaesthetictype_id, left_anaestheticdelivery_id, left_anaestheticagent_id, '.
                    'right_anaesthetictype_id, right_anaestheticdelivery_id, right_anaestheticagent_id', 'safe', ),
            array('eye_id', 'required'),
            array('left_anaesthetictype_id, left_anaestheticdelivery_id, left_anaestheticagent_id', 'requiredIfSide', 'side' => 'left'),
            array('right_anaesthetictype_id, right_anaestheticdelivery_id, right_anaestheticagent_id', 'requiredIfSide', 'side' => 'right'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, event_id, eye_id, left_anaesthetictype_id, left_anaestheticagent_id, left_anaestheticagent_id, '.
                    'right_anaesthetictype_id, right_anaestheticdelivery_id, right_anaestheticagent_id', 'safe', 'on' => 'search', ),
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
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
            'left_anaesthetictype' => array(self::BELONGS_TO, 'AnaestheticType', 'left_anaesthetictype_id'),
            'left_anaestheticdelivery' => array(self::BELONGS_TO, 'AnaestheticDelivery', 'left_anaestheticdelivery_id'),
            'left_anaestheticagent' => array(self::BELONGS_TO, 'AnaestheticAgent', 'left_anaestheticagent_id'),
            'right_anaesthetictype' => array(self::BELONGS_TO, 'AnaestheticType', 'right_anaesthetictype_id'),
            'right_anaestheticdelivery' => array(self::BELONGS_TO, 'AnaestheticDelivery', 'right_anaestheticdelivery_id'),
            'right_anaestheticagent' => array(self::BELONGS_TO, 'AnaestheticAgent', 'right_anaestheticagent_id'),
        );
    }

    /**
     * (non-PHPdoc).
     *
     * @see SplitEventTypeElement::sidedFields()
     */
    public function sidedFields()
    {
        return array('anaesthetictype_id', 'anaestheticdelivery_id', 'anaestheticagent_id');
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'event_id' => 'Event',
            'left_anaesthetictype_id' => 'Anaesthetic Type',
            'left_anaestheticdelivery_id' => 'Anaesthetic Delivery',
            'left_anaestheticagent_id' => 'Anaesthetic Agent',
            'right_anaesthetictype_id' => 'Anaesthetic Type',
            'right_anaestheticdelivery_id' => 'Anaesthetic Delivery',
            'right_anaestheticagent_id' => 'Anaesthetic Agent',
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
        $criteria->compare('left_anaesthetictype_id', $this->left_anaesthetictype_id);
        $criteria->compare('left_anaestheticdelivery_id', $this->left_anaestheticdelivery_id);
        $criteria->compare('left_anaestheticagent_id', $this->left_anaestheticagent_id);
        $criteria->compare('right_anaesthetictype_id', $this->right_anaesthetictype_id);
        $criteria->compare('right_anaestheticdelivery_id', $this->right_anaestheticdelivery_id);
        $criteria->compare('right_anaestheticagent_id', $this->right_anaestheticagent_id);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * (non-PHPdoc).
     *
     * @see SplitEventTypeElement::setDefaultOptions()
     */
    public function setDefaultOptions(Patient $patient = null)
    {
        $def_anaesthetictype = OphTrIntravitrealinjection_AnaestheticType::getDefault();
        $def_anaestheticagent = OphTrIntravitrealinjection_AnaestheticAgent::getDefault();

        foreach (array('left', 'right') as $side) {
            $this->{$side.'_anaesthetictype_id'} = $def_anaesthetictype ? $def_anaesthetictype->id : null;
            $this->{$side.'_anaestheticagent_id'} = $def_anaestheticagent ? $def_anaestheticagent->id : null;
        }
    }

    /*
     * Get anaesthetic types for the form
     */
    public function getAnaestheticTypes()
    {
        $options = array();
        foreach (OphTrIntravitrealinjection_AnaestheticType::model()->with('anaesthetic_type')->findAll(array('order' => 't.display_order asc')) as $ad) {
            $options[$ad->anaesthetic_type->id] = $ad->anaesthetic_type->name;
        }

        return $options;
    }

    /*
     * Get anaesthetic delivery types
     */
    public function getAnaestheticDeliveryTypes()
    {
        $options = array();
        foreach (OphTrIntravitrealinjection_AnaestheticDelivery::model()->with('anaesthetic_delivery')->findAll(array('order' => 't.display_order asc')) as $ad) {
            $options[$ad->anaesthetic_delivery->id] = $ad->anaesthetic_delivery->name;
        }

        return $options;
    }

    /**
     * Get the the agent options for side.
     *
     * @param string $side
     *
     * @return multitype:NULL unknown
     */
    public function getAnaestheticAgentsForSide($side)
    {
        $i_agents = OphTrIntravitrealinjection_AnaestheticAgent::model()->with('anaesthetic_agent')->findAll(array('order' => 't.display_order asc'));
        $agents = array();
        $found = false;
        foreach ($i_agents as $ia) {
            $agents[] = $ia->anaesthetic_agent;
            if ($this->{$side.'_anaestheticagent_id'} == $ia->anaesthetic_agent_id) {
                $found = true;
            }
        }
        // ensure that if the agent for this element is not available anymore, its still available as an option
        if ($id = $this->{$side.'_anaestheticagent_id'} && !$found) {
            $agents[] = $this->{$side.'_anaestheticagent'};
        }

        return $agents;
    }
}
