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
 * Stores the responses for given decision tree nodes for Patient Suitability element.
 *
 * @property string id
 * @property string patientsuit_id
 * @property string eye_id
 * @property string node_id
 * @property string value
 *
 * The following model relations exist
 * @property Element_OphCoTherapyapplication_PatientSuitablity patientsuitability
 * @property OphCoTherapyapplication_DecisionTreeNode node
 * @property Eye eye
 */
class OphCoTherapyapplication_PatientSuitability_DecisionTreeNodeResponse extends BaseActiveRecordVersioned
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
        return 'ophcotherapya_patientsuit_decisiontreenoderesponse';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
                array('patientsuit_id, node_id, eye_id, value', 'safe'),
        );
    }

    /**
     * @return array
     *
     * @see parent::relations()
     */
    public function relations()
    {
        return array(
            'patientsuitability' => array(self::HAS_ONE, 'Element_OphCoTherapyapplication_PatientSuitability', 'patientsuit_id'),
            // note that this should only ever be a side, not both
            'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
            'node' => array(self::BELONGS_TO, 'OphCoTherapyapplication_DecisionTreeNode', 'node_id'),
        );
    }
}
