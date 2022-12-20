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
 * This is the model class for table "element_procedurelist".
 *
 * The followings are the available columns in table 'et_ophtroperationnote_cxl':
 *
 * @property string $id
 * @property int $event_id
 * @property int $surgeon_id
 * @property int $assistant_id
 * @property int $anaesthetic_type
 * protocol_id, epithelial_removal_method_id, epithelial_removal_diameter_id, riboflavin_preparation_id,
interval_between_drops, soak_duration_range_id, uv_irradiance_range_id, total_exposure_time_id, uv_pulse_duration_id,
interpulse_duration_id
 *
 * The followings are the available model relations:
 * @property Event $event
 */
class Element_OphTrOperationnote_CXL extends Element_OpNote
{
    public $service;

    public $requires_eye = false;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return ElementOperation the static model class
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
        return 'et_ophtroperationnote_cxl';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, protocol_id, epithelial_removal_method_id, epithelial_removal_diameter_id, riboflavin_preparation_id,
            interval_between_drops_id, soak_duration_range_id, uv_irradiance_range_id, total_exposure_time_id, uv_pulse_duration_id, 
            interpulse_duration_id, uv_total_energy_value, device_id, iontophoresis_id, iontophoresis_current_value,
            iontophoresis_duration_value, cxl_comments, mitomycin_c, epithelial_status_id', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, event_id, protocol_id, epithelial_removal_method_id, epithelial_removal_diameter_id, riboflavin_preparation_id,
            interval_between_drops_id, soak_duration_range_id, uv_irradiance_range_id, total_exposure_time_id, uv_pulse_duration_id, 
            interpulse_duration_id, uv_total_energy_value, device_id, iontophoresis_id, iontophoresis_current_value,
            iontophoresis_duration_value, cxl_comments, mitomycin_c, epithelial_status_id', 'safe', 'on' => 'search'),

            // array('epithelial_removal_method_id', 'required'),
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
            'protocol' => array(self::BELONGS_TO, 'Protocol', 'protocol_id'),
            'epithelial_removal_method' => array(self::BELONGS_TO, 'OphTrOperationnote_CXL_Epithelial_Removal_Method', 'epithelial_removal_method_id'),
            'epithelial_removal_diameter' => array(self::BELONGS_TO, 'OphTrOperationnote_CXL_Epithelial_Removal_Diameter', 'epithelial_removal_diameter_id'),
            'riboflavin_preparation' => array(self::BELONGS_TO, 'OphTrOperationnote_CXL_Riboflavin_Preparation', 'riboflavin_preparation_id'),
            'interval_between_drops' => array(self::BELONGS_TO, 'OphTrOperationnote_CXL_Interval_Between_Drops', 'interval_between_drops_id'),
            'soak_duration_range' => array(self::BELONGS_TO, 'OphTrOperationnote_CXL_Soak_Duration', 'soak_duration_range_id'),
            'uv_irradiance_range' => array(self::BELONGS_TO, 'OphTrOperationnote_CXL_UV_Irradiance_Range', 'uv_irradiance_range_id'),
            'total_exposure_time' => array(self::BELONGS_TO, 'OphTrOperationnote_CXL_Total_Exposure_Time', 'total_exposure_time_id'),
            'uv_pulse_duration' => array(self::BELONGS_TO, 'OphTrOperationnote_CXL_UV_Pulse_Duration', 'uv_pulse_duration_id'),
            'interpulse_duration' => array(self::BELONGS_TO, 'OphTrOperationnote_CXL_Interpulse_Duration', 'interpulse_duration_id'),
            'device' => array(self::BELONGS_TO, 'OphTrOperationnote_CXL_Device', 'device_id'),
            'iontophoresis' => array(self::BELONGS_TO, 'OphTrOperationnote_CXL_Iontophoresis', 'iontophoresis_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'mitomycin_c' => array(self::BELONGS_TO, 'OphTrOperationnote_CXL_Mitomycin', 'mitomycin_c'),
            'epithelial_status' => array(self::BELONGS_TO, 'OphTrOperationnote_CXL_Epithelial_Status', 'epithelial_status_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'protocol_id' => 'Protocol',
            'device_id' => 'Device Used',
            'epithelial_removal_method_id' => 'Epithelial Removal Method',
            'epithelial_removal_diameter_id' => 'Epithelial Removal Diameter',
            'riboflavin_preparation_id' => 'Riboflavin Preparation',
            'interval_between_drops_id' => 'Interval Between Drops',
            'soak_duration_range_id' => 'Soak Duration',
            'uv_irradiance_range_id' => 'UV Irradiance (mW/cm2)',
            'total_exposure_time_id' => 'Total Exposure Time (mins)',
            'uv_pulse_duration_id' => 'UV Pulse Duration',
            'interpulse_duration_id' => 'Inter-pulse Duration',
            'uv_total_energy_value' => 'UV Total Energy (J/cm2)',
            'iontophoresis_id' => 'Iontophoresis Used?',
            'iontophoresis_current_value' => 'Iontophoresis Current (mA)',
            'iontophoresis_duration_value' => 'Iontophoresis Duration (minutes)',
            'cxl_comments' => 'CXL Comments',
            'mitomycin_c' => 'Mitomycin C',
            'epithelial_status_id' => 'Epithelial Status',
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
        $criteria->compare('protocol_id', $this->protocol_id);
        $criteria->compare('device_id', $this->device_id);
        $criteria->compare('epithelial_removal_method_id', $this->epithelial_removal_method_id);
        $criteria->compare('epithelial_removal_diameter_id', $this->epithelial_removal_diameter_id, true);
        $criteria->compare('riboflavin_preparation_id', $this->riboflavin_preparation_id, true);
        $criteria->compare('interval_between_drops_id', $this->interval_between_drops_id);
        $criteria->compare('soak_duration_range_id', $this->soak_duration_range_id);
        $criteria->compare('uv_irradiance_range_id', $this->uv_irradiance_range_id, true);
        $criteria->compare('total_exposure_time_id', $this->total_exposure_time_id, true);
        $criteria->compare('uv_pulse_duration_id', $this->uv_pulse_duration_id);
        $criteria->compare('interpulse_duration_id', $this->interpulse_duration_id);
        $criteria->compare('uv_total_energy_value', $this->uv_total_energy_value);
        $criteria->compare('iontophoresis_id', $this->iontophoresis_id);
        $criteria->compare('iontophoresis_current_value', $this->iontophoresis_current_value);
        $criteria->compare('iontophoresis_duration_value', $this->iontophoresis_duration_value);
        $criteria->compare('cxl_comments', $this->cxl_comments);
        $criteria->compare('mitomycin_c', $this->mitomycin_c);
        $criteria->compare('epithelial_status_id', $this->epithelial_status_id);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function getPrefillableAttributeSet()
    {
        return [
            'protocol_id',
            'epithelial_removal_method_id',
            'epithelial_removal_diameter_id',
            'riboflavin_preparation_id',
            'interval_between_drops_id',
            'soak_duration_range_id',
            'uv_irradiance_range_id',
            'total_exposure_time_id',
            'uv_pulse_duration_id',
            'interpulse_duration_id',
            'uv_total_energy_value',
            'device_id',
            'iontophoresis_id',
            'iontophoresis_current_value',
            'iontophoresis_duration_value',
            'cxl_comments',
            'mitomycin_c',
            'epithelial_status_id'
        ];
    }
}
