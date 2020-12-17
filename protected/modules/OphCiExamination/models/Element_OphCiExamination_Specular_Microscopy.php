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
 * This is the model class for table "et_ophciexamination_specular_microscopy".
 *
 * The followings are the available columns in table:
 *
 * @property int $id
 * @property int $event_id
 * @property int $left_endothelial_cell_density_value
 * @property dec $left_coefficient_variation_value
 * @property int $specular_microscope_id
 * @property int $scan_quality_id
 * @property int $right_endothelial_cell_density_value
 * @property dec $right_coefficient_variation_value
 *
 * The followings are the available model relations:
 */
class Element_OphCiExamination_Specular_Microscopy extends \SplitEventTypeElement
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
        return 'et_ophciexamination_specular_microscopy';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
                array('eye_id, specular_microscope_id, scan_quality_id, right_endothelial_cell_density_value, 
                right_coefficient_variation_value, left_endothelial_cell_density_value, left_coefficient_variation_value', 'safe'),
                array('right_endothelial_cell_density_value, left_endothelial_cell_density_value', 'numerical',
                    'integerOnly'=>false,'min'=>500, 'max'=>4000),

                array('right_coefficient_variation_value, left_coefficient_variation_value', 'numerical',
                'integerOnly'=>false),

                array('right_endothelial_cell_density_value, right_coefficient_variation_value',
                'requiredIfSide', 'side' => 'right'),
                array('left_endothelial_cell_density_value, left_coefficient_variation_value',
                'requiredIfSide', 'side' => 'left'),
                // The following rule is used by search().
                // Please remove those attributes that should not be searched.
                array('id, eye_id, event_id, specular_microscope_id, scan_quality_id, right_endothelial_cell_density_value, 
                right_coefficient_variation_value, 
                left_endothelial_cell_density_value, left_coefficient_variation_value', 'safe', 'on' => 'search'),
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
                'specular_microscope' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\OphCiExamination_Specular_Microscope', 'specular_microscope_id'),
                'scan_quality' => array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\ophciexamination_scan_quality', 'scan_quality_id'),
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
                'specular_microscope_id' => 'Specular Microscope',
                'scan_quality_id' => 'Scan Quality',
                'right_endothelial_cell_density_value' => 'Endothelial Cell Density (cells/mm<sup>2</sup>)',
                'right_coefficient_variation_value' => 'Coefficient of Variation (CV)',
                'left_endothelial_cell_density_value' => 'Endothelial Cell Density (cells/mm<sup>2</sup>)',
                'left_coefficient_variation_value' => 'Coefficient of Variation (CV)'
        );
    }

    public function getSpecularMicroscopeName($id)
    {
        $specMicroName = OphCiExamination_Specular_Microscope::model()->find('id=?', $this->id);
        return $specMicroName;
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
        $criteria->compare('left_endothelial_cell_density_value', $this->left_endothelial_cell_density_value);
        $criteria->compare('left_coefficient_variation_value', $this->left_coefficient_variation_value);
        $criteria->compare('specular_microscope_id', $this->specular_microscope_id);
        $criteria->compare('scan_quality_id', $this->scan_quality_id);
        $criteria->compare('right_endothelial_cell_density_value', $this->right_endothelial_cell_density_value);
        $criteria->compare('right_coefficient_variation_value', $this->right_coefficient_variation_value);

        return new \CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }

    /**
     * Can this element be copied (cloned/duplicated)
     * Override to return true if you want an element to be copyable.
     *
     * @return bool
     */
    public function canCopy()
    {
        return true;
    }
}
