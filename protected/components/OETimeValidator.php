<?php
/**
 * OpenEyes.
 *
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
 * @copyright Copyright 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class OETimeValidator extends CValidator
{
    public $allowEmpty = false;

    /**
     * @param $value
     *
     * @return bool
     */
    public function validateValue($value)
    {
        if (!preg_match('/^(([01]?[0-9])|(2[0-3])):[0-5][0-9]$/', $value)) {
            return false;
        }

        return true;
    }

    /**
     * @param CModel $object
     * @param string $attribute
     */
    protected function validateAttribute($object, $attribute)
    {
        $message = null;

        if (!$object->$attribute) {
            if (!$this->allowEmpty) {
                $message = $this->message ?: Yii::t('yii', '{attribute} cannot be empty.');
            }
        } else {
            if (!$this->validateValue($object->$attribute)) {
                $message = $this->message ?: Yii::t('yii', '{attribute} is not a valid time.');
            }
        }

        if ($message) {
            $this->addError($object, $attribute, $message);
        }
    }
}
