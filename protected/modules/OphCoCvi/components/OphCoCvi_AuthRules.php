<?php
/**
 * (C) Copyright Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
namespace OEModule\OphCoCvi\components;

/**
 * Class OphCoCvi_AuthRules
 *
 * @package OEModule\OphCoCvi\components
 */
class OphCoCvi_AuthRules
{
    protected $yii;

    /**
     * OphCoCvi_AuthRules constructor.
     *
     * @param \CApplication|null $yii
     */
    public function __construct(\CApplication $yii = null)
    {
        if (is_null($yii)) {
            $yii = \Yii::app();
        }

        $this->yii = $yii;
    }

    /**
     * @var \EventType
     */
    protected $event_type;

    /**
     * @return \EventType
     */
    protected function getEventType()
    {
        if (!isset($this->event_type)) {
            $this->event_type = \EventType::model()->findByAttributes(array('class_name' => 'OphCoCvi'));
        }

        return $this->event_type;
    }

    /**
     * Root permission checking function for edit ability
     *
     * @param      $user_id
     * @param bool $clerical
     * @return bool
     */
    private function canEdit($user_id, $clerical = false)
    {
        if ($this->yii->authManager->checkAccess('admin', $user_id)) {
            return true;
        }

        if ($this->yii->params['ophcocvi_allow_all_consultants']) {
            $user = \User::model()->findByPk($user_id);
            if ($user->is_consultant) {
                return true;
            }
        }

        if ($this->yii->authManager->checkAccess('OprnEditClinicalCviExplicit', $user_id) ||
            ($clerical && $this->yii->authManager->checkAccess('OprnEditClericalCvi', $user_id))
        ) {
            return true;
        }

        return false;
    }

    /**
     * Biz rule for RBAC. if only the user id is provided, determines whether the user for the id has permissions for creating a CVI event.
     * If the view context (containing a Firm and Episode) is provided, checks whether the context is correct for creating a CVI event in the episode.
     *
     * @param       $user_id
     * @param array $view_context
     * @return bool
     */
    public function canCreateOphCoCvi($data, $user_id, $view_context = array())
    {
        if ($this->canEdit($user_id, true)) {
            if (isset($view_context['firm'])) {
                return $this->yii->getAuthManager()->executeBizRule(
                    'canCreateEvent',
                    array($view_context['firm'], $view_context['episode'], $this->getEventType()),
                    null
                );
            } else {
                return true;
            }
        }

        return false;
    }

    /**
     * Biz rule for RBAC. if only the user id is provided, determines whether the user for the id has permissions for editing a CVI event.
     * If the view context (containing a Firm and Event) is provided, checks whether the context is correct for editing the given CVI Event object.
     * NB Event checks are purely about view context, and does not account for business rules around the status of the event.
     *
     * @param       $user_id
     * @param array $view_context array containing the currently selected firm and Event for editing
     * @return bool
     */
    public function canEditOphCoCvi($data, $user_id, $view_context = array())
    {
        if ($this->canCreateOphCoCvi(null, $user_id)) {
            if (isset($view_context['firm'])) {
                return $this->yii->getAuthManager()->executeBizRule(
                    'canEditEvent',
                    array($view_context['event']),
                    null
                );
            } else {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $user_id
     * @return bool
     */
    public function canEditClinicalOphCoCvi($data, $user_id)
    {
        return $this->canEdit($user_id, false);
    }
}
