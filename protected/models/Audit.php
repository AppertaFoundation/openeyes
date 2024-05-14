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

/**
 * This is the model class for table "audit".
 *
 * The following are the available columns in table 'audit':
 *
 * @property int $id
 * @property string $target_type
 * @property int $patient_id
 * @property int $episode_id
 * @property int $event_id
 * @property int $user_id
 * @property string $data
 * @property string $remote_addr
 * @property string $http_user_agent
 * @property string $server_name
 * @property string $request_uri
 * @property int $site_id
 * @property int $firm_id
 *
 * The following are the available model relations:
 * @property AuditAction $action
 * @property Patient $patient
 * @property Episode $episode
 * @property Event $event
 * @property User $user
 * @property Site $site
 * @property Firm $firm
 */
class Audit extends BaseActiveRecord
{
    public $count;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return Audit the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'audit';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('action_id,type_id', 'required'),
            // array('name', 'length', 'max'=>255),
            array('id,action,target_type,patient_id,episode_id,event_id,user_id,data,remote_addr,event_type_id,http_user_agent,server_name,request_uri,site_id,firm_id', 'safe', 'on' => 'search'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        # patient, episode, event, user
        return array(
            'patient' => array(self::BELONGS_TO, 'Patient', 'patient_id'),
            'episode' => array(self::BELONGS_TO, 'Episode', 'episode_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'user' => array(self::BELONGS_TO, 'User', 'user_id'),
            'site' => array(self::BELONGS_TO, 'Site', 'site_id'),
            'firm' => array(self::BELONGS_TO, 'Firm', 'firm_id'),
            'event_type' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
            'action' => array(self::BELONGS_TO, 'AuditAction', 'action_id'),
            'target_type' => array(self::BELONGS_TO, 'AuditType', 'type_id'),
            'ip_addr' => array(self::BELONGS_TO, 'AuditIPAddr', 'ipaddr_id'),
            'server' => array(self::BELONGS_TO, 'AuditServer', 'server_id'),
            'user_agent' => array(self::BELONGS_TO, 'AuditUseragent', 'useragent_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'action' => 'Action',
            'target_type' => 'Target type',
            'patient_id' => 'Patient',
            'episode_id' => 'Episode',
            'event_id' => 'Event',
            'user_id' => 'User',
            'data' => 'Data',
            'remote_addr' => 'Remote address',
            'http_user_agent' => 'HTTP User Agent',
            'server_name' => 'Server name',
            'request_uri' => 'Request URI',
            'site_id' => 'Site',
            'firm_id' => 'Firm',
            'event_type_id' => 'Event Type'
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function save($runValidation = true, $attributes = null, $allow_overriding = false)
    {
        if (isset($_SERVER['REMOTE_ADDR'])) {
            if (!$ipaddr = AuditIPAddr::model()->find('name=?', array($_SERVER['REMOTE_ADDR']))) {
                $ipaddr = new AuditIPAddr();
                $ipaddr->name = $_SERVER['REMOTE_ADDR'];
                if (!$ipaddr->save()) {
                    throw new Exception('Unable to save audit IP address: '.print_r($ipaddr->getErrors(), true));
                }
            }

            if (isset($_SERVER['HTTP_USER_AGENT'])) {
                if (!$useragent = AuditUseragent::model()->find('name=?', array($_SERVER['HTTP_USER_AGENT']))) {
                    $useragent = new AuditUseragent();
                    $useragent->name = $_SERVER['HTTP_USER_AGENT'];
                    if (!$useragent->save()) {
                        throw new Exception('Unable to save user agent: '.print_r($useragent->getErrors(), true));
                    }
                }
                $this->useragent_id = $useragent->id;
            }

            if (!$server = AuditServer::model()->find('name=?', array($_SERVER['SERVER_NAME']))) {
                $server = new AuditServer();
                $server->name = $_SERVER['SERVER_NAME'];
                if (!$server->save()) {
                    throw new Exception('Unable to save server: '.print_r($server->getErrors(), true));
                }
            }

            $this->ipaddr_id = $ipaddr->id;
            $this->server_id = $server->id;
            $this->request_uri = substr($_SERVER['REQUEST_URI'], 0, 255);

            if ($this->user) {
                $this->site_id = Yii::app()->session['selected_site_id'];
                $this->firm_id = Yii::app()->session['selected_firm_id'];
            }
        }

        return parent::save($runValidation, $attributes, $allow_overriding);
    }

    public function getColour()
    {
        if ($this->action) {
            switch ($this->action->name) {
                case 'login-successful':
                case 'delete-approved':
                    return 'success';
                    break;
                case 'create-failed':
                    return 'warn';
                    break;
                case 'login-failed':
                case 'search-error':
                    return 'fail';
                    break;
            }
        }
    }

    /**
     * Adding audit log
     *
     * @param $target
     * @param $action
     * @param null $data
     * @param null $log_message
     * @param array $properties
     * @return Audit
     * @throws Exception
     */
    public static function add($target, $action, $data = null, $log_message = null, $properties = array())
    {
        if (!$_target = AuditType::model()->find('name=?', array($target))) {
            $_target = new AuditType();
            $_target->name = $target;
            if (!$_target->save()) {
                throw new Exception('Unable to save audit target: '.print_r($_target->getErrors(), true));
            }
        }

        if (!$_action = AuditAction::model()->find('name=?', array($action))) {
            $_action = new AuditAction();
            $_action->name = $action;
            if (!$_action->save()) {
                throw new Exception('Unable to save audit action: '.print_r($_action->getErrors(), true));
            }
        }

        $audit = new self();
        $audit->type_id = $_target->id;
        $audit->action_id = $_action->id;
        $audit->data = $data;

        if (!isset($properties['user_id'])) {
            if (Yii::app()->session['user']) {
                $properties['user_id'] = Yii::app()->session['user']->id;
            }
        }

        if (isset($properties['module'])) {
            if ($et = EventType::model()->find('class_name=?', array($properties['module']))) {
                $properties['event_type_id'] = $et->id;
            } else {
                if (!$module = AuditModule::model()->find('name=?', array($properties['module']))) {
                    $module = new AuditModule();
                    $module->name = $properties['module'];
                    if (!$module->save()) {
                        throw new Exception('Unable to create audit_module: '.print_r($module->getErrors(), true));
                    }
                }
                $properties['module_id'] = $module->id;
            }

            unset($properties['module']);
        }

        if (isset($properties['model'])) {
            if (!$model = AuditModel::model()->find('name=?', array($properties['model']))) {
                $model = new AuditModel();
                $model->name = $properties['model'];
                if (!$model->save()) {
                    throw new Exception('Unable to save audit_model: '.print_r($model->getErrors(), true));
                }
            }
            $properties['model_id'] = $model->id;
            unset($properties['model']);
        }

        foreach ($properties as $key => $value) {
            $audit->{$key} = $value;
        }

        if (!$audit->save()) {
            throw new Exception('Failed to save audit entry: '.print_r($audit->getErrors(), true));
        }

        if (isset($properties['user_id'])) {
            $username = User::model()->findByPk($properties['user_id'])->username;
        }

        $log_message && OELog::log($log_message, @$username);

        return $audit;
    }
}
