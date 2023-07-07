<?php
/**
 * OpenEyes.
 *
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
 * @copyright Copyright 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class OEWebUser extends CWebUser
{

    private $session_id_to_invalidate;
    
    protected function changeIdentity($id, $name, $states)
    {
        //force regeneration of session id to avoid bug in CWebUser where empty phpsessionid will not be regenerated
        session_regenerate_id(true);
        parent::changeIdentity($id, $name, $states);
    }

    /**
     * Initializes the application component.
     * This method overrides the parent implementation by checking whether
     * current authentication status is incorrect - this is to avoid bug in CWebUser where
     * sometimes logged out user is accepted as authenticated user
     */
    public function init()
    {
        parent::init();
        if(!$this->getIsGuest() && !$this->isValidSession()) {
            OELog::log("User logged out due to invalid session");
            $this->logout(false);
        }
    }

    private function createInvalidatedIDfromID($id) {
        return '_' . substr($id, 1);
    }

    private function isValidSession() {
        $session_table_name = Yii::app()->components['session']->sessionTableName;
        $invalidated_id = Yii::app()->db->createCommand()
            ->select('id')
            ->from($session_table_name)
            ->where('id = :id')
            ->bindValue(':id', $this->createInvalidatedIDfromID(session_id()))
            ->queryScalar();

        return $invalidated_id === false;
    }

    private function invalidateSession() {
        if($this->session_id_to_invalidate) {
            $session_table_name = Yii::app()->components['session']->sessionTableName;
            $invalidated_id = $this->createInvalidatedIDfromID($this->session_id_to_invalidate);
            $expire = time() + 3600 * 48; // 2 days
            $query = <<<EOD
insert into $session_table_name (id, expire, data) values
(:invalid_id, :expire, 'Logged out.') on duplicate key update expire = :expire;
EOD;
            Yii::app()->db->createCommand($query)
            ->bindValue(':invalid_id', $invalidated_id)
            ->bindValue(':expire', $expire)
            ->execute();
        }
    }

    /**
     * This method overrides the parent implementation by saving session id
     * to make sure it can be invalidated after logout
     */
    protected function beforeLogout()
    {
        $this->session_id_to_invalidate = session_id();
        return parent::beforeLogout();
    }

    /**
     * This method overrides the parent implementation by throwing exception
     * if the request is an eventImage request, instead of changing returnUrl
     * because we don't want returnUrl to be changed in this case
     */
    public function loginRequired()
    {
        $app=Yii::app();
        $request=$app->getRequest();
        $url = $request->getUrl();

        if(strpos($url, '/eventImage') !== false) {
            // although this request might be not an Ajax Request, we do not want returnUrl to be changed
            throw new CHttpException(403,Yii::t('yii','Login Required'));
        }
        parent::loginRequired();
    }

    /**
     * This method overrides the parent implementation by invalidating the session
     * to make sure the user will not be able to log in with the same session id again
     */
    protected function afterLogout()
    {
        parent::afterLogout();
        $this->invalidateSession();
    }

    /**
     * Is the current user a surgeon.
     *
     * @return bool
     */
    public function isSurgeon()
    {
        $user = User::model()->findByPk($this->getId());
        if ($user) {
            return (bool) $user->is_surgeon;
        } else {
            return false;
        }
    }

    /*Get the roles of current user*/
    public function getRole($id){
        $roles = array();
        $query = "SELECT itemname FROM authassignment
                  WHERE userid = $id;";
        $command = Yii::app()->db->createCommand($query);
        $command->prepare();
        $result = $command->queryAll();
        foreach ($result as $item=>$value)
        {
            array_push($roles, $value['itemname']);
        }
        return $roles;
    }
}
