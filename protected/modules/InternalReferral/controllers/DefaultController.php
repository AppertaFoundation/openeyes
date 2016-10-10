<?php
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

namespace OEModule\Internalreferral\controllers;

class DefaultController extends \BaseEventTypeController
{
    // default behaviour as to whether a referral should be editable.
    static protected $ALLOW_EDIT = false;

    /**
     * Set up the default values on create.
     *
     * @param $element
     * @param $action
     */
    public function setElementDefaultOptions_Element_Internalreferral_ReferralDetails($element, $action)
    {
        if ($action == 'create') {
            $user = \User::model()->findByPk(\Yii::app()->user->id);
            if ($user->is_consultant) {
                $element->referrer_id = \Yii::app()->user->id;
            }
            $element->from_subspecialty_id = $this->firm->getSubspecialtyID();
        }
    }

    /**
     * Typically don't want to allow edits when internal referral is integrated
     * with a 3rd party system.
     *
     * @return bool
     */
    public function checkEditAccess()
    {
        if (isset($this->getApp()->params['internalreferral_allowedit'])) {
            return $this->getApp()->params['internalreferral_allowedit'] && parent::checkEditAccess();
        }

        return self::$ALLOW_EDIT && parent::checkEditAccess();
    }

    /**
     * Partial render to insert values into event view.
     */
    public function renderIntegration()
    {
        if ($component = $this->getApp()->internalReferralIntegration) {
            echo $component->renderEventView($this->event);
        }
    }

    /**
     * Handle external application call for a referral update.
     *
     * @throws \CHttpException
     */
    public function actionExternalReferralResponse()
    {
        if ($component = $this->getApp()->internalReferralIntegration) {
            if ($this->getApp()->request->isPostRequest) {
                list($status_code, $response) = $component->processExternalResponse($_POST);
            } else {
                list($status_code, $response) = $component->processExternalResponse($_GET);
            }
            header('HTTP/1.1 ' . $status_code);
            echo $response;
            $this->getApp()->end();
        }
        throw new \CHttpException(404, 'External Integration Not Configured.');
    }
    
    /**
     * Extending the parent function to set up session variable to open popup window on first view page visit
     * @param type $event
     */
    protected function afterCreateElements($event)
    {
        $this->getApp()->user->setState("new_referral", true);
        parent::afterCreateElements($event);
    }
}
