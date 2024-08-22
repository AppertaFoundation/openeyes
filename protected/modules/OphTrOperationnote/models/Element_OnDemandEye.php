<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */


/**
 * Class Element_OnDemandEye
 *
 * Base on demand element that needs to know which eye is being operated on (in op note this is derived from the procedure
 * list element). Provides for explicit setting to support the inline loading process.
 */
class Element_OnDemandEye extends Element_OnDemand
{
    /**
     * @var Eye
     */
    protected $eye;

    /**
     * The eye of the procedure is stored in the parent procedure list element.
     * However, it may be set direcly on the element object, to enable encapsulation of the current eye when creating the element
     *
     * @return Eye
     * @throws SystemException
     */
    public function getEye()
    {
        if (!$this->eye) {
            if (!$this->event_id) {
                throw new SystemException('Cannot automatically determine eye with no event attached, eye must be set externally.');
            }
            $this->eye = Element_OphTrOperationnote_ProcedureList::model()->find('event_id=?', array($this->event_id))->eye;
        }
        return $this->eye;
    }

    /**
     * @param Eye $eye
     */
    public function setEye(Eye $eye)
    {
        $this->eye = $eye;
    }

    public function getDefaults(array $context): array
    {
        $eye = null;
        if ($context['action'] === 'create' && $context['booking_procedures']) {
            $api = Yii::app()->moduleAPI->get('OphTrOperationbooking');
            $eye = $api->getEyeForOperation($context['booking']->event_id);
            $this->setEye($eye);
        } elseif ($context['action'] === 'create' && !empty($context['unbooked_eye'])) {
            $eye = $context['unbooked_eye'];
            $this->setEye($eye);
        }
        $fields = array(
            'eye' => $eye,
        );
        return array_merge($fields, parent::getDefaults($context));
    }
}
