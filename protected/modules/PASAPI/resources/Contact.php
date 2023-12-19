<?php

/**
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
 *
 * @property Address[] $AddressList
 */

namespace OEModule\PASAPI\resources;

use CActiveRecord;
use Exception;

class Contact extends BaseResource
{
    protected static $resource_type = 'Contact';

    /**
     * Assign the Contact resource attributes to the given Contact model
     * and save it.
     *
     * @param CActiveRecord|\Contact $contact
     *
     * @throws Exception
     *
     * @return bool|null
     */
    public function saveModel($contact)
    {
        $contact->scenario = "pasapi_import";
        $contact->source = "PASAPI";

        $this->assignProperty($contact, 'title', 'Title');
        $this->assignProperty($contact, 'first_name', 'FirstName');
        $this->assignProperty($contact, 'last_name', 'Surname');
        $this->assignProperty($contact, 'primary_phone', 'TelephoneNumber');
        $this->assignProperty($contact, 'mobile_phone', 'MobilePhoneNumber');
        $this->assignProperty($contact, 'email', 'Email');

        if ($this->getAssignedProperty('ContactType')) {
            $contact->contact_label_id = static::getContactType($this->getAssignedProperty('ContactType'));
        }

        if (!$contact->validate()) {
            $this->addModelErrors($contact->getErrors());

            return false;
        }

        $contact->save();

        $this->mapAddresses($contact);

        if (!$this->errors) {
            return true;
        }
        return false;
    }

    private static function getContactType($contact_type): ?int
    {
        $contact_label = \ContactLabel::model()->find('UPPER(name) = UPPER(:label)', [':label' => $contact_type]);

        if (!$contact_label) {
            $contact_label = new \ContactLabel();
            $contact_label->name = $contact_type;
            $contact_label->save();
            $contact_label->refresh();
        }
        return $contact_label->id;
    }

    /**
     * Will create or update addresses for the given contact based on matching by postcode.
     *
     * It may be useful to abstract this to a helper class or for it to be a static method
     * on the Address resource ... if we wind up dooing more API importing.
     *
     * @param \Contact $contact
     *
     * @throws Exception
     */
    private function mapAddresses(\Contact $contact)
    {
        if (property_exists($this, 'AddressList')) {
            $matched_address_ids = array();
            foreach ($this->AddressList as $idx => $address_resource) {
                $matched_clause = ($matched_address_ids) ? ' AND id NOT IN (' . implode(',', $matched_address_ids) . ')' : '';
                $address_model = \Address::model()->find(array(
                    'condition' => "contact_id = :contact_id AND REPLACE(postcode,' ','') = :postcode" . $matched_clause,
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
        } elseif (!$this->partial_record) {
            $this->deleteAddresses($contact);
        }
    }

    /**
     * @param \Contact $contact
     * @param array $except_ids
     * @throws Exception
     */
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
}
