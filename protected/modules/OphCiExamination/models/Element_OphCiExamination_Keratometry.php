<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;

/**
 * This is the model class for table "et_ophciexamination_keratometry".
 *
 * The followings are the available columns in table:
 *
 *
 * @property int $id
 * @property int $event_id
 * @property int $eye_id
 * @property int $topographer_id
 * @property int $topographer_scan_quality_id
 * @property int $anterior_k1_value
 * @property dec $axis_anterior_k1_value
 * @property var $anterior_k2_value
 * @property int $axis_anterior_k2_value
 * @property int $kmax_value
 * @property var $tomographer_id
 * @property int $tomographer_scan_quality_id
 * @property int $posterior_k2_value
 * @property var $thinnest_point_pachymetry_value
 * @property dec $a_index_value
 * @property int $keratoconus_stage_id
 *
 * The followings are the available model relations:
 */
class Element_OphCiExamination_Keratometry extends \SplitEventTypeElement
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
        return 'et_ophciexamination_keratometry';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {


        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
                array('topographer_id, topographer_scan_quality_id, anterior_k1_value, axis_anterior_k1_value,
                 anterior_k2_value, axis_anterior_k2_value, kmax_value, tomographer_id, tomographer_scan_quality_id,
                 posterior_k2_value, thinnest_point_pachymetry_value, a_index_value, keratoconus_stage_id', 'safe'),
                // The following rule is used by search().
                // Please remove those attributes that should not be searched.
                array('id, event_id, topographer_id, topographer_scan_quality_id, anterior_k1_value, axis_anterior_k1_value,
                 anterior_k2_value, axis_anterior_k2_value, kmax_value, tomographer_id, tomographer_scan_quality_id,
                 posterior_k2_value, thinnest_point_pachymetry_value, a_index_value, keratoconus_stage_id', 'safe', 'on' => 'search'),
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
            'topographer_id' => array(self::BELONGS_TO, 'ophciexamination_topographer_device', 'id'),
            'topographer_scan_quality_id' => array(self::BELONGS_TO, 'ophciexamination_scan_quality', 'id'),
            'tomographer_id' => array(self::BELONGS_TO, 'ophciexamination_tomographer_device', 'id'),
            'tomographer_scan_quality_id' => array(self::BELONGS_TO, 'ophciexamination_scan_quality', 'id'),
            'keratoconus_stage_id' => array(self::BELONGS_TO, 'ophciexamination_keratoconus_stage', 'id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'topographer_id' => 'Topographer Device',
            'topographer_scan_quality_id' => 'Topographer Scan Quality',
            'anterior_k1_value' => 'Anterior K1 Value',
            'axis_anterior_k1_value' => 'Axis Anterior K1 Value',
            'anterior_k2_value' => 'Anterior K2 Value',
            'axis_anterior_k2_value' => 'Axis Anterior K2 Value',
            'kmax_value' => 'Kmax range',
            'tomographer_id' => 'Tomographer Device',
            'tomographer_scan_quality_id' => 'Topographer Scan Quality',
            'posterior_k2_value' => 'Posterior K2 Value',
            'thinnest_point_pachymetry_value' => 'Thinnest Point Pachymetry Value',
            'a_index_value' => 'A Index Value',
            'keratoconus_stage_id' => 'Keratoconus Stage',
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
        $criteria->compare('topographer_id', $this->topographer_id, true);
        $criteria->compare('topographer_scan_quality_id', $this->topographer_scan_quality_id, true);
        $criteria->compare('anterior_k1_value', $this->anterior_k1_value, true);
        $criteria->compare('axis_anterior_k1_value', $this->axis_anterior_k1_value, true);
        $criteria->compare('anterior_k2_value', $this->anterior_k2_value, true);
        $criteria->compare('axis_anterior_k2_value', $this->axis_anterior_k2_value, true);
        $criteria->compare('kmax_value', $this->kmax_value, true);
        $criteria->compare('tomographer_id', $this->tomographer_id, true);
        $criteria->compare('tomographer_scan_quality_id', $this->tomographer_scan_quality_id, true);
        $criteria->compare('posterior_k2_value', $this->posterior_k2_value, true);
        $criteria->compare('thinnest_point_pachymetry_value', $this->thinnest_point_pachymetry_value, true);
        $criteria->compare('a_index_value', $this->a_index_value, true);
        $criteria->compare('keratoconus_stage_id', $this->keratoconus_stage_id, true);

        return new \CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }

}
