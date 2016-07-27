<?php namespace OEModule\OphCoCvi\components;

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class OphCoCvi_AuthRules
{
    protected $yii;

    public function __construct(\CApplication $yii = null)
    {
        if (is_null($yii)) {
            $yii = \Yii::app();
        }

        $this->yii = $yii;
    }

    /**
     * Root permission checking function for edit ability
     *
     * @param $user_id
     * @param bool $clerical
     * @return bool
     */
    private function canEdit($user_id, $clerical = false)
    {
        if ($this->yii->authManager->checkAccess('admin', $user_id)) {
            return true;
        }
        \OELog::log("something weird");
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

        \OELog::log('say whhaaaat?');
        return false;
    }

    /**
     * @param $user_id
     * @return bool
     */
    public function canCreateOphCoCvi($user_id)
    {
        return $this->canEdit($user_id, true);
    }

    /**
     * @param $user_id
     * @return bool
     */
    public function canEditOphCoCvi($user_id)
    {
        return $this->canCreateOphCoCvi($user_id);
    }

    /**
     * @param $user_id
     * @return bool
     */
    public function canEditClinicalOphCoCvi($user_id)
    {
        return $this->canEdit($user_id, false);
    }
}