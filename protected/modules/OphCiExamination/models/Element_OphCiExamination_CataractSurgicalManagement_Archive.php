<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;

use Yii;

/**
 * This is the model class for table "et_ophciexamination_cataractsurgicalmanagement_archive".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $event_id
 *
 * The followings are the available model relations:
 */
class Element_OphCiExamination_CataractSurgicalManagement_Archive extends \BaseEventTypeElement
{
    use traits\CustomOrdering;
    public $service;

    protected $auto_update_relations = true;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return Element_OphCiExamination_CataractSurgicalManagement the static model class
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
        return 'et_ophciexamination_cataractsurgicalmanagement_archive';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
                array('city_road, satellite, fast_track, target_postop_refraction, correction_discussed, suitable_for_surgeon_id, supervised, previous_refractive_surgery, vitrectomised_eye, eye_id, reasonForSurgery', 'safe'),
                array('city_road, satellite, fast_track, target_postop_refraction, correction_discussed, suitable_for_surgeon_id, supervised, previous_refractive_surgery, vitrectomised_eye, eye_id', 'required'),
                // The following rule is used by search().
                // Please remove those attributes that should not be searched.
                array('id, city_road, satellite, fast_track, target_postop_refraction, correction_discussed, suitable_for_surgeon_id, supervised, previous_refractive_surgery, vitrectomised_eye', 'safe', 'on' => 'search'),
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
                'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
                'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
                'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
                'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
                'suitable_for_surgeon' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_CataractSurgicalManagement_SuitableForSurgeon', 'suitable_for_surgeon_id'),
                'eye' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_CataractSurgicalManagement_Eye', 'eye_id'),
                'reasonForSurgery' => array(self::MANY_MANY, 'OEModule\OphCiExamination\models\OphCiExamination_Primary_Reason_For_Surgery', 'et_ophciexamination_cataractsurgicalmanagement_surgery_reasons(cataractsurgicalmanagement_id,primary_reason_for_surgery_id)'),
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
                'city_road' => 'At City Road',
                'satellite' => 'At Satellite',
                'fast_track' => 'Straightforward case',
                'target_postop_refraction' => 'Post operative refractive target in dioptres',
                'correction_discussed' => 'The post operative refractive target has been discussed with the patient',
                'suitable_for_surgeon_id' => 'Suitable for surgeon',
                'supervised' => 'Supervised',
                'previous_refractive_surgery' => 'Previous refractive surgery',
                'vitrectomised_eye' => 'Vitrectomised eye',
                'eye_id' => 'Eye',
                'reasonForSurgery' => 'Primary reason for cataract surgery',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return \CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new \CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('event_id', $this->event_id, true);

        $criteria->compare('description', $this->description);

        return new \CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }

    public function getLetter_string()
    {
        $text = array();

        if ($this->city_road) {
            $text[] = 'at City Road';
        }
        if ($this->satellite) {
            $text[] = 'at satellite';
        }
        if ($this->fast_track) {
            $text[] = 'straightforward case';
        }
        $text[] = 'target post-op refraction: '.$this->target_postop_refraction;

        if ($this->correction_discussed) {
            $text[] = 'refractive correction discussed with patient';
        }

        $text[] = 'suitable for '.$this->suitable_for_surgeon->name.' ('.($this->supervised ? 'supervised' : 'unsupervised').')';

        return 'Cataract management: '.implode(', ', $text)."\n";
    }

    public function getPrint_view()
    {
        return 'print_'.$this->getDefaultView();
    }

    public function canCopy()
    {
        return true;
    }

    public function setDefaultOptions(\Patient $patient = null)
    {
        if (in_array(Yii::app()->getController()->getAction()->id, array('created', 'ElementForm'))) {
            $api = Yii::app()->moduleAPI->get('OphTrOperationnote');
            if ($api) {
                $patient = \Patient::model()->findByPk(Yii::app()->request->getParams('patient_id'));
                if (!$patient) {
                    throw new Exception('Patient not found: ' . Yii::app()->request->getParams('patient_id'));
                }
                if ($api->getOpnoteWithCataractElementInCurrentEpisode($patient)) {
                    $this->eye_id = OphCiExamination_CataractSurgicalManagement_Eye::model()->find('name=?', array('Second eye'))->id;
                } else {
                    $this->eye_id = OphCiExamination_CataractSurgicalManagement_Eye::model()->find('name=?', array('First eye'))->id;
                }
            }
        }
    }

    public function __toString()
    {
        return $this->eye->name . ' target post-op: '.$this->target_postop_refraction;
    }
}
