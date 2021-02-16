<?php

namespace OEModule\PASAPI\resources;

/*
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class Patient extends BaseResource
{
    protected static $resource_type = 'Patient';
    protected static $model_class = 'Patient';

    public $isNewResource;

    /**
     * @return bool
     */
    public function shouldValidateRequired()
    {
        return $this->isNewResource || !$this->partial_record;
    }

    /**
     * As a primary resource (i.e. mapped to external resource) we need to ensure we have an id for tracking
     * the resource in the system.
     *
     * @return bool
     */
    public function validate()
    {
        if (!$this->id) {
            $this->addError('Resource ID required');
        }

        return parent::validate();
    }

    /**
     * If this Patient resource points to an already existing Patient, then update
     * otherwise create a new one.
     */
    public function save()
    {
        $assignment = $this->getAssignment();
        $model = $assignment->getInternal();
        $this->isNewResource = $model->isNewRecord;

        if (!$this->validate()) {
            return;
        }

        $transaction = $this->startTransaction();

        try {
            if ($this->isNewResource && $this->update_only) {
                return;
            }

            if ($this->saveModel($model)) {
                $assignment->internal_id = $model->id;
                $assignment->save();
                $assignment->unlock();

                $this->audit($this->isNewResource ? 'create' : 'update', null, null, array('patient_id' => $model->id));

                if ($transaction) {
                    $transaction->commit();
                }

                return $model->id;
            } else {
                if ($transaction) {
                    $transaction->rollback();
                }
            }
        } catch (\Exception $e) {
            if ($transaction) {
                $transaction->rollback();
            }

            throw $e;
        }
    }

    /**
     * Assign the Patient resource attributes to the given Patient model
     * and save it.
     *
     * @param \Patient $patient
     *
     * @throws \Exception
     *
     * @return bool|null
     */
    public function saveModel(\Patient $patient)
    {
        $this->assignProperty($patient, 'nhs_num', 'NHSNumber');
        $this->assignProperty($patient, 'hos_num', 'HospitalNumber');
        $this->assignProperty($patient, 'dob', 'DateOfBirth');
        $this->assignProperty($patient, 'is_deceased', 'IsDeceased');

        // Special case to resurrect patients.
        if ($this->partial_record && !$patient->is_deceased && !property_exists($this, 'DateOfDeath')) {
            $patient->date_of_death = null;
        } else {
            $this->assignProperty($patient, 'date_of_death', 'DateOfDeath');
        }

        $this->mapGender($patient);
        $this->mapEthnicGroup($patient);
        $this->mapGp($patient);
        $this->mapPractice($patient);
        $this->mapNhsNumberStatus($patient);

        if (!$patient->validate()) {
            $this->addModelErrors($patient->getErrors());

            return;
        }
        $patient->save();

        // Set the contact details
        // ContactBehavior.php creates a contact automatically before save
        $contact = $patient->contact;
        $contact->source = "PASAPI";

        $this->assignProperty($contact, 'title', 'Title');
        $this->assignProperty($contact, 'first_name', 'FirstName');
        $this->assignProperty($contact, 'last_name', 'Surname');
        $this->assignProperty($contact, 'primary_phone', 'TelephoneNumber');

        if (!$contact->validate()) {
            $this->addModelErrors($contact->getErrors());

            return;
        }

        $contact->save();

        $this->mapAddresses($contact);

        if (!$this->errors) {
            return true;
        }
    }

    private function mapGender(\Patient $patient)
    {
        if (property_exists($this, 'Gender')) {
            $patient->gender = strtoupper($this->getAssignedProperty('Gender'));
        } else {
            if (!$this->partial_record) {
                $patient->gender = null;
            }
        }
    }

    /**
     * Handle mapping the ethnic group resource to the patient model.
     *
     * @param \Patient $patient
     */
    private function mapEthnicGroup(\Patient $patient)
    {
        if (property_exists($this, 'EthnicGroup')) {
            $code = $this->getAssignedProperty('EthnicGroup');
            if ($code) {
                if ($eg = \EthnicGroup::model()->findByAttributes(array('code' => $code))) {
                    $patient->ethnic_group_id = $eg->id;
                } else {
                    $this->addWarning('Unrecognised ethnic group code '.$code);
                }
            } else {
                $patient->ethnic_group_id = null;
            }
        } else {
            if (!$this->partial_record) {
                $patient->ethnic_group_id = null;
            }
        }
    }

    /**
     * Handle mapping of the Gp resource code to the patient model.
     *
     * @param \Patient $patient
     */
    private function mapGp(\Patient $patient)
    {
        if (property_exists($this, 'GpCode')) {
            $code = $this->getAssignedProperty('GpCode');
            if ($code) {
                if ($gp = \Gp::model()->findByAttributes(array('nat_id' => $code))) {
                    $patient->gp_id = $gp->id;
                } else {
                    $this->addWarning('Could not find '.\SettingMetadata::model()->getSetting('gp_label').' for code '.$code);
                }
            } else {
                $patient->gp_id = null;
            }
        } else {
            if (!$this->partial_record) {
                $patient->gp_id = null;
            }
        }
    }

    /**
     * Handle mapping of the resource PracticeCode to the Patient model.
     *
     * @param \Patient $patient
     */
    private function mapPractice(\Patient $patient)
    {
        if (property_exists($this, 'PracticeCode')) {
            if ($code = $this->getAssignedProperty('PracticeCode')) {
                if ($practice = \Practice::model()->findByAttributes(array('code' => $code))) {
                    $patient->practice_id = $practice->id;
                } else {
                    $this->addWarning('Could not find Practice for code '.$code);
                }
            } else {
                $patient->practice_id = null;
            }
        } else {
            if (!$this->partial_record) {
                $patient->practice_id = null;
            }
        }
    }

    /**
     * Will create or update addresses for the given contact based on matching by postcode.
     *
     * It may be useful to abstract this to a helper class or for it to be a static method
     * on the Address resource ... if we wind up dooing more API importing.
     *
     * @param \Contact $contact
     *
     * @throws \Exception
     */
    private function mapAddresses(\Contact $contact)
    {
        if (property_exists($this, 'AddressList')) {
            $matched_address_ids = array();
            foreach ($this->AddressList as $idx => $address_resource) {
                $matched_clause = ($matched_address_ids) ? ' AND id NOT IN ('.implode(',', $matched_address_ids).')' : '';
                $address_model = \Address::model()->find(array(
                    'condition' => "contact_id = :contact_id AND REPLACE(postcode,' ','') = :postcode".$matched_clause,
                    'params' => array(':contact_id' => $contact->id, ':postcode' => str_replace(' ', '', $address_resource->Postcode)),
                ));

                if (!$address_model) {
                    $address_model = new \Address();
                    $address_model->contact_id = $contact->id;
                }

                if ($address_resource->saveModel($address_model)) {
                    $matched_address_ids[] = $address_model->id;
                    foreach ($address_resource->warnings as $warn) {
                        $this->addWarning("Address {$idx}: {$warn}");
                    }
                } else {
                    $this->addWarning("Address {$idx} not added");
                    foreach ($address_resource->errors as $err) {
                        $this->addWarning("Address {$idx}: {$err}");
                    }
                }
            }
            // clear out any addresses not matched
            $this->deleteAddresses($contact, $matched_address_ids);
        } else {
            if (!$this->partial_record) {
                $this->deleteAddresses($contact);
            }
        }
    }

    private function deleteAddresses(\Contact $contact, $except_ids = array())
    {
        // delete any address that are no longer relevant
        $matched_string = implode(',', $except_ids);
        $condition_str = 'contact_id = :contact_id';
        if ($matched_string) {
            $condition_str .= " AND id NOT IN($matched_string)";
        }

        \Address::model()->deleteAll(array(
            'condition' => $condition_str,
            'params' => array(':contact_id' => $contact->id),
        ));
    }

    /**
     * @param \Patient $patient
     */
    private function mapNhsNumberStatus(\Patient $patient)
    {
        $status = null;
        if (property_exists($this, 'NHSNumberStatus')) {
            if ($code = $this->getAssignedProperty('NHSNumberStatus')) {
                if ($status = \NhsNumberVerificationStatus::model()->findByAttributes(array('code' => $code))) {
                    $patient->nhs_num_status_id = $status->id;
                } else {
                    $this->addWarning('Unrecognised '.\SettingMetadata::model()->getSetting('nhs_num_label').' number status code '.$code);
                }
            } else {
                $patient->nhs_num_status_id = null;
            }
        } else {
            if (!$this->partial_record) {
                $patient->nhs_num_status_id = null;
            }
        }
    }
}
