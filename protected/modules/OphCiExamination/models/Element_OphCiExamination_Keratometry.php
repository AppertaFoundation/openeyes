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
 * This is the model class for table "et_ophciexamination_keratometry".
 *
 * The followings are the available columns in table:
 *
 *
 * @property int $id
 * @property int $event_id
 * @property int $eye_id
 *
 * @property var $tomographer_id
 *
 * @property int $right_anterior_k1_value
 * @property dec $right_axis_anterior_k1_value
 * @property var $right_anterior_k2_value
 * @property int $right_axis_anterior_k2_value
 * @property int $right_kmax_value
 * @property int $right_posterior_k2_value
 * @property var $right_thinnest_point_pachymetry_value
 * @property dec $right_b-a_index_value
 *
 * @property int $left_anterior_k1_value
 * @property dec $left_axis_anterior_k1_value
 * @property var $left_anterior_k2_value
 * @property int $left_axis_anterior_k2_value
 * @property int $left_kmax_value
 * @property int $left_posterior_k2_value
 * @property var $left_thinnest_point_pachymetry_value
 * @property dec $left_b-a_index_value
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
    use traits\CustomOrdering;
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
                array('eye_id, 
                right_anterior_k1_value, right_axis_anterior_k1_value,
                 right_anterior_k2_value, right_axis_anterior_k2_value, right_kmax_value, 
                 right_posterior_k2_value, right_thinnest_point_pachymetry_value, right_ba_index_value, 
                 tomographer_id,
                 left_anterior_k1_value, left_axis_anterior_k1_value,
                 left_anterior_k2_value, left_axis_anterior_k2_value, left_kmax_value, 
                 left_posterior_k2_value, left_thinnest_point_pachymetry_value, left_ba_index_value,
                 right_quality_front, right_quality_back, left_quality_front, left_quality_back, 
                 right_cl_removed, left_cl_removed, right_flourescein_value, left_flourescein_value', 'safe'),

//            array('right_anterior_k1_value, left_anterior_k1_value, right_anterior_k2_value, left_anterior_k2_value,
//                    right_quality_front, right_quality_back, left_quality_front, left_quality_back,
//                    right_axis_anterior_k1_value, left_axis_anterior_k1_value, right_axis_anterior_k2_value, left_axis_anterior_k2_value,
//                    right_kmax_value, left_kmax_value, right_thinnest_point_pachymetry_value, left_thinnest_point_pachymetry_value,
//                    right_flourescein_value, left_flourescein_value, right_cl_removed, left_cl_removed', 'required'),

            array('right_anterior_k1_value, right_anterior_k2_value, right_kmax_value,
                    left_anterior_k1_value, left_anterior_k2_value, left_kmax_value', 'numerical',
                'integerOnly'=>false,'min'=>1, 'max'=>150),


            array('right_axis_anterior_k1_value, right_axis_anterior_k2_value, 
            left_axis_anterior_k1_value, left_axis_anterior_k2_value', 'numerical',
                'integerOnly'=>false,'min'=>-150, 'max'=>-1),


            array('right_thinnest_point_pachymetry_value, left_thinnest_point_pachymetry_value', 'numerical',
                'integerOnly'=>false,'min'=>10, 'max'=>800),


            array('right_ba_index_value, left_ba_index_value', 'numerical',
                'integerOnly'=>false,'min'=>0, 'max'=>999),

            array('right_kmax_value', 'kValueCompare', 'compare' => 'right_anterior_k2_value', 'side' => 'Right'),
            array('left_kmax_value', 'kValueCompare', 'compare' => 'left_anterior_k2_value', 'side' => 'Left'),
            array('right_anterior_k2_value', 'kValueCompare', 'compare' => 'right_anterior_k1_value', 'side' => 'Right'),
            array('left_anterior_k2_value', 'kValueCompare', 'compare' => 'left_anterior_k1_value', 'side' => 'Left'),

            // The following rule is used by search().
                // Please remove those attributes that should not be searched.
                array('id, event_id,
                right_anterior_k1_value, right_axis_anterior_k1_value,
                 right_anterior_k2_value, right_axis_anterior_k2_value, right_kmax_value, 
                 right_thinnest_point_pachymetry_value, right_ba_index_value, 
                 tomographer_id,
                 left_anterior_k1_value, left_axis_anterior_k1_value,
                 left_anterior_k2_value, left_axis_anterior_k2_value, left_kmax_value, 
                 left_thinnest_point_pachymetry_value, left_ba_index_value,
                 right_quality_front, right_quality_back, left_quality_front, left_quality_back, 
                 right_cl_removed, left_cl_removed, right_flourescein_value, left_flourescein_value', 'safe', 'on' => 'search'),
        );
    }

    public function kValueCompare($KValue, $params)
    {
        if ($this->hasLeft() and $params['side'] == "Left") {
            if ($this->{$KValue} < $this->{$params['compare']}) {
                $this->addError($KValue, $this->getAttributeLabel($KValue) . ' (' . $params['side']
                    . ') must be bigger than ' . $this->getAttributeLabel($params['compare']) . ' ('
                    . $params['side'] . ')');
            }
        }
        if ($this->hasRight() and $params['side'] == "Right") {
            if ($this->{$KValue} < $this->{$params['compare']}) {
                $this->addError($KValue, $this->getAttributeLabel($KValue) . ' (' . $params['side']
                    . ') must be bigger than ' . $this->getAttributeLabel($params['compare']) . ' ('
                    . $params['side'] . ')');
            }
        }
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
            'tomographer_id' => array(self::BELONGS_TO, 'ophciexamination_tomographer_device', 'id'),
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
            'right_anterior_k1_value' => 'Front K1',
            'right_axis_anterior_k1_value' => 'Back K1',
            'right_anterior_k2_value' => 'Front K2',
            'right_axis_anterior_k2_value' => 'Back K2',
            'right_kmax_value' => 'Kmax',
            'left_anterior_k1_value' => 'Front K1',
            'left_axis_anterior_k1_value' => 'Back K1',
            'left_anterior_k2_value' => 'Front K2',
            'left_axis_anterior_k2_value' => 'Back K2',
            'left_kmax_value' => 'Kmax',
            'tomographer_id' => 'Tomographer Device',
            'right_posterior_k2_value' => 'Posterior K2',
            'right_thinnest_point_pachymetry_value' => 'Thinnest Point Pachymetry (µm)',
            'right_ba_index_value' => 'B-A Index',
            'left_posterior_k2_value' => 'Posterior K2',
            'left_thinnest_point_pachymetry_value' => 'Thinnest Point Pachymetry (µm)',
            'left_ba_index_value' => 'B-A Index',
            'right_quality_front' => 'Quality Score - Front',
            'right_quality_back' => 'Quality Score - Back',
            'left_quality_front' => 'Quality Score - Front',
            'left_quality_back' => 'Quality Score - Back',
            'right_cl_removed' => 'CL Removed?',
            'left_cl_removed' => 'CL Removed?',
            'right_flourescein_value' => 'Flourescein',
            'left_flourescein_value' => 'Flourescein',
        );
    }

    public function afterValidate()
    {

        // When an eye is closed we do not want to validate that eye's info.
            $side_checks = array('_anterior_k1_value', '_axis_anterior_k1_value', '_anterior_k2_value',
                '_axis_anterior_k2_value', '_kmax_value', '_thinnest_point_pachymetry_value');
            foreach (array('left', 'right') as $side) {
                $check = 'has' . ucfirst($side);
                if ($this->$check()) {
                    foreach ($side_checks as $f) {
                        if (!$this->{$side . $f}) {
                            $this->addError($side.$f, ucfirst($side) . ' ' . $this->getAttributeLabel($f) . ' cannot be blank');
                        }
                    }
                }
            }

            parent::afterValidate();
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
        $criteria->compare('right_thinnest_point_pachymetry_value', $this->right_thinnest_point_pachymetry_value, true);
        $criteria->compare('right_ba_index_value', $this->right_ba_index_value, true);
        $criteria->compare('left_thinnest_point_pachymetry_value', $this->left_thinnest_point_pachymetry_value, true);
        $criteria->compare('left_ba_index_value', $this->left_ba_index_value, true);
        $criteria->compare('right_quality_front', $this->right_quality_front, true);
        $criteria->compare('right_quality_back', $this->right_quality_back, true);
        $criteria->compare('left_quality_front', $this->left_quality_front, true);
        $criteria->compare('left_quality_back', $this->left_quality_back, true);
        $criteria->compare('right_cl_removed', $this->right_cl_removed, true);
        $criteria->compare('left_cl_removed', $this->left_cl_removed, true);
        $criteria->compare('right_flourescein', $this->right_flourescein_value, true);
        $criteria->compare('left_flourescein', $this->left_flourescein_value, true);

        return new \CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }
}
