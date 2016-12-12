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

class DocumentSet extends BaseActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Site the static model class
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
		return 'document_set';
	}

	public function behaviors()
	{
		return array();
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('event_id', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, event_id', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'created_user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
                        'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
                        'document_instance' => array(self::HAS_MANY, 'DocumentInstance', 'document_set_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
	}


	public function createNew($eventId)
	{
		$ds = new DocumentSet;
		$ds->event_id = $eventId;
		$ds->save();
		return ($ds->id);
	}
	
	// If AUTO, pre-calculates template
	// If MANUAL, takes body text from data[] arrary
	public function addDocument($autoManual, $macroId, $eventId, $target, $outputs, $data=null)
	{
	}

	
	// Need new document version.
	// This will:
	// Copy the current _version record to a new version number
	
// It will also incement 
	public function updateDocument($macroId, $eventId, $target, $outputs, $data=null)
	{
	}

}
