<?php

/**
 * OpenEyes.
 *
 * Copyright OpenEyes Foundation, 2017
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class OEPhoneNumberValidator extends CValidator
{
    public $allowEmpty = true;

    public function validateAttribute($object, $attribute)
    {
        if (isset($object->source) && $object->source == 'PASAPI' && !SettingMetadata::model()->getSetting('validate_PASAPI_phone_number')) {
            return;
        }

        $object->$attribute=str_replace(array(' ','-'), '', $object->$attribute);

        $value = $object->$attribute;

        if (preg_match('/\(/', $value) && preg_match('/\)/', $value) && (strpos($value, '(') < strpos($value, ')'))) {
            $value = preg_replace('/\(/', '', $value, 1);
            $value = preg_replace('/\)/', '', $value, 1);
        }

        if ($this->allowEmpty && $this->isEmpty($value)) {
            return;
        }

        if (!$this->isEmpty($value) && $value[0] == '+') {
            $value = substr($value, 0);
        }

        if (!is_numeric($value)) {
            $message=$this->message!==null ? $this->message : Yii::t('yii', '{attribute} must be a valid telephone number.');
            $this->addError($object, $attribute, $message);
            return;
        }
    }
}
