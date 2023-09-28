<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
namespace OEModule\OphDrPrescription\widgets;

class PrescriptionEsignElementWidget extends \EsignElementWidget
{
    public const PRESCRIBER_DISPLAY_ROLE = "Prescriber";

    /**
     * @return string[]
     */
    protected static function getFieldTypes() : array
    {
        return [
            \BaseSignature::TYPE_LOGGEDIN_USER => \EsignPINField::class,
            \BaseSignature::TYPE_OTHER_USER => PinOnlyWidget::class,
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getView()
    {
        $short_name = substr(strrchr(get_class($this), '\\'), 1);
        if ($this->mode === self::$EVENT_PRINT_MODE) {
            return $short_name . "_event_print";
        }
        return $this->getViewNameForPrefix($short_name);
    }

    /**
     * Returns a field widget class by type
     *
     * @param int $type
     * @return string
     * @throws Exception In case $type is invalid
     */
    public static function getWidgetClassByType(int $type) : string
    {
        $field_types = static::getFieldTypes();
        if (array_key_exists($type, $field_types)) {
            return $field_types[$type];
        }

        throw new \Exception("Signature type $type not defined");
    }
}
