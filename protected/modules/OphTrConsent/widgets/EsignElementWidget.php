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

namespace OEModule\OphTrConsent\widgets;

class EsignElementWidget extends \EsignElementWidget
{
    /**
     * @inheritDoc
     */
    public function isSigningAllowed(): bool
    {
        return $this->mode === \BaseEventElementWidget::$EVENT_VIEW_MODE;
    }

    /**
     * @inheritDoc
     */
    protected function getView()
    {
        $path = explode('\\', __CLASS__);
        $prefix = "application.widgets.views." . array_pop($path);
        if ($this->mode === self::$EVENT_PRINT_MODE) {
            return $prefix . "_event_print";
        }
        // View is the same in edit mode and view mode
        return $prefix . "_event_edit";
    }

    /**
     * @inheritDoc
     */
    protected static function getFieldTypes(): array
    {
        return [
            \BaseSignature::TYPE_PATIENT => EsignSignatureCaptureField::class,
            \BaseSignature::TYPE_OTHER_USER => EsignPINField::class
        ];
    }
}