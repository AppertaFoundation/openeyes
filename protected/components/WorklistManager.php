<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * This component class is intended to encaspulate the logic of interacting with the Worklists
 *
 * Class WorklistManager
 */
class WorklistManager extends CComponent
{
    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @param Worklist $worklist
     * @param Patient $patient
     * @return array|CActiveRecord|mixed|null
     */
    public function getWorklistPatient(Worklist $worklist, Patient $patient)
    {
        return WorklistPatient::model()->findByAttributes(array('patient_id' => $patient->id, 'worklist_id' => $worklist->id));
    }

    /**
     * Wrapper for starting a transaction
     *
     * @return CDbTransaction|null
     */
    protected function startTransaction()
    {
        return Yii::app()->db->getCurrentTransaction() === null
            ? Yii::app()->db->beginTransaction()
            : null;
    }

    /**
     * If the given Patient is successfully added to the given Worklist, returns true. false otherwise
     *
     * @param Patient $patient
     * @param Worklist $worklist
     * @param datetime $when
     * @param array $attributes
     * @return bool
     */
    public function addPatientToWorklist(Patient $patient, Worklist $worklist, $when=null, $attributes = array())
    {
        $this->reset();


        if ($this->getWorklistPatient($worklist, $patient)) {
            $this->addError("Patient is already on the given worklist.");
            return false;
        }

        $transaction = $this->startTransaction();

        $valid_attributes = array();
        foreach ($worklist->mapping_attributes as $attr)
            $valid_attributes[$attr->name] = $attr->id;

        try {
            $wp = new WorklistPatient();
            $wp->patient_id = $patient->id;
            $wp->worklist_id = $worklist->id;
            if ($when)
                $wp->when = $when;

            $wp->save();

            foreach ($attributes as $attr => $val)
            {
                if (!array_key_exists($attr, $valid_attributes))
                    throw new Exception("Unrecognised attribute {$attr} for {$worklist->name}");
                $wlattr = new WorklistPatientAttribute();
                $wlattr->attributes = array(
                    'worklist_patient_id' => $wp->id,
                    'worklist_attribute_id' => $valid_attributes[$attr],
                    'attribute_value' => $val
                );
                $wlattr->save();
            }
            if ($transaction)
                $transaction->commit();
        }
        catch (Exception $e) {
            $this->addError($e->getMessage());
            if ($transaction)
                $transaction->rollback();
            return false;
        }

        return true;
    }

    /**
     * Internal method to reset state for error tracking
     */
    protected function reset()
    {
        $this->errors = array();
    }

    /**
     * @param string $message
     */
    protected function addError($message)
    {
        $this->errors[] = $message;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }
}