<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class Element_OphInVisualfields_Image extends BaseEventTypeElement
{
    public function tableName()
    {
        return 'et_ophinvisualfields_image';
    }

    public function rules()
    {
        return array(
            array('left_field_id, right_field_id', 'safe'),
        );
    }

    public function relations()
    {
        return array(
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'left_field' => array(self::BELONGS_TO, 'OphInVisualfields_Field_Measurement', 'left_field_id'),
            'right_field' => array(self::BELONGS_TO, 'OphInVisualfields_Field_Measurement', 'right_field_id'),
        );
    }

    public function afterSave()
    {
        parent::afterSave();
        $this->updateMeasurementReference($this->left_field_id, Eye::LEFT);
        $this->updateMeasurementReference($this->right_field_id, Eye::RIGHT);
    }

    private function updateMeasurementReference($measurement_id, $eye_id)
    {
        $existing = $this->dbConnection->createCommand()
                ->select(array('fm.id fm_id', 'mr.id mr_id'))
                ->from('ophinvisualfields_field_measurement fm')
                ->join('patient_measurement pm', 'pm.id = fm.patient_measurement_id')
                ->join('measurement_reference mr', 'mr.patient_measurement_id = pm.id and mr.event_id = :event_id')
                ->join('event ev', 'ev.id = mr.event_id')->where(
                    'fm.eye_id = :eye_id and mr.event_id = :event_id',
                    array(':eye_id' => $eye_id, ':event_id' => $this->event_id)
                )
                ->queryRow();

        if ($existing) {
            if ($existing['fm_id'] != $measurement_id) {
                MeasurementReference::model()->deleteByPk($existing['mr_id']);
            } else {
                // Nothing to do
                return;
            }
        }

        if ($measurement_id) {
            OphInVisualfields_Field_Measurement::model()->findByPk($measurement_id)->attach($this->event);
        }
    }
}
