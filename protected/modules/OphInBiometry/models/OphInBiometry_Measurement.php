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
 * This is the model class for table "ophinbiometry_lenstype_lens".
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property string $name
 *
 * The followings are the available model relations:
 * @property ElementType $element_type
 * @property EventType $eventType
 * @property Event $event
 * @property User $user
 * @property User $usermodified
 */
class OphInBiometry_Measurement extends BaseActiveRecord
{
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
        return 'ophinbiometry_measurement';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(

                array('last_name,	first_name,	middle_name,	name_prefix,	name_suffix,	patient_id,	patients_birth_date,	patients_comment,	patients_priv_id,	measurement_date,	r_sphere,	r_cylinder,	r_axis,	r_visual_acuity,	r_eye_state,	r_axial_length_mean,	r_axial_length_cnt,	r_axial_length_std,	r_axial_length_changed,	r_radius_se_mean,	r_radius_se_cnt,	r_radius_se_std,	r_radius_r1,	r_radius_r2,	r_radius_r1_axis,	r_radius_r2_axis,	r_acd_mean,	r_acd_cnt,	r_acd_std,	r_wtw_mean,	r_wtw_cnt,	r_wtw_std,	l_sphere,	l_cylinder,	l_axis,	l_visual_acuity,	l_eye_state,	l_axial_length_mean,	l_axial_length_cnt,	l_axial_length_std,	l_axial_length_changed,	l_radius_se_mean,	l_radius_se_cnt,	l_radius_se_std,	l_radius_r1,	l_radius_r2,	l_radius_r1_axis,	l_radius_r2_axis,	l_acd_mean,	l_acd_cnt,	l_acd_std,	l_wtw_mean,	l_wtw_cnt,	l_wtw_std,	refractive_index,	iol_machine_id,	iol_poll_id', 'safe'),
                //array('patient_id', 'required'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
                array('id', 'safe', 'on' => 'search'),
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
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
                'id' => 'ID',
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

        return new CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }
}
