<?php
/**
 * (C) Apperta Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2023, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class EventAutoGenerateEsign extends BaseCWidget
{
    public $signature_field_name;
    public $save_as_draft_field_name;
    public $signature;
    public $save_as_draft_value_correspondence = 0;
    public $save_as_draft_value_prescription = 0;
    public $container_id;

    public const CORRESPONDENCE = 'correspondence';
    public const PRESCRIPTION = 'prescription';
    public $pin_required_for_event_type = [];
    public const REQUIRE_PIN_SETTING_NAME_BEGINNING = 'require_pin_for_';
    public $user_can_create_prescriptions;

    public function init()
    {
        $posted_signature = $_POST[$this->signature_field_name] ?? null;

        /*
         * OphCoCorrespondence_Signature is used a base class to just have a signature as i cannot
         * create an abstract class and use signature functions
         */
        $this->signature = new OphCoCorrespondence_Signature();
        //Not sure which type should be here
        $this->signature->type = BaseSignature::TYPE_LOGGEDIN_USER;
        $this->signature->signed_user_id = \Yii::app()->session['user']->id;
        $this->signature->signatory_name = \Yii::app()->session['user']->getFullName();

        if (isset($posted_signature['proof'])) {
            $this->signature->proof = $posted_signature['proof'];
            $this->signature->setDataFromProof();
        }

        $this->user_can_create_prescriptions = Yii::app()->user->checkAccess('OprnCreatePrescription');

        $posted_save_as_draft_data = $_POST[$this->save_as_draft_field_name] ?? null;

        if(!$this->user_can_create_prescriptions) {
            $this->save_as_draft_value_prescription = 1;
        }

        if (isset($posted_save_as_draft_data)) {
            if(isset($posted_save_as_draft_data[self::CORRESPONDENCE])) {
                $this->save_as_draft_value_correspondence = $posted_save_as_draft_data[self::CORRESPONDENCE];
            }

            if(isset($posted_save_as_draft_data[self::PRESCRIPTION])) {
                $this->save_as_draft_value_prescription = $posted_save_as_draft_data[self::PRESCRIPTION];
            }
        }

        foreach ([self::CORRESPONDENCE, self::PRESCRIPTION] as $event_type) {
            $this->pin_required_for_event_type[$event_type] = SettingMetadata::model()->checkSetting(
                "require_pin_for_$event_type", 'yes');
        }

        parent::init();
    }

    public function fieldIsShown()
    {
        $is_shown = false;

        foreach ($this->pin_required_for_event_type as $event_type => $value) {
            if ($value) {
                $is_shown = $value;
            }
        }

        return $is_shown;
    }
}
