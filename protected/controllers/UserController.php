<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class UserController extends BaseController
{
    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('autoComplete', 'surgeonGrade'),
                'roles' => array('OprnViewClinical'),
            ),
            array('allow',
                'actions' => array('testAuthenticated', 'getSecondsUntilSessionExpire'),
                'users' => array('@'),
            ),
        );
    }

    public function actionAutoComplete($term, $consultant_only = false)
    {
        $res = array();
        if (\Yii::app()->request->isAjaxRequest && !empty($term)) {
            $term = strtolower($term);

            $criteria = new \CDbCriteria;
            $criteria->join = 'JOIN user_authentication user_auth ON t.id = user_auth.user_id';
            $criteria->compare("LOWER(user_auth.username)", $term, true, 'OR');
            $criteria->compare("LOWER(first_name)", $term, true, 'OR');
            $criteria->compare("LOWER(last_name)", $term, true, 'OR');
            $words = explode(" ", $term);
            if (count($words) > 1) {
                // possibly slightly verbose approach to checking first and last name combinations
                // for searches
                $first_criteria = new \CDbCriteria();
                $first_criteria->compare("LOWER(first_name)", $words[0], true);
                $first_criteria->compare("LOWER(last_name)", implode(" ", array_slice($words, 1, count($words) - 1)), true);
                $last_criteria = new \CDbCriteria();
                $last_criteria->compare("LOWER(first_name)", $words[count($words) - 1], true);
                $last_criteria->compare("LOWER(last_name)", implode(" ", array_slice($words, 0, count($words) - 2)), true);
                $first_criteria->mergeWith($last_criteria, 'OR');
                $criteria->mergeWith($first_criteria, 'OR');
            }

            if ($consultant_only) {
                $criteria->compare("is_consultant", true);
            }
            $criteria->compare('user_auth.active', true);

            foreach (\User::model()->findAll($criteria) as $user) {
                $res[] = $user->getUserPermissionDetails();
            }
        }
        echo \CJSON::encode($res);
    }


    public function actionSurgeonGrade($id)
    {
        $user = User::model()->with('grade')->findByPk($id);

        $this->renderJSON(array(
            'id' => $user->doctor_grade_id,
            'grade' => ($user->grade) ? $user->grade->grade : "None selected.",
            'pcr_risk' => $user->grade->pcr_risk_value ?? null,
        ));
    }

    public function actionTestAuthenticated()
    {
        //If the user is not authenticated, the request will be blocked and this will not be returned
        $this->renderJSON('Success');
    }

    public function actionGetSecondsUntilSessionExpire()
    {
        $expire = Yii::app()->db->createCommand()
            ->select('expire')
            ->from('user_session')
            ->where('id=:id', array(':id' => $_COOKIE[session_name()]))
            ->queryRow();

        $seconds_to_expire = $expire['expire'] - time();

        $this->renderJSON($seconds_to_expire);
    }

}
