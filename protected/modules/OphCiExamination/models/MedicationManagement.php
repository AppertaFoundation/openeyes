<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2018, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\models;

/**
 * This is the model class for table "et_ophciexamination_medicationmanagement".
 *
 * The followings are the available columns in table 'et_ophciexamination_medicationmanagement':
 * @property integer $id
 * @property string $event_id
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property \Event $event
 * @property \User $createdUser
 * @property \User $lastModifiedUser
 */
class MedicationManagement extends BaseMedicationElement
{
    public $do_not_save_entries = false;

    public $widgetClass = 'OEModule\OphCiExamination\widgets\MedicationManagement';

    public static $entry_class = MedicationManagementEntry::class;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'et_ophciexamination_medicationmanagement';
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'event_id' => 'Event',
			'last_modified_user_id' => 'Last Modified User',
			'last_modified_date' => 'Last Modified Date',
			'created_user_id' => 'Created User',
			'created_date' => 'Created Date',
		);
	}

    public function relations()
    {
        return array(
            'event' => array(self::BELONGS_TO, \Event::class, 'event_id'),
            'user' => array(self::BELONGS_TO, \User::class, 'created_user_id'),
            'usermodified' => array(self::BELONGS_TO, \User::class, 'last_modified_user_id'),
            'entries' => array(
                self::HAS_MANY,
                MedicationManagementEntry::class,
                array('element_id' => 'id'),
                'order' => 'entries.start_date DESC, entries.end_date DESC'
            ),
        );
    }

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return MedicationManagement the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function getContainer_form_view()
    {
        return false;
    }

    public function getContainer_update_view()
    {
        return '//patient/element_container_form';
    }

    public function getContainer_create_view()
    {
        return '//patient/element_container_form';
    }

    protected function saveEntries()
    {
        $criteria = new \CDbCriteria();
        $criteria->addCondition("element_id = :element_id");
        $criteria->params['element_id'] = $this->id;
        $orig_entries = MedicationManagementEntry::model()->findAll($criteria);
        $saved_ids = array();
        foreach ($this->entries as $entry) {
            /** @var MedicationManagementEntry $entry */
            $entry->element_id = $this->id;
            if(!$entry->save()) {
                foreach ($entry->errors as $err) {
                    $this->addError('entries', implode(', ', $err));
                }
                return false;
            }
            $saved_ids[] = $entry->id;
        }
        foreach ($orig_entries as $entry) {
            if(!in_array($entry->id, $saved_ids)) {
                $entry->delete();
            }
        }
        if(count($this->entries_to_prescribe) > 0) {
            $this->generatePrescriptionEvent();
        }
        return true;
    }
}
