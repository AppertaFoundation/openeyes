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
class OEDatetimeValidator extends OEBaseDateValidator
{
    /**
     * @var bool whether the attribute value can be null or empty. Defaults to false.
     *           If this is true, it means the attribute is considered valid when it is empty.
     */
    public $allowEmpty = false;

    public function validateAttribute($object, $attribute)
    {
        $message = null;

        if (!$object->$attribute) {
            if (!$this->allowEmpty) {
                $message = $this->message ?: Yii::t('yii', '{attribute} cannot be empty.');
            }
        } elseif (!$this->parseDateValue($object->$attribute)) {
            $message = $this->message ?: Yii::t('yii', '{attribute} is not a valid date and time.');
        }

        if ($message) {
            $this->addError($object, $attribute, $message);
        }
    }
}
