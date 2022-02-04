<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\PatientTicketing\models;

/**
 * This is the model class for table "patientticketing_ticketqueue_assignment". THis is the link table between tickets and queues.
 *
 * The followings are the available columns in table:
 *
 * @property string $id
 * @property int $ticket_id
 * @property int $queue_id
 * @property datetime $assignment_date
 * @property int $assignment_user_id
 * @property int $assigment_firm_id
 * @property string $notes
 * @property string $details
 * @property int $created_user_id
 * @property datetime $created_date
 * @property int $last_modified_user_id
 * @property datetime $last_modified_date
 *
 * The followings are the available model relations:
 * @property Ticket $ticket
 * @property Queue $queue
 * @property \User $assignment_user
 * @property \Firm $assignment_firm
 * @property \User $user
 * @property \User $usermodified
 */
class TicketQueueAssignment extends \BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return OphTrOperationnote_GlaucomaTube_PlatePosition the static model class
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
        return 'patientticketing_ticketqueue_assignment';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
                array('ticket_id, queue_id, assignment_date, assignment_user_id, assignment_firm_id', 'required'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
                'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
                'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
                'assignment_user' => array(self::BELONGS_TO, 'User', 'assignment_user_id'),
                'assignment_firm' => array(self::BELONGS_TO, 'Firm', 'assignment_firm_id'),
                'ticket' => array(self::BELONGS_TO, 'OEModule\PatientTicketing\models\Ticket', 'ticket_id'),
                'queue' => array(self::BELONGS_TO, 'OEModule\PatientTicketing\models\Queue', 'queue_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'is_patient_called' => 'Did you telephone the patient during this review?'
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

    /**
     * Searches for string patterns to replace with assignment data and returns the resultant string.
     *
     * @param string $text
     *
     * @return string $replaced_text
     */
    public function replaceAssignmentCodes($text, $replace_linebreaks = false)
    {
        if ($this->details) {
            $flds = json_decode($this->details, true);

            $by_id = array();
            foreach ($flds as $fld) {
                if (@$fld['widget_name']) {
                    $cls_name = 'OEModule\\PatientTicketing\\widgets\\'.$fld['widget_name'];
                    $widget = new $cls_name();
                    $by_id[$fld['id']] = $widget->getReportString($fld['value']);
                } else {
                    $by_id[$fld['id']] = $fld['value'];
                }
            }

            // match for ticketing fields
            preg_match_all('/\[pt_([a-z_]+)\]/is', $text, $m);

            foreach ($m[1] as $el) {
                $replacement_text = @$by_id[$el] ?
                    $replace_linebreaks ?
                        \OELinebreakReplacer::replace($by_id[$el])
                        : $by_id[$el]
                    : 'Not recorded';
                $text = preg_replace('/\[pt_'.$el.'\]/is', $replacement_text, $text);
            }
        }
        return $text;
    }

    /* Generate the report text */
    public function generateReportText()
    {
        $this->report = \OEModule\PatientTicketing\components\Substitution::replace($this->replaceAssignmentCodes($this->queue->report_definition), $this->ticket->patient);
    }

    public function getFormattedReport()
    {
        return \OEModule\PatientTicketing\components\Substitution::replace($this->replaceAssignmentCodes($this->queue->report_definition, true), $this->ticket->patient);
    }
}
