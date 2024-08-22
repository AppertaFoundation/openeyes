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

/**
 * This is the model class for table "followup_analysis_aggregate".
 *
 * The followings are the available columns in table 'followup_analysis_aggregate':
 *
 * @property int $id
 * @property int $patient_id ID of patient this followup applies to
 * @property int $event_id ID of event this followup applies to
 * @property int $ticket_id ID of ticket this followup applies to
 * @property string $type Type of followup
 * @property string $made_at_date Date followup was made
 * @property string $due_date Date followup is due
 *
 * The following are the available model relations:
 */
class FollowupAnalysisAggregate extends BaseActiveRecord
{
    public const TYPE_FOLLOWUP = 'FollowUp';
    public const TYPE_TICKETED = 'Ticketed';
    public const TYPE_REFERRAL = 'Referral';

    public const FOLLOWUP_WEEK_LIMITED = 78;
    private const WEEKTIME = 604800;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return FollowupAnalysisAggregate the static model class
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
        return 'followup_analysis_aggregate';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('patient_id, event_id, ticket_id, made_at_date, due_date', 'safe'),
            array('patient_id, type, made_at_date', 'required'),
            array('id, patient_id, event_id, ticket_id, type, made_at_date, due_date', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'patient' => array(self::BELONGS_TO, 'Patient', 'patient_id'),
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'ticket' => array(self::BELONGS_TO, 'OEModule\\PatientTicketing\\models\\Ticket', 'ticket_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'patient_id' => 'Patient',
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
        $criteria->compare('patient_id', $this->patient_id, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    private function updateOrRemoveExaminationOrDocumentEntry()
    {
        $updated = false;

        switch ($this->type) {
            case self::TYPE_FOLLOWUP:
                $followup = self::findLatestFollowUpElementEvent($this->patient_id);

                if ($followup) {
                    $this->event_id = $followup['event_id'];
                    $this->made_at_date = $followup['event_date'];
                    $this->due_date = $followup['due_date'];

                    $updated = true;
                }
                break;

            case self::TYPE_TICKETED:
                return; // This is handled separately, thus the caller of this function should not pass entries of this type

            case self::TYPE_REFERRAL:
                $referral = self::findLatestReferralLetterEvent($this->patient_id);

                if ($referral) {
                    $this->event_id = $referral['event_id'];
                    $this->made_at_date = $referral['event_date'];

                    $updated = true;
                }
                break;
        }

        if ($updated) {
            $this->save();
        } else {
            $this->delete();
        }
    }

    private static function findLatestFollowUpElementEvent($patient_id)
    {
        return Yii::app()->db->createCommand()
            ->select(
                "
                e.id as event_id,
                e.event_date as event_date,
                DATE_ADD(event_date, INTERVAL IF(period.name = 'weeks', 7 ,IF( period.name = 'months', 30, IF(period.name = 'years', 365, 1)))*eoc_entry.followup_quantity DAY) as due_date
            "
            )
            ->from("event e")
            ->leftjoin("episode e2", "e.episode_id = e2.id")
            ->leftjoin("patient p", "p.id = e2.patient_id")
            ->leftjoin("event_type e3", "e3.id = e.event_type_id")
            ->leftjoin("et_ophciexamination_clinicoutcome eoc", "eoc.event_id = e.id")
            ->leftjoin("ophciexamination_clinicoutcome_entry eoc_entry", "eoc_entry.element_id = eoc.id")
            ->leftjoin("period", "period.id = eoc_entry.followup_period_id")
            ->where("p.deleted <> 1 and e.deleted <> 1 and e2.deleted <> 1")
            ->andWhere('p.id = :patient_id', [':patient_id' => $patient_id])
            ->andWhere("lower(e3.name) like lower('%examination%')")
            ->andWhere(
                "
                e.event_date = (
                    select MAX(e4.event_date) from event e4
                    left join episode e5 on e4.episode_id = e5.id
                    left join patient p2 on e5.patient_id = p2.id
                    left join event_type e6 ON e6.id = e4.event_type_id
                    WHERE p2.id = p.id and e4.deleted = 0 and e5.deleted = 0
                    and lower(e3.name) like lower('%examination%')
                )
            "
            )
            ->andWhere("eoc.id is not null")
            ->andWhere("eoc_entry.followup_period_id is not null")
            ->queryRow();
    }

    /* Get the waiting follow up data, uses Document event with referral letter type and later on the worklist time
        To calculate how long a patient will wait frm the date of referral to the date assigned in a worklist */
    private static function findLatestReferralLetterEvent($patient_id)
    {
        return Yii::app()->db->createCommand()
            ->select(
                "
                e.id as event_id,
                p.id as patient_id,
                e.event_date as event_date
            "
            )
            ->from("event e")
            ->leftjoin("episode e2", "e.episode_id = e2.id")
            ->leftjoin("patient p", "p.id = e2.patient_id")
            ->leftjoin("event_type e3", "e3.id = e.event_type_id")
            ->leftjoin("et_ophcodocument_document eod", "e.id = eod.event_id")
            ->leftjoin("ophcodocument_sub_types ost", "eod.event_sub_type = ost.id")
            ->where("ost.name = 'Referral Letter'")
            ->andWhere('p.id = :patient_id', [':patient_id' => $patient_id])
            ->andWhere("p.deleted <> 1 and e.deleted <> 1 and e2.deleted <> 1")
            ->andWhere("lower(e3.name) like lower('%document%')")
            ->andWhere(
                "
                e.event_date = (
                    select MAX(e4.event_date) from event e4
                    left join episode e5 on e4.episode_id = e5.id
                    left join patient p2 on e5.patient_id = p2.id
                    left join event_type e6 ON e6.id = e4.event_type_id
                WHERE p2.id = p.id and e4.deleted = 0 and e5.deleted = 0
                and lower(e3.name) like lower('%document%')
                )
            "
            )
            ->queryRow();
    }

    private static function createEntry($type, $patient_id, $event_id, $ticket_id, $made_at_date, $due_date)
    {
        $entry = new FollowupAnalysisAggregate();

        $entry->type = $type;
        $entry->patient_id = $patient_id;
        $entry->event_id = $event_id;
        $entry->ticket_id = $ticket_id;
        $entry->made_at_date = $made_at_date;
        $entry->due_date = $due_date;

        $entry->save();

        return $entry;
    }

    public static function makeDueDate($from, $period, $quantity)
    {
        $due_interval = 'P' . $quantity;

        switch ($period) {
            case 'weeks':
                $due_interval .= 'W';
                break;
            case 'months':
                $due_interval .= 'M';
                break;
            case 'years':
                $due_interval .= 'Y';
                break;

            case 'days':
            default:
                $due_interval .= 'D';
                break;
        }

        $due_date = new DateTime($from);
        $due_date->add(new \DateInterval($due_interval));

        return $due_date->format('Y-m-d H:i:s');
    }

    public static function updateForPatientExaminationOrDocument($patient_id)
    {
        $entries = array_reduce(
            self::model()->findAll(
                'patient_id = :patient_id AND type <> :ignore_type',
                [':patient_id' => $patient_id, ':ignore_type' => self::TYPE_TICKETED]
            ),
            static function ($into, $entry) {
                $into[$entry['type']] = $entry;
                return $into;
            },
            []
        );

        if (!empty($entries[self::TYPE_FOLLOWUP])) {
            $entries[self::TYPE_FOLLOWUP]->updateOrRemoveExaminationOrDocumentEntry();
        } elseif ($followup = self::findLatestFollowUpElementEvent($patient_id)) {
            self::createEntry(self::TYPE_FOLLOWUP, $patient_id, $followup['event_id'], null, $followup['event_date'], $followup['due_date']);
        }

        if (!empty($entries[self::TYPE_REFERRAL])) {
            $entries[self::TYPE_REFERRAL]->updateOrRemoveExaminationOrDocumentEntry();
        } elseif ($referral = self::findLatestReferralLetterEvent($patient_id)) {
            self::createEntry(self::TYPE_REFERRAL, $patient_id, $referral['event_id'], null, $referral['event_date'], null);
        }
    }

    public static function updateForPatientTickets($patient_id, $ticket_id)
    {
        $api = Yii::app()->moduleAPI->get('PatientTicketing');

        if ($api) {
            $entry = self::model()->find('patient_id = :patient_id AND ticket_id = :ticket_id',
                                         [':patient_id' => $patient_id, ':ticket_id' => $ticket_id]);
            $followup = $api->getFollowUp($ticket_id);

            if ($entry) {
                if ($followup) {
                    $entry->made_at_date = $followup['assignment_date'];
                    $entry->due_date = self::makeDueDate($followup['assignment_date'], $followup['followup_period'], $followup['followup_quantity']);

                    $entry->save();
                } else {
                    $entry->delete();
                }
            } elseif ($followup) {
                $ticket = \OEModule\PatientTicketing\models\Ticket::model()->findByPk((int) $ticket_id);
                $event_id = $ticket->event ? $ticket->event->id : null;

                $due_date = self::makeDueDate($followup['assignment_date'], $followup['followup_period'], $followup['followup_quantity']);

                self::createEntry(self::TYPE_TICKETED, $patient_id, $event_id, $ticket_id, $followup['assignment_date'], $due_date);
            }
        }
    }

    public static function retrieveFormattedAnalytics(&$patient_list, &$csv_data, $start_date = null, $end_date = null, $diagnosis_text = null, $surgeon_id = null, $subspecialty_id = null)
    {
        $command = Yii::app()->db->createCommand()
                 ->select('type, faa.patient_id AS patient_id,
                           UNIX_TIMESTAMP(made_at_date) AS made_at_date,
                           UNIX_TIMESTAMP(due_date) AS due_date,
                           CAST(DATEDIFF(due_date, current_date()) / 7 AS SIGNED) AS weeks,
                           MIN(UNIX_TIMESTAMP(`when`)) AS `when`,
                           MAX(UNIX_TIMESTAMP(w.start)) AS start,
                           UNIX_TIMESTAMP(e.event_date) AS event_date')
                 ->from('followup_analysis_aggregate faa')
                 ->leftjoin("worklist_patient wp", "wp.patient_id = faa.patient_id")
                 ->leftjoin("worklist w", "w.id = wp.worklist_id")
                 ->leftjoin('event e', 'e.id = event_id')
                 ->join('patient p', 'p.id = faa.patient_id')
                 ->where('p.deleted = 0 AND e.deleted = 0 AND p.is_deceased = 0')
                 ->group('faa.id');

        if ($start_date) {
            $command->andWhere('UNIX_TIMESTAMP(made_at_date) >= :start_date', [':start_date' => $start_date]);
        }

        if ($end_date) {
            $command->andWhere('UNIX_TIMESTAMP(made_at_date) <= :end_date', [':end_date' => $end_date]);
        }

        if ($diagnosis_text) {
            $command->andWhere('faa.patient_id IN (' . $diagnosis_text . ')');
        }

        if ($surgeon_id) {
            $command->andWhere('e.created_user_id = :surgeon_id', [':surgeon_id' => $surgeon_id]);
        }

        if ($subspecialty_id) {
            $command
                ->join("firm f", "e.firm_id = f.id")
                ->join("service_subspecialty_assignment ssa", "ssa.id = f.service_subspecialty_assignment_id")
                ->andWhere('ssa.subspecialty_id = :subspecialty_id', [':subspecialty_id'  => $subspecialty_id]);
        }

        $results = $command->queryAll();

        $current_time = time();

        foreach ($results as $result) {
            $weeks = $result['weeks'];
            $into = '';
            $overdue = $weeks < 0;

            switch ($result['type']) {
                case self::TYPE_FOLLOWUP:
                    $overdue = $weeks <= 0;
                    // No break as the 'followup' and 'ticketed' case are the same bar the criteria for being overdue.

                case self::TYPE_TICKETED:
                    $latest_time = max($result['event_date'], $result['start']);

                    if ($overdue) {
                        if (!$latest_time || $latest_time > $result['made_at_date']) {
                            continue 2;
                        }

                        $weeks = -$weeks;

                        $into = 'overdue';
                    } else {
                        if ($result['start'] > $current_time && $result['start'] < $result['due_date']) {
                            continue 2;
                        }

                        $into = 'coming';
                    }
                    break;

                case self::TYPE_REFERRAL:
                    $current_referral_date = $result['made_at_date'];

                    if (!empty($result['when'])) {
                        $appointment_time = $result['when'];

                        if ($appointment_time >= $current_referral_date) {
                            $waiting_time = ceil(($appointment_time - $current_referral_date) / self::WEEKTIME);
                        }
                    } else {
                        $current_time = time();

                        if ($current_time > $current_referral_date) {
                            $waiting_time = ceil(($current_time - $current_referral_date) / self::WEEKTIME);
                        }
                    }

                    if (!isset($waiting_time)) {
                        continue 2;
                    }

                    $weeks = $waiting_time;
                    $into = 'waiting';
                    break;
            }

            if ($into !== '' && $weeks <= self::FOLLOWUP_WEEK_LIMITED) {
                $patient_list[$into][$weeks][] = $result['patient_id'];

                $csv_data[$into][] = array(
                    'patient_id' => $result['patient_id'],
                    'weeks' => $weeks,
                );
            }
        }
    }
}
