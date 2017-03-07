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
 *
 * @property var $tomographer_id
 * @property int $tomographer_scan_quality_id
 *
 * @property int $right_anterior_k1_value
 * @property dec $right_axis_anterior_k1_value
 * @property var $right_anterior_k2_value
 * @property int $right_axis_anterior_k2_value
 * @property int $right_kmax_value
 * @property int $right_posterior_k2_value
 * @property var $right_thinnest_point_pachymetry_value
 * @property dec $right_b-a_index_value
 * @property int $right_keratoconus_stage_id
 *
 * @property int $left_anterior_k1_value
 * @property dec $left_axis_anterior_k1_value
 * @property var $left_anterior_k2_value
 * @property int $left_axis_anterior_k2_value
 * @property int $left_kmax_value
 * @property int $left_posterior_k2_value
 * @property var $left_thinnest_point_pachymetry_value
 * @property dec $left_b-a_index_value
 * @property int $left_keratoconus_stage_id
 *
 * @property int $left_quality_front
 * @property int $left_quality_back
 * @property int $right_quality_front
 * @property int $right_quality_back
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
                array('eye_id, topographer_id, topographer_scan_quality_id, 
                right_anterior_k1_value, right_axis_anterior_k1_value,
                 right_anterior_k2_value, right_axis_anterior_k2_value, right_kmax_value, 
                 right_posterior_k2_value, right_thinnest_point_pachymetry_value, right_ba_index_value, 
                 tomographer_id, tomographer_scan_quality_id,
                 left_anterior_k1_value, left_axis_anterior_k1_value,
                 left_anterior_k2_value, left_axis_anterior_k2_value, left_kmax_value, 
                 left_posterior_k2_value, left_thinnest_point_pachymetry_value, left_ba_index_value,
                 keratoconus_stage_id, right_quality_front, right_quality_back, left_quality_front, left_quality_back, 
                 right_cl_removed, left_cl_removed', 'safe'),

                array('right_anterior_k1_value, right_anterior_k2_value, right_kmax_value,  
                    left_anterior_k1_value, left_anterior_k2_value, left_kmax_value',
                    'in','range'=>range(30,80)),

                array('right_axis_anterior_k1_value, right_axis_anterior_k2_value, left_axis_anterior_k1_value, left_axis_anterior_k2_value',
                        'in','range'=>range(0,180)),

            array('right_thinnest_point_pachymetry_value, left_thinnest_point_pachymetry_value',
                'in','range'=>range(100,800)),

            array('right_ba_index_value, left_ba_index_value', 'numerical',
                'integerOnly'=>false,'min'=>0, 'max'=>10),


            // The following rule is used by search().
                // Please remove those attributes that should not be searched.
                array('id, event_id, topographer_id, topographer_scan_quality_id, 
                right_anterior_k1_value, right_axis_anterior_k1_value,
                 right_anterior_k2_value, right_axis_anterior_k2_value, right_kmax_value, 
                 right_thinnest_point_pachymetry_value, right_ba_index_value, 
                 tomographer_id, tomographer_scan_quality_id,
                 left_anterior_k1_value, left_axis_anterior_k1_value,
                 left_anterior_k2_value, left_axis_anterior_k2_value, left_kmax_value, 
                 left_thinnest_point_pachymetry_value, left_ba_index_value, keratoconus_stage_id,
                 right_quality_front, right_quality_back, left_quality_front, left_quality_back, 
                 right_cl_removed, left_cl_removed', 'safe', 'on' => 'search'),
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
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'topographer_id' => array(self::BELONGS_TO, 'ophciexamination_topographer_device', 'id'),
            'topographer_scan_quality_id' => array(self::BELONGS_TO, 'ophciexamination_scan_quality', 'id'),
            'tomographer_id' => array(self::BELONGS_TO, 'ophciexamination_tomographer_device', 'id'),
            'tomographer_scan_quality_id' => array(self::BELONGS_TO, 'ophciexamination_scan_quality', 'id'),
            'keratoconus_stage_id' => array(self::BELONGS_TO, 'ophciexamination_keratoconus_stage', 'id'),
            'right_quality_front' => array(self::BELONGS_TO, 'ophciexamination_cxl_quality_score', 'id'),
            'right_quality_back' => array(self::BELONGS_TO, 'ophciexamination_cxl_quality_score', 'id'),
            'left_quality_front' => array(self::BELONGS_TO, 'ophciexamination_cxl_quality_score', 'id'),
            'left_quality_back' => array(self::BELONGS_TO, 'ophciexamination_cxl_quality_score', 'id'),
            'right_cl_removed' => array(self::BELONGS_TO, 'ophciexamination_cxl_cl_removed', 'id'),
            'left_cl_removed' => array(self::BELONGS_TO, 'ophciexamination_cxl_cl_removed', 'id'),

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
            'right_anterior_k1_value' => 'Front K1 Value (D)',
            'right_axis_anterior_k1_value' => 'Back K1 Value (degrees)',
            'right_anterior_k2_value' => 'Front K2 Value (D)',
            'right_axis_anterior_k2_value' => 'Back K2 Value (degrees)',
            'right_kmax_value' => 'Kmax range (D)',
            'left_anterior_k1_value' => 'Front K1 Value (D)',
            'left_axis_anterior_k1_value' => 'Back K1 Value (degrees)',
            'left_anterior_k2_value' => 'Front K2 Value (D)',
            'left_axis_anterior_k2_value' => 'Back K2 Value (degrees)',
            'left_kmax_value' => 'Kmax range (D)',
            'tomographer_id' => 'Tomographer Device',
            'tomographer_scan_quality_id' => 'Tomographer Scan Quality',
            'right_posterior_k2_value' => 'Posterior K2 Value (D)',
            'right_thinnest_point_pachymetry_value' => 'Thinnest Point Pachymetry Value (µm)',
            'right_ba_index_value' => 'B-A Index Value',
            'left_posterior_k2_value' => 'Posterior K2 Value (D)',
            'left_thinnest_point_pachymetry_value' => 'Thinnest Point Pachymetry Value (µm)',
            'left_ba_index_value' => 'B-A Index Value',
            'keratoconus_stage_id' => 'Keratoconus Stage',
            'right_quality_front' => 'Quality Score - Front',
            'right_quality_back' => 'Quality Score - Back',
            'left_quality_front' => 'Quality Score - Front',
            'left_quality_back' => 'Quality Score - Back',
            'right_cl_removed' => 'CL Removed?',
            'left_cl_removed' => 'CL Removed?',
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
        $criteria->compare('eye_id', $this->eye_id, true);
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('topographer_id', $this->topographer_id, true);
        $criteria->compare('topographer_scan_quality_id', $this->topographer_scan_quality_id, true);
        $criteria->compare('right_anterior_k1_value', $this->right_anterior_k1_value, true);
        $criteria->compare('right_axis_anterior_k1_value', $this->right_axis_anterior_k1_value, true);
        $criteria->compare('right_anterior_k2_value', $this->right_anterior_k2_value, true);
        $criteria->compare('right_axis_anterior_k2_value', $this->right_axis_anterior_k2_value, true);
        $criteria->compare('right_kmax_value', $this->right_kmax_value, true);
        $criteria->compare('left_anterior_k1_value', $this->left_anterior_k1_value, true);
        $criteria->compare('left_axis_anterior_k1_value', $this->left_axis_anterior_k1_value, true);
        $criteria->compare('left_anterior_k2_value', $this->left_anterior_k2_value, true);
        $criteria->compare('left_axis_anterior_k2_value', $this->left_axis_anterior_k2_value, true);
        $criteria->compare('left_kmax_value', $this->left_kmax_value, true);
        $criteria->compare('tomographer_id', $this->tomographer_id, true);
        $criteria->compare('tomographer_scan_quality_id', $this->tomographer_scan_quality_id, true);
        $criteria->compare('right_thinnest_point_pachymetry_value', $this->right_thinnest_point_pachymetry_value, true);
        $criteria->compare('right_ba_index_value', $this->right_ba_index_value, true);
        $criteria->compare('left_thinnest_point_pachymetry_value', $this->left_thinnest_point_pachymetry_value, true);
        $criteria->compare('left_ba_index_value', $this->left_ba_index_value, true);
        $criteria->compare('keratoconus_stage_id', $this->keratoconus_stage_id, true);
        $criteria->compare('right_quality_front', $this->right_quality_front, true);
        $criteria->compare('right_quality_back', $this->right_quality_back, true);
        $criteria->compare('left_quality_front', $this->left_quality_front, true);
        $criteria->compare('left_quality_back', $this->left_quality_back, true);
        $criteria->compare('right_cl_removed', $this->right_cl_removed, true);
        $criteria->compare('left_cl_removed', $this->left_cl_removed, true);

        return new \CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }

}