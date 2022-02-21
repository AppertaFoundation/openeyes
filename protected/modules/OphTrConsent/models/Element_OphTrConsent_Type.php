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
 * This is the model class for table "et_ophtrconsent_type".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 * @property int $type_id
 *
 * The followings are the available model relations:
 * @property ElementType $element_type
 * @property EventType $eventType
 * @property Event $event
 * @property User $user
 * @property User $usermodified
 * @property OphTrConsent_Type_Type $type
 */
class Element_OphTrConsent_Type extends BaseEventTypeElement
{
    public $service;

    public const TYPE_PATIENT_AGREEMENT_ID = 1;
    public const TYPE_PARENTAL_AGREEMENT_ID = 2;
    public const TYPE_PATIENT_PARENTAL_AGREEMENT_ID = 3;
    public const TYPE_UNABLE_TO_CONSENT_ID = 4;
    public const UNABLE_TO_CONSENT = [
        self::TYPE_PARENTAL_AGREEMENT_ID,
        self::TYPE_UNABLE_TO_CONSENT_ID
    ];

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
        return 'et_ophtrconsent_type';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, type_id, draft', 'safe'),
            array('type_id, ', 'required'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, event_id, type_id, ', 'safe', 'on' => 'search'),
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
            'type' => array(self::BELONGS_TO, 'OphTrConsent_Type_Type', 'type_id'),
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
            'type_id' => 'Type',
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
        $criteria->compare('type_id', $this->type_id);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Set default values for forms on create.
     */
    public function setDefaultOptions(Patient $patient = null)
    {
        if (Yii::app()->getController()->getAction()->id == 'create') {
            // TODO: should not need this with the change to method signature
            if (!$patient = Patient::model()->findByPk($_GET['patient_id'])) {
                throw new Exception("Can't find patient: ".$_GET['patient_id']);
            }

            if ($patient->isChild()) {
                $this->type_id = 2;
            } else {
                $this->type_id = 1;
            }

            $this->draft = 1;
        }
    }

    public function beforeSave()
    {
        if (in_array(Yii::app()->getController()->getAction()->id, array('create', 'update'))) {
            if (!$this->draft) {
                $this->print = 1;
            } else {
                $this->print = 0;
            }
        }

        return parent::beforeSave();
    }

    public function isUnableToConsent()
    {
        return in_array($this->type_id,self::UNABLE_TO_CONSENT);
    }


    public function isEditable()
    {
        return $this->draft;
    }
}
