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

class Element_OphTrOperationnote_Biometry extends Element_OnDemand
{
    // these are legacy and should be removed one switch to using the constants on the Eye model
    const LEFT = Eye::LEFT;
    const RIGHT = Eye::RIGHT;
    const BOTH = Eye::BOTH;

    private $event;

    /**
     * @return bool
     */
    public function hasLeft()
    {
        return $this->eye_id != Eye::RIGHT;
    }

    /**
     * @return bool
     */
    public function hasRight()
    {
        return $this->eye_id != Eye::LEFT;
    }

    /**
     * Returns a value indicating whether this event has the eye of the given side
     *
     * @param string $side The side of the eye to test for (either left or right)
     * @return bool True if this event has the eye of the given side
     * @throws InvalidArgumentException Thrown if the given eye is not valid
     */
    public function hasEye($side)
    {
        switch ($side) {
            case 'left':
                return $this->hasLeft();
            case 'right':
                return $this->hasRight();
            default:
                throw new InvalidArgumentException('Side must be either "left" or "right"');
        }
    }

    /**
     * Returns the static model of the specified AR class.
     *
     * @return Element_OphTrOperationnote_Biometry the static model class
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
        return 'et_ophtroperationnote_biometry';
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'lens_right' => 'Lens',
            'lens_left' => 'Lens',
            'lens_id_right' => 'Lens',
            'lens_id_left' => 'Lens',
            'lens_description_left' => 'Description',
            'lens_description_right' => 'Description',
            'lens_acon_left' => 'a-const',
            'lens_acon_right' => 'a-const',
            'k1_left' => 'K1',
            'k1_right' => 'K1',
            'k2_left' => 'K2',
            'k2_right' => 'K2',
            'k1_axis_left' => 'Axis K1 (D)',
            'k1_axis_right' => 'Axis K1 (D)',
            'axial_length_left' => 'AL',
            'axial_length_right' => 'AL',
            'snr_left' => 'SNR',
            'snr_right' => 'SNR',
            'iol_power_left' => 'IOLPower',
            'predicted_refraction_left' => 'Predicted Refraction',
            'iol_power_right' => 'IOLPower',
            'predicted_refraction_right' => 'Predicted Refraction',
            'target_refraction_left' => 'Target Refraction',
            'target_refraction_right' => 'Target Refraction',
            'acd_left' => 'ACD',
            'acd_right' => 'ACD',
            'status_left' => 'Status',
            'status_right' => 'Status',
            'delta_k_left' => 'K',
            'delta_k_right' => 'K',
            'delta_k_axis_right' => 'K',
            'delta_k_axis_left' => 'K',
            'formula_id_left' => 'Formula Used',
            'formula_id_right' => 'Formula Used',
        );
    }

    public function findAll($attributes = '', $values = array())
    {
        // because we are working with a view, we should present the event as the last Biometry from the view
        // we need the patient ID and the last_modified date of the current event
        // $attributes == "event_id = ?" in this case
        $eventData = Event::model()->findByPk($values[0]);
        $episodeData = Episode::model()->findByPk($eventData->episode_id);

        $latestData = $this->findAllBySql("
						SELECT eob.*, '".$values[0]."' AS event_id FROM et_ophtroperationnote_biometry eob
										WHERE eob.patient_id=".$episodeData->patient_id."
										AND eob.last_modified_date <= '".$eventData->last_modified_date."'
										ORDER BY eob.last_modified_date
										DESC LIMIT 1; ");

        return $latestData;
    }

    // because we are working with a view here we do not need to save
    public function __set($name, $value)
    {
        return true;
    }

    public function save($validation = false, $attributes = null, $allow_overriding = false)
    {
        return true;
    }

    public function isChild()
    {
        return false;
    }
}
