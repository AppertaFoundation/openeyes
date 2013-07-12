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

/**
 * This is the model class for table "event".
 *
 * The followings are the available columns in table 'event':
 * @property string $id
 * @property string $episode_id
 * @property string $user_id
 * @property string $event_type_id
 * @property string $datetime
 *
 * The followings are the available model relations:
 * @property Episode $episode
 * @property User $user
 * @property EventType $eventType
 */
class Event extends BaseActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Event the static model class
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
		return 'event';
	}

	/**
	 * Sets default scope for events such that we never pull back any rows that have deleted set to 1
	 * @return array of mandatory conditions
	 */

	public function defaultScope()
	{
		$table_alias = $this->getTableAlias(false,false);
		return array(
			'condition' => $table_alias.'.deleted = 0',
		);
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('event_type_id', 'required'),
			array('episode_id, event_type_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, episode_id, event_type_id, datetime, created_date', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'episode' => array(self::BELONGS_TO, 'Episode', 'episode_id'),
			'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
			'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
			'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
			'issues' => array(self::HAS_MANY, 'EventIssue', 'event_id'),
		);
	}

	public function getEditable()
	{
		if (!$this->episode->editable) {
			return FALSE;
		}

		if ($this->episode->patient->date_of_death) {
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'episode_id' => 'Episode',
			'created_user_id' => 'User',
			'event_type_id' => 'Event Type',
			'datetime' => 'Datetime',
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
		$criteria->compare('episode_id',$this->episode_id,true);
		$criteria->compare('created_user_id',$this->created_user_id,true);
		$criteria->compare('event_type_id',$this->event_type_id,true);
		$criteria->compare('datetime',$this->datetime,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	/* Does this event have some kind of issue that the user should know about */

	public function hasIssue()
	{
		return (boolean) $this->issues;
	}

	public function getIssueText()
	{
		$text = '';

		foreach ($this->issues as $issue) {
			$text .= $this->expandIssueText($issue->text)."\n";
		}

		return $text;
	}

	public function expandIssueText($text)
	{
		if (preg_match_all('/\{(.*?)\}/',$text,$m)) {
			foreach ($m[0] as $i => $match) {
				if (preg_match('/^\{(.*?)\+([0-9]+)H:(.*)\}/',$match,$n)) {
					$field = $n[1];
					$hours = $n[2];
					$dateformat = $n[3];

					$text = str_replace($match,date($dateformat,strtotime($this->{$field}) + 3600 * $hours),$text);
				} elseif ($this->hasProperty($m[1][$i])) {
					$text = str_replace($match,$this->{$m[1][$i]},$text);
				}
			}
		}

		return $text;
	}

	public function getInfoText()
	{
		foreach (Yii::app()->getController()->getDefaultElements('view',false,$this) as $element) {
			if ($element->getInfoText()) {
				return $element->getInfoText();
			}
		}
	}

	public function addIssue($text)
	{
		if (!$issue = Issue::model()->find('name=?',array($text))) {
			$issue = new Issue;
			$issue->name = $text;
			if (!$issue->save()) {
				return false;
			}
		}

		if (!EventIssue::model()->find('event_id=? and issue_id=?',array($this->id,$issue->id))) {
			$ei = new EventIssue;
			$ei->event_id = $this->id;
			$ei->issue_id = $issue->id;

			if (!$ei->save()) {
				return false;
			}
		}

		return true;
	}

	public function deleteIssue($name)
	{
		if (!$issue = Issue::model()->find('name=?',array($name))) {
			return false;
		}

		foreach (EventIssue::model()->findAll('event_id=? and issue_id = ?',array($this->id, $issue->id)) as $event_issue) {
			$event_issue->delete();
		}

		return true;
	}

	public function deleteIssues()
	{
		foreach (EventIssue::model()->findAll('event_id=?',array($this->id)) as $event_issue) {
			$event_issue->delete();
		}
	}

	// Only the event creator can delete the event, and only 24 hours after its initial creation
	public function canDelete()
	{
		if (!BaseController::checkUserLevel(4)) return false;

		if ($this->episode->patient->date_of_death) return false;

		if (!$this->episode->editable) return false;

		$admin = User::model()->find('username=?',array('admin'));	 // these two lines should be replaced once we have rbac
		if ($admin->id == Yii::app()->session['user']->id) {return true;}
		return ($this->created_user_id == Yii::app()->session['user']->id && (time() - strtotime($this->created_date)) <= 86400);
	}

	public function delete()
	{
		// Delete related
		EventIssue::model()->deleteAll('event_id = ?', array($this->id));

		parent::delete();
	}

	/*
	 * returns the latest event of this type in the event episode
	 *
	 * @returns Event
	 */
	public function getLatestOfTypeInEpisode()
	{
		$criteria = new CDbCriteria;
		$criteria->condition = 'episode_id = :e_id AND event_type_id = :et_id';
		$criteria->limit = 1;
		$criteria->order = 'created_date DESC';
		$criteria->params = array(':e_id'=>$this->episode_id, ':et_id'=>$this->event_type_id);

		return Event::model()->find($criteria);
	}

	/*
	 * if this event is the most recent of its type in its episode, returns true. false otherwise
	 *
	 * @returns boolean
	 */
	public function isLatestOfTypeInEpisode()
	{
		$latest = $this->getLatestOfTypeInEpisode();
		return ($latest->id == $this->id) ? true : false;
	}

	public function audit($target, $action, $data=null, $log=false, $properties=array())
	{
		$properties['event_id'] = $this->id;
		$properties['episode_id'] = $this->episode_id;
		$properties['patient_id'] = $this->episode->patient_id;

		return parent::audit($target, $action, $data, $log, $properties);
	}

}
