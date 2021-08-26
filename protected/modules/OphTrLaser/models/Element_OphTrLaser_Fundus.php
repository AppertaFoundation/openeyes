<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * This is the model class for table "et_ophtrlaser_fundus".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property int $eye_id
 *
 * The followings are the available model relations:
 * @property ElementType $element_type
 * @property EventType $eventType
 * @property Event $event
 * @property User $user
 * @property User $usermodified
 * @property Eye $eye
 */
class Element_OphTrLaser_Fundus extends SplitEventTypeElement
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
        return 'et_ophtrlaser_fundus';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, eye_id, left_eyedraw, right_eyedraw, ', 'safe'),
            array('eye_id,', 'required'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, event_id, eye_id, left_eyedraw, right_eyedraw, ', 'safe', 'on' => 'search'),
        );
    }

    /**
     * (non-PHPdoc).
     *
     * @see SplitEventTypeElement::sidedFields()
     */
    public function sidedFields()
    {
        return array('eyedraw');
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
            'eye_id' => 'Eye',
            'left_eyedraw' => 'Left Eyedraw',
            'right_eyedraw' => 'Right Eyedraw',
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

        $criteria->compare('eye_id', $this->eye_id);
        $criteria->compare('left_eyedraw', $this->left_eyedraw);
        $criteria->compare('right_eyedraw', $this->right_eyedraw);

        return new CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
            ));
    }

    public function getSelectedEye()
    {
        if (Yii::app()->getController()->getAction()->id == 'create') {
            // Get the procedure list and eye from the most recent booking for the episode of the current user's subspecialty
            if (!$patient = Patient::model()->findByPk(@$_GET['patient_id'])) {
                throw new SystemException('Patient not found: '.@$_GET['patient_id']);
            }

            if ($episode = $patient->getEpisodeForCurrentSubspecialty()) {
                if ($booking = $episode->getMostRecentBooking()) {
                    return $booking->elementOperation->eye;
                }
            }
        }

        if (isset($_GET['eye'])) {
            return Eye::model()->findByPk($_GET['eye']);
        }

        return new Eye();
    }

    public function getEye()
    {
        // Insert your code to retrieve the current eye here
        return new Eye();
    }

    protected function beforeSave()
    {
        return parent::beforeSave();
    }

    protected function afterSave()
    {
        return parent::afterSave();
    }

    protected function beforeValidate()
    {
        return parent::beforeValidate();
    }
}
