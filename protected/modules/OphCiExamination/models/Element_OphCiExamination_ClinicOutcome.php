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

namespace OEModule\OphCiExamination\models;

use OEModule\PatientTicketing\models\TicketQueueAssignment;
use Yii;

/**
 * This is the model class for table "et_ophciexamination_clinicoutcome".
 *
 * The followings are the available columns in table:
 *
 * @property int $id
 * @property \Event $event
 * @property string $comments
 * @property ClinicOutcomeEntry[] $entries
 *
 */
class Element_OphCiExamination_ClinicOutcome extends \BaseEventTypeElement
{
    use traits\CustomOrdering;
    protected $auto_update_relations = true;
    protected $auto_validate_relations = true;

    const FOLLOWUP_Q_MIN = 1;
    const FOLLOWUP_Q_MAX = 18;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return Element_OphCiExamination_ClinicOutcome the static model class
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
        return 'et_ophciexamination_clinicoutcome';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
                ['comments, entries, event_id, id', 'safe'],
                ['entries', 'required'],
                ['id, event_id, comments', 'safe', 'on' => 'search'],
        ];
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'entries' => array(self::HAS_MANY, 'OEModule\OphCiExamination\models\ClinicOutcomeEntry', 'element_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
                'id' => 'ID',
                'event_id' => 'Event',
                'comments' => 'Comments',
        ];
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return \CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new \CDbCriteria();

        $criteria->compare('id', $this->id, true);
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('comments', $this->comments);

        return new \CActiveDataProvider(get_class($this), array(
                'criteria' => $criteria,
        ));
    }

    public function afterSave()
    {
        // Update Episode status when outcome is saved
        if ($this->entries && $this->entries[0]->status) {
            if ($this->event->isLatestOfTypeInEpisode()) {
                $this->event->episode->episode_status_id = $this->entries[0]->status->episode_status_id;
                if (!$this->event->episode->save()) {
                    throw new Exception('Unable to save episode status: '.print_r($this->event->episode->getErrors(), true));
                }
            }
        }
        parent::afterSave();
    }

    public function afterDelete()
    {
        $ticket = $this->getPatientTicket();
        if ($ticket) {
            $this->deleteRelatedTicket($ticket);
        }
        parent::afterDelete();
    }

    /**
     * Delete the patient ticket when the examination is deleted
     */
    public function softDelete()
    {
        $ticket = $this->getPatientTicket();
        if ($ticket) {
            $this->deleteRelatedTicket($ticket);
        }
        parent::softDelete();
    }

    public function getFollowUpQuantityOptions()
    {
        $opts = array();
        for ($i = self::FOLLOWUP_Q_MIN; $i <= self::FOLLOWUP_Q_MAX; ++$i) {
            $opts[(string) $i] = $i;
        }

        return $opts;
    }

    /**
     * If the PatientTicketing module is installed, will use API to get Patient Ticket for this element event (if one exists).
     *
     * @return mixed
     */
    public function getPatientTicket()
    {
        if ($this->event && $this->event->id && $api = Yii::app()->moduleAPI->get('PatientTicketing')) {
            return $api->getTicketForEvent($this->event);
        }
    }

    /**
     * Will determine the queue options for the given firm.
     *
     * @param $firm
     *
     * @return array
     */
    public function getPatientTicketQueues($firm, $patient)
    {
        if ($api = Yii::app()->moduleAPI->get('PatientTicketing')) {
            return $api->getQueueSetList($firm, $patient);
        }

        return array();
    }

    public function deleteRelatedTicket($ticket)
    {
        TicketQueueAssignment::model()->deleteAllByAttributes(array('ticket_id' => $ticket->id));
        $ticket->delete();
    }

    public function checkIfTicketEntryExists($status_id)
    {
        foreach ($this->entries as $entry) {
            if ($entry->isPatientTicket() && $entry->status_id === $status_id) {
                return true;
            }
        }

        return false;
    }
}
