<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class WorklistBehavior extends CBehavior
{
    protected $worklist_manager;

    public function __construct()
    {
        $this->worklist_manager = new WorklistManager();
    }

    public function events()
    {
        return array_merge(parent::events(), [
            'onAfterAction' => 'afterAction',
        ]);
    }

    public function afterAction(\CEvent $event)
    {
        $referrer = \Yii::app()->request->getQuery('referrer', null);
        if($referrer === 'worklist' && $this->owner->patient->id){
            //store the patient ID to use later when we create an event
            $this->worklist_manager->setWorklistPatientId($this->owner->patient->id);
        } else {
            $this->worklist_manager->clearWorklistPatientId();
        }
    }
}

