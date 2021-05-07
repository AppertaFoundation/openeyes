<?php

/**
 * (C) OpenEyes Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class EventAutoGenerateCheckboxesWidget extends BaseCWidget
{
    /**
     * Class of the wrapper div
     * @var string
     */
    public $width_class = 'cols-full';

    /**
     * Whether we need the both eye wrapper div
     * @var bool
     */
    public $element_both_eyes = true;

    /**
     * The suffix would be e.g.: "injection" or "surgery"
     * for auto_generate_prescription_after_injection <- for Injection event
     *     auto_generate_prescription_after_surgery <- for Op Note event
     * @var null
     */
    public ?string $suffix;

    // settings
    public $drug_set_name;
    public $gp_letter_setting;
    public $prescription_setting;
    public $optom_setting;
    public $macro_name;
    public $optom_letter_name;
    public $sets;
    public $default_set_id = null;

    public function init()
    {
        // Prescription checkbox settings
        $prescription_setting = \SettingMetadata::model()->getSetting('auto_generate_prescription_after_' . $this->suffix);
        $prescription_setting = $prescription_setting ? ($prescription_setting === 'on') : false;
        $this->prescription_setting = \Yii::app()->request->getParam('auto_generate_prescription_after_' . $this->suffix, $prescription_setting);

        // GP letter lettings checkbox settings
        $gp_letter_setting = \SettingMetadata::model()->getSetting('auto_generate_gp_letter_after_' . $this->suffix);
        $gp_letter_setting = $gp_letter_setting ? ($gp_letter_setting === 'on') : false;
        $this->gp_letter_setting = \Yii::app()->request->getParam('auto_generate_gp_letter_after_' . $this->suffix, $gp_letter_setting);

        // Optom letter checkbox settings
        $optom_setting = \SettingMetadata::model()->getSetting('auto_generate_optom_letter_after_' . $this->suffix);
        $optom_setting = $optom_setting ? ($optom_setting === 'on') : false;
        $this->optom_setting = \Yii::app()->request->getParam('auto_generate_optom_letter_after_' . $this->suffix, $optom_setting);

        // Values
        $this->drug_set_name = \SettingMetadata::model()->getSetting('default_drug_set_' . $this->suffix);
        $this->macro_name = \SettingMetadata::model()->getSetting('default_letter_' . $this->suffix);
        $this->optom_letter_name = \SettingMetadata::model()->getSetting('default_optom_letter_' . $this->suffix);

        $firm_id = \Yii::app()->session->get('selected_firm_id');
        $firm = $firm_id ? Firm::model()->findByPk($firm_id) : null;
        $subspecialty_id = $firm->getSubspecialtyID();

        if (\Yii::app()->request->isPostRequest) {
            $this->default_set_id = \Yii::app()->request->getParam("auto_generate_prescription_after_{$this->suffix}_set_id");
        } else {
            $default_set = MedicationSet::model()->find([
                'condition' => 'subspecialty_id = :subspecialty_id AND name = :name',
                'params' => [':subspecialty_id' => $subspecialty_id, ':name' => $this->drug_set_name],
                'with' => 'medicationSetRules',
                'together' => true
            ]);
            $this->default_set_id = $default_set->id ?? null;
        }


        $params = [':subspecialty_id' => $subspecialty_id];

        $this->sets = MedicationSet::model()->findAll([
            'condition' => 'subspecialty_id = :subspecialty_id',
            'params' => $params,
            'with' => 'medicationSetRules',
            'together' => true
        ]);

        parent::init();
    }
}
