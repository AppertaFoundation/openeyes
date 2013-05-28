<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

abstract class MultiActiveRecord extends CActiveRecord
{
    /**
     * @var CDbConnection the default database connection for all active record classes.
     * By default, this is the 'db' application component.
     * @see getDbConnection
     */
    public static $db;
    /**
     * Returns the database connection used by active record.
     * By default, the "db" application component is used as the database connection.
     * If you override the method connectionId it will use this connection.
     * 
     * @return CDbConnection the database connection used by active record.
     */
    public function getDbConnection()
    {
        $dbName=$this->connectionId();
        
        if(!isset(self::$db[$dbName])){
            if(Yii::app()->hasComponent($dbName) && (self::$db[$dbName]=Yii::app()->getComponent($dbName)) instanceof CDbConnection){
    			self::$db[$dbName]->setActive(true);
            }else
                throw new CDbException(Yii::t('yii','Active Record requires a "'.$dbName.'" CDbConnection application component.'));
        }
        
        return self::$db[$dbName];
    }
    /**
     * workaround to try the model's name, if not given
     * doesnt always work, and thats the reason its not included in the framework's core 
     * 
     * @param string $className
     * @return CModel
     */
    public static function model($className=__CLASS__){
        if($className===__CLASS__){
            if(version_compare(PHP_VERSION,'5.3',">"))
                $className=get_called_class();
            else
                throw new CException("You must define a static function 'model' in your models");
        }
        return parent::model($className);
    }
    /**
     * try to guess the model's name, models should override this function to improve speed and better customization 
     * it does the inverse process of gii's model creator
     *  
     * @return string name of the class table
     */
    public function tableName(){
        $tableName=get_class($this);
        $tableName=strtolower(substr($tableName,0,1)).substr($tableName,1);
        $tableName=preg_replace_callback('/([A-Z])/',create_function('$matches','return "_".strtolower($matches[0]);'),$tableName);
        return $tableName;
    }
    /**
     * define which connection to use in the model, default to 'db'
     * 
     * @return string
     */
    public function connectionId(){
        return 'db';
    }
}
