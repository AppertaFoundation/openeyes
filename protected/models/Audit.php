<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * This is the model class for table "audit".
 *
 * The following are the available columns in table 'audit':
 * @property string $id
 * @property string $action
 * @property string $target_type
 * @property string $patient_id
 * @property string $episode_id
 * @property string $event_id
 * @property string $user_id
 * @property string $data
 * @property string $remote_addr
 * @property string $http_user_agent
 * @property string $server_name
 * @property string $request_uri
 * @property string $site_id
 * @property string $firm_id
 *
 * The following are the available model relations:
 * @property Patient[] $patient
 * @property Episode[] $episode
 * @property Event[] $event
 * @property User[] $user
 * @property Site[] $site
 * @property Firm[] $firm
 */
class Audit extends BaseActiveRecord
{
	public $count;

	/**
	 * Returns the static model of the specified AR class.
	 * @return Theatre the static model class
	 */
	public static function model($className=__CLASS__)
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
			array('action,target_type', 'required'),
			// array('name', 'length', 'max'=>255),
			array('id,action,target_type,patient_id,episode_id,event_id,user_id,data,remote_addr,http_user_agent,server_name,request_uri,site_id,firm_id', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	public function save($runValidation=true, $attributes=null, $allow_overriding=false) {
		if(isset($_SERVER['REMOTE_ADDR'])) {
			$this->remote_addr = $_SERVER['REMOTE_ADDR'];
			$this->http_user_agent = @$_SERVER['HTTP_USER_AGENT'];
			$this->server_name = $_SERVER['SERVER_NAME'];
			$this->request_uri = $_SERVER['REQUEST_URI'];
			if ($this->user) {
				$this->site_id = Yii::app()->session['selected_site_id'];
				$this->firm_id = Yii::app()->session['selected_firm_id'];
			}
		}
		parent::save($runValidation, $attributes, $allow_overriding);
	}

	public function getColour() {
		switch ($this->action) {
			case 'login-successful':
				return 'Green';
				break;
			case 'login-failed':
			case 'search-error':
				return 'Red';
				break;
		}
	}

	public static function add($target, $action, $data=null, $log=false, $properties=array()) {
		$audit = new Audit;
		$audit->target_type = $target;
		$audit->action = $action;
		$audit->data = $data;

		if (!isset($properties['user_id'])) {
			if (Yii::app()->session['user']) {
				$properties['user_id'] = Yii::app()->session['user']->id;
			}
		}

		foreach ($properties as $key => $value) {
			$audit->{$key} = $value;
		}

		$audit->save();

		if (isset($properties['user_id'])) {
			$username = User::model()->findByPk($properties['user_id'])->username;
		}

		$log && OELog::log($data,@$username);

		return $audit;
	}
}
