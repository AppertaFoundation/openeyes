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

namespace OEModule\OphCiExamination\models;

/**
 * This is the model class for table "et_ophciexamination_cxl_history".
 *
 * The followings are the available columns in table:
 *
 * @property int $id
 * @property int $event_id
 * @property int $eye_id
 * @property int $right_previous_cxl_value
 * @property int $right_previous_refractive_value
 * @property int $right_intacs_kera_ring_value
 * @property int $right_trans_prk_value
 * @property int $right_previous_hsk_keratitis_value
 * @property int $left_previous_cxl_value
 * @property int $left_previous_refractive_value
 * @property int $left_intacs_kera_ring_value
 * @property int $left_trans_prk_value
 * @property int $left_previous_hsk_keratitis_value
 * @property int $asthma_id
 * @property int $eczema_id
 * @property int $hayfever_id
 * @property int $ocular_surface_disease_id
 * @property int $eye_rubber_id
 *
 * The followings are the available model relations:
 */
class Element_OphCiExamination_CXL_History extends \SplitEventTypeElement
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
        return 'et_ophciexamination_cxl_history';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.

        return array(
            array('eye_id, left_previous_cxl_value, right_previous_cxl_value, left_previous_refractive_value, right_previous_refractive_value,
            left_intacs_kera_ring_value, right_intacs_kera_ring_value, left_trans_prk_value, right_trans_prk_value, left_previous_hsk_keratitis_value, 
            right_previous_hsk_keratitis_value, asthma_id, eczema_id, hayfever_id, eye_rubber_id', 'safe'),

            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, eye_id, event_id, left_previous_cxl_value, right_previous_cxl_value, left_previous_refractive_value, right_previous_refractive_value,
            left_intacs_kera_ring_value, right_intacs_kera_ring_value, left_trans_prk_value, right_trans_prk_value, left_previous_hsk_keratitis_value, 
            right_previous_hsk_keratitis_value, asthma_id, eczema_id, hayfever_id, eye_rubber_id', 'safe', 'on' => 'search'),
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
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
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
            'left_previous_cxl_value' => 'Previous CXL',
            'right_previous_cxl_value' => 'Previous CXL',
            'left_previous_refractive_value' => 'Previous Refractive Surgery',
            'right_previous_refractive_value' => 'Previous Refractive Surgery',
            'left_intacs_kera_ring_value' => 'Intacs/Kera-ring',
            'right_intacs_kera_ring_value' => 'Intacs/Kera-ring',
            'left_trans_prk_value' => 'Trans PRK',
            'right_trans_prk_value' => 'Trans PRK',
            'left_previous_hsk_keratitis_value' => 'Previous HSK Keratitis',
            'right_previous_hsk_keratitis_value' => 'Previous HSK Keratitis',
            'asthma_id' => 'Asthma',
            'eczema_id' => 'Eczema',
            'hayfever_id' => 'Hayfever',
            'eye_rubber_id' => 'Eye Rubber'
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

        $criteria = new \CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('left_previous_cxl_value', $this->left_previous_cxl_value);
        $criteria->compare('right_previous_cxl_value', $this->right_previous_cxl_value);
        $criteria->compare('left_previous_refractive_value', $this->left_previous_refractive_value);
        $criteria->compare('right_previous_refractive_value', $this->right_previous_refractive_value);
        $criteria->compare('left_intacs_kera_ring_value', $this->left_intacs_kera_ring_value);
        $criteria->compare('right_intacs_kera_ring_value', $this->right_intacs_kera_ring_value);
        $criteria->compare('left_trans_prk_value', $this->left_trans_prk_value);
        $criteria->compare('right_trans_prk_value', $this->right_trans_prk_value);
        $criteria->compare('left_previous_hsk_keratitis_value', $this->left_previous_hsk_keratitis_value);
        $criteria->compare('right_previous_hsk_keratitis_value', $this->right_previous_hsk_keratitis_value);
        $criteria->compare('asthma_id', $this->asthma_id);
        $criteria->compare('eczema_id', $this->eczema_id);
        $criteria->compare('hayfever_id', $this->hayfever_id);
        $criteria->compare('eye_rubber_id', $this->eye_rubber_id);

        return new \CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

}
