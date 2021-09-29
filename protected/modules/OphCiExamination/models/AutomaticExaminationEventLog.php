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

class AutomaticExaminationEventLog extends BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return UniqueCodes the static model class
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
        return 'automatic_examination_event_log';
    }

    public function rules()
    {
        return array(
            array('id,event_id,unique_code,examination_data,examination_date,import_status,invoice_status_id,comment', 'safe'),
            array('event_id,unique_code,examination_data', 'required'),
        );
    }

    public function behaviors()
    {
        return array(
            'LookupTable' => 'LookupTable',
        );
    }

    public function relations()
    {
        return array(
            'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
            'import_status' => array(self::BELONGS_TO, 'ImportStatus', 'import_success'),
            'invoice_status' =>array(self::BELONGS_TO, 'OEModule\OphCiExamination\models\InvoiceStatus', 'invoice_status_id')
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

    public function latestSuccessfulEvent()
    {
        $criteria = new CDbCriteria();
        $criteria->join = 'JOIN import_status ON import_status.id = t.import_success';
        $criteria->condition = 'import_status.status_value <> "Import Failure" AND import_status.status_value <> "Dismissed Event"';
        $criteria->order = 'created_date DESC, id DESC';
        $criteria->limit = 1;

        return $this->find($criteria);
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function optomSearch($filter = array())
    {
        $criteria = $this->buildOptomFilterCriteria($filter);

        return new CActiveDataProvider(get_class($this), array(
            'criteria'=>$criteria,
            'sort'=>array(
                'defaultOrder'=>'t.created_date DESC',
            ),
            'pagination'=>array(
                'pageSize'=>25
            ),
        ));
    }

    /*
     * Optometrist feedback manager filter handle function
     * @param array        $filter
     */
    protected function buildOptomFilterCriteria($filter = array())
    {
        $criteria = new \CDbCriteria();
        $criteria->with = array('event.episode.patient.identifiers');
        $criteria->together = true;
        $criteria->condition = "import_success = 1";
        $criteria->addCondition("event.deleted<>1");

        $this->handleDateRangeFilter($criteria, $filter);
        $this->invoiceStatusSearch($criteria, $filter);
        $this->patientNumberSearch($criteria, $filter);
        $this->optomNameSearch($criteria, $filter);
        $this->gocNumberSearch($criteria, $filter);

        return $criteria;

    }

    /**
     * Date range search in optometrist feedback manager
     * @param \CDbCriteria $criteria
     * @param array        $filter
     */

    private function handleDateRangeFilter(\CDbCriteria $criteria, $filter = array())
    {
        $from = null;
        if (isset($filter['date_from'])) {
            $from = \Helper::convertNHS2MySQL($filter['date_from']);
        }
        $to = null;
        if (isset($filter['date_to'])) {
            $to = \Helper::convertNHS2MySQL($filter['date_to']);
        }
        if ($from && $to) {
            if ($from > $to) {
                $criteria->addBetweenCondition('DATE(t.created_date)', $to, $from);
            } else {
                $criteria->addBetweenCondition('DATE(t.created_date)', $from, $to);
            }
        } elseif ($from) {
            $criteria->addCondition('DATE(t.created_date) >= :from');
            $criteria->params[':from'] = $from;
        } elseif ($to) {
            $criteria->addCondition('DATE(t.created_date) <= :to');
            $criteria->params[':to'] = $to;
        }
    }

    /*
     * Invoice status search in optometrist feedback manager
     * @param \CDbCriteria $criteria
     * @param array        $filter
     */
    private function invoiceStatusSearch(\CDbCriteria $criteria, $filter)
    {
        if (array_key_exists('status_id', $filter) && $filter['status_id'] !== '') {
            $criteria->addCondition('invoice_status_id = :invoice_status_id');
            $criteria->params[':invoice_status_id'] = $filter['status_id'];
        }
    }

    /*
     * Patient search in optometrist feedback manager
     * @param \CDbCriteria $criteria
     * @param array        $filter
     */
    private function patientNumberSearch(\CDbCriteria $criteria, $filter)
    {
        if (array_key_exists('patient_number', $filter) && $filter['patient_number'] !== '') {
            $patient_search = new \PatientSearch();
            $patient_search_details = $patient_search->prepareSearch($filter['patient_number']);
            $terms_with_types = $patient_search_details['terms_with_types'] ?? [];

            $id_condition = [];
            foreach ($terms_with_types as $tid => $item) {
                $type = $item['patient_identifier_type'];
                $id_condition[] = "value = :{$tid}_value AND patient_identifier_type_id = :{$tid}_type_id";
                $criteria->params[":{$tid}_value"] = $item['term'];
                $criteria->params[":{$tid}_type_id"] = $type['id'];
            }

            if ($id_condition) {
                $criteria->addCondition(implode(' OR ', $id_condition));
            } else {
                // no type to returned to search in, so return no result
                $criteria->addCondition("1=0");
            }
        }
    }

    /*
     * Optometrist search in optometrist feedback manager
     * @param \CDbCriteria $criteria
     * @param array        $filter
     */
    private function optomNameSearch(\CDbCriteria $criteria, $filter)
    {
        if (array_key_exists('optometrist', $filter) && $filter['optometrist'] !== '') {
            //$criteria->addCondition('optometrist = :optometrist');
            $criteria->addSearchCondition('optometrist', $filter['optometrist']);
           // $criteria->params[':optometrist'] = $filter['optometrist'];
        }
    }

    /*
     * GOC number search in optometrist feedback manager
     * @param \CDbCriteria $criteria
     * @param array        $filter
     */
    private function gocNumberSearch(\CDbCriteria $criteria, $filter)
    {
        if (array_key_exists('goc_number', $filter) && $filter['goc_number'] !== '') {
            $criteria->addCondition('goc_number = :goc_number');
            $criteria->params[':goc_number'] = $filter['goc_number'];
        }
    }

    /*
     * Generate invoice status dropdown to optometrist feedback manager view
     * @param integer        $default
     */
    public function invoiceStatusSelect($default)
    {
        $status = \OEModule\OphCiExamination\models\InvoiceStatus::model();
        return CHtml::dropDownList(
            'invoice_status_id',
            $default,
            CHtml::listData($status->findAll(), 'id', 'name'),
            array(
                'empty'=>' - '
            )
        );
    }
}
