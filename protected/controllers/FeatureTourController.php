<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
 * version. OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details. You should have received a copy of the GNU General Public License along with OpenEyes in a file titled
 * COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class FeatureTourController extends BaseController
{

    public function accessRules()
    {
        return array(array('allow', 'roles' => array('User')));
    }

    protected function sendResponse($status = 200)
    {
        header('HTTP/1.1 '.$status);
        $this->getApp()->end();
    }

    /**
     * @param $id
     * @return mixed
     */
    protected function getUserTour($id)
    {
        return UserFeatureTourState::model()->findOrCreate($this->getApp()->user->id, $id);
    }

    /**
     * @param $id
     * @throws CHttpException
     */
    public function actionComplete($id)
    {
        if (!$this->getApp()->request->isPostRequest) {
            throw new CHttpException(400);
        }

        $user_tour = $this->getUserTour($id);
        $user_tour->completed = true;
        $user_tour->save();

        $this->sendResponse();
    }

    /**
     * @param $period
     * @return string
     */
    protected function calculateDateFromPeriod($period)
    {
        if ($period === '-1') {
            return '9999-12-31 23:59:59';
        }
        $interval = DateInterval::createFromDateString($period);
        $now = new DateTime();
        $today = $now->format('d');
        $until_day = $now->add($interval)->format('d');
        // if the sleep date is another day, we don't want to wait until the exact time,
        // so we null out the time portion of the datetime
        if ($until_day === $today) {
            return $now->format('Y-m-d H:i:s');
        } else {
            return $now->format('Y-m-d 00:00:00');
        }
    }

    /**
     * @param $id
     * @throws CHttpException
     */
    public function actionSleep($id)
    {
        if (!$this->getApp()->request->isPostRequest) {
            throw new CHttpException(400);
        }

        $user_tour = $this->getUserTour($id);
        $user_tour->sleep_until = $this->calculateDateFromPeriod($_POST['period']);
        $user_tour->save();

        $this->sendResponse();
    }
}
