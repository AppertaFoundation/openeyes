<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */


namespace OEModule\OphCoCvi\models;

/**
 * This is the model class for table "ophcocvi_clericinfo_patient_factor_answer".
 *
 * The followings are the available columns in table:
 * @property string $id
 * @property integer $element_id
 * @property integer $ophcocvi_clinicinfo_patient_factor_id
 * @property string $is_factor
 * @property string $comments
 *
 * The followings are the available model relations:
 *
 * @property Element_OphCoCvi_ClericalInfo $element
 * @property OphCoCvi_ClinicalInfo_PatientFactor $patient_factor
 * @property User $user
 * @property User $usermodified
 */

class OphCoCvi_ClericalInfo_PatientFactor_Answer extends \BaseEventTypeElement
{
    /**
     * Returns the static model of the specified AR class.
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
        return 'ophcocvi_clericinfo_patient_factor_answer';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('element_id, patient_factor_id', 'safe'),
            array('element_id, patient_factor_id', 'required'),
            array('id, element_id, ophcocvi_clinicinfo_patient_factor_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'element' => array(self::BELONGS_TO, '\\OEModule\\OphCoCvi\\models\\Element_OphCoCvi_ClericalInfo', 'element_id'),
            'patient_factor' => array(self::BELONGS_TO, '\\OEModule\\OphCoCvi\\models\\OphCoCvi_ClericalInfo_PatientFactor', 'patient_factor_id'),
            'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
        );
    }

    /**
     * @param $factor_id
     * @param $element_id
     * @return factor status for each factor to the clerical element
     */
    public function getFactorAnswer($factor_id, $element_id)
    {
        $criteria = new \CDbCriteria;
        $criteria->select = 'is_factor';
        $criteria->condition = "element_id=:element_id";
        $criteria->addCondition("patient_factor_id=:patient_factor_id");
        $criteria->params = array(':element_id' => $element_id, ':patient_factor_id' => $factor_id);
        $item = OphCoCvi_ClericalInfo_PatientFactor_Answer::model()->find($criteria);

        return $item['is_factor'];
    }

    /**
     * @param $factor_id
     * @param $element_id
     * @return comments for each factor to the clerical element
     */
    public function getComments($factor_id, $element_id)
    {
        $criteria = new \CDbCriteria;
        $criteria->select = 'comments';
        $criteria->condition = "element_id=:element_id";
        $criteria->addCondition("patient_factor_id=:patient_factor_id");
        $criteria->params = array(':element_id' => $element_id, ':patient_factor_id' => $factor_id);
        $item = OphCoCvi_ClericalInfo_PatientFactor_Answer::model()->find($criteria);
        return $item['comments'];

    }

}

?>