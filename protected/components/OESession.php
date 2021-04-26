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
class OESession extends CDbHttpSession
{
    //Note to any future developers: OELog does not work reliably in this function. Use error_log() instead and (most likely) find logs in /var/logs/apache2/error.log
    public function writeSession($id, $data)
    {
        if ($id == '') {
            //prevent the saving of blank ids into the user session table
            return false;
        } else {
            //Default to extending the session to not break existing behaviour
            $extend_session = !array_key_exists('extend_session', $_GET) || $_GET['extend_session'] === 'true';

            if(!$extend_session)
            {
                return $this->writeSessionWithoutExtending($id, $data);
            }else{
                return parent::writeSession($id, $data);
            }
        }
    }

    //Needed to patch Yii for desired functionality.
    //The main change is that on a session update, the expire field will not be updated
    //THIS WILL NEED TO BE CHANGED IF UPDATING TO YII 2
    public function writeSessionWithoutExtending($id, $data)
    {
        // exception must be caught in session write handler
        // http://us.php.net/manual/en/function.session-set-save-handler.php
        try
        {
            $expire=time()+$this->getTimeout();
            $db=$this->getDbConnection();
            if($db->getDriverName()=='pgsql')
                $data=new CDbExpression($db->quoteValueWithType($data, PDO::PARAM_LOB)."::bytea");
            if($db->getDriverName()=='sqlsrv' || $db->getDriverName()=='mssql' || $db->getDriverName()=='dblib')
                $data=new CDbExpression('CONVERT(VARBINARY(MAX), '.$db->quoteValue($data).')');
            if($db->createCommand()->select('id')->from($this->sessionTableName)->where('id=:id',array(':id'=>$id))->queryScalar()===false)
                $db->createCommand()->insert($this->sessionTableName,array(
                    'id'=>$id,
                    'data'=>$data,
                    'expire'=>$expire,
                ));
            else
                $db->createCommand()->update($this->sessionTableName,array(
                    'data'=>$data,
                ),'id=:id',array(':id'=>$id));
        }
        catch(Exception $e)
        {
            if(YII_DEBUG)
                echo $e->getMessage();
            // it is too late to log an error message here
            return false;
        }
        return true;
    }
}
