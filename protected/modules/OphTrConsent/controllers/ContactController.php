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

class ContactController extends \BaseController
{
    public function accessRules()
    {
        return array(
            array('allow', 'users' => array('@')),
        );
    }

    /**
     * Lists all disorders for a given search term.
     */
    public function actionAutocomplete()
    {
        if (\Yii::app()->request->isAjaxRequest) {
            $criteria = new \CDbCriteria();
            $criteria->join = "left join contact_label cl on cl.id = t.contact_label_id ";
            $criteria->join .= "left join address ad on ad.contact_id = t.id";
            if (isset($_GET['term']) && $term = strtolower($_GET['term'])) {
                $criteria->addSearchCondition('LOWER(last_name)', $term, true, 'OR');
                $criteria->addSearchCondition('LOWER(first_name)', $term, true, 'OR');
                $criteria->addSearchCondition('LOWER(cl.name)', $term, true, 'OR');
                $criteria->addSearchCondition('LOWER(t.national_code)', $term, true, 'OR');
                $criteria->addSearchCondition('LOWER(ad.address1)', $term, true, 'OR');
                $criteria->addSearchCondition('LOWER(ad.address2)', $term, true, 'OR');
                $criteria->addSearchCondition('LOWER(ad.postcode)', $term, true, 'OR');
                $criteria->addSearchCondition('LOWER(last_name)', $term, true, 'OR');
            }
            if (isset($_GET['filter'])) {
                $contact_label_id = $_GET['filter'];
                if ($contact_label_id != 'false') {
                    $contact_label = \ContactLabel::model()->findByPk($contact_label_id);
                    $criteria->addCondition(array(
                            'cl.name = ' . '"' . $contact_label->name . '"'
                        ));
                }
            }
            $criteria->addCondition(array('cl.is_private = 0'));
            $criteria->addCondition(array('t.active = 1'));
            $criteria->order = 'cl.name';

            // Limit results
            $criteria->limit = '200';

            $contacts = \Contact::model()->findAll($criteria);
            $return = array();
            foreach ($contacts as $contact) {
                $return[] = $this->contactStructure($contact);
            }
            $this->renderJSON($return);
        }
    }

    /**
     * Lists all disorders for a given search term.
     */
    public function actionPatientcontacts()
    {
        if (\Yii::app()->request->isAjaxRequest) {
            if (isset($_GET['filter'])) {
                $contactLabelName = \ContactLabel::model()->findByPk($_GET['filter'])->name;
                $criteria = new \CDbCriteria();

                if (isset($_GET['term']) && $term = strtolower($_GET['term'])) {
                    $criteria->addSearchCondition('LOWER(last_name)', $term, true, 'OR');
                    $criteria->addSearchCondition('LOWER(first_name)', $term, true, 'OR');
                }

                if ($contactLabelName === 'General Practitioner') {
                    $criteria->select = 'c.title, c.first_name, c.last_name';
                    $criteria->join = "join patient p ON p.contact_id = t.id AND p.id = " . $_GET['code'];
                    $criteria->join .= " join gp g ON g.id = p.gp_id";
                    $criteria->join .= " join contact c ON c.id = g.contact_id";
                    $contact = \Contact::model()->find($criteria);
                    if (isset($contact)) {
                        $fullName = trim(implode(' ', array($contact['title'], $contact['first_name'], $contact['last_name'])));
                        $return = [
                            'label' => $fullName,
                            'name' => $fullName,
                            'phone' => $contact['primary_phone'],
                        ];
                        echo \CJSON::encode($return);
                        \Yii::app()->end();
                    }
                }
                $criteria->select = 't.title, t.first_name, t.last_name, t.primary_phone';
                $criteria->join = "join patient_contact_assignment pca ON pca.contact_id = t.id AND pca.patient_id = " . $_GET['code'];
                $criteria->join .= " join contact_label cl ON cl.id = t.contact_label_id AND cl.id = " . $_GET['filter'];

                $contacts = \Contact::model()->findAll($criteria);
                $return = array();
                foreach ($contacts as $contact) {
                    $return[] = $this->contactStructure($contact);
                }
                $this->renderJSON($return);
            }
        }
    }

    /**
     * Generate array structure of disorder for JSON structure return
     *
     * @param \Contact $contact
     * @return array
     */
    protected function contactStructure(\Contact $contact, $user = null)
    {
        $result = array(
            'label' => $contact->getFullName() .
                (isset($contact->label) && isset($contact->label->name) ?
                    " (" . $contact->label->name . ")" : "")
                . (isset($contact->national_code) && trim($contact->national_code) ? "(" . $contact->national_code . ")" : "") .
                " " . ($contact->address ? $contact->address->getLetterLine() : ""),
            'id' => $contact['id'],
            'name' => $contact->getFullName(),
            'first_name' => $contact->first_name,
            'last_name' => $contact->last_name,
            'email' => $contact->email,
            'phone' => $contact->primary_phone,
            'address' => $contact->address ? $contact->address->getLetterLine() : "",
            'contact_label' => $contact->label ? $contact->label->name : "",
        );

        if ($user) {
            $result['contact_user_id'] = $user->id;
        }

        if (isset($contact->address)) {
            $result['address_line1'] = $contact->address->address1;
            $result['address_line2'] = $contact->address->address2;
            $result['city'] = $contact->address->city;
            $result['country_id'] = $contact->address->country_id;
            $result['postcode'] = $contact->address->postcode;
        }
        return $result;
    }

    public function actionContactPage()
    {
        $selected_contact_type = null;
        if (isset($_GET['selected_contact_type'])) {
            $selected_contact_type = $_GET['selected_contact_type'];
        }
        $this->renderPartial(
            '//contacts/add_new_contact_assignment',
            array('selected_contact_type' => $selected_contact_type),
            false,
            true
        );
    }

    public function actionSaveNewContact()
    {
        if (\Yii::app()->request->isAjaxRequest) {
            if (isset($_POST['data'])) {
                $transaction = \Yii::app()->db->beginTransaction();
                $errors = [];

                $data = json_decode($_POST['data']);
                $contact = new \Contact();
                $contact->scenario = 'self_register';
                $contact->first_name = $data->first_name;
                $contact->last_name = $data->last_name;
                $contact->primary_phone = $data->primary_phone;
                $contact->mobile_phone = $data->mobile_phone;
                $contact->contact_label_id = $data->contact_label_id;
                $contact->created_institution_id = Yii::app()->session['selected_institution_id'];
                $contact->active = 1;
                $contact->email = $data->email;

                $address = new \Address();
                $address->address1 = $data->address1;
                $address->address2 = $data->address2;
                $address->city = $data->city;
                $address->postcode = $data->postcode;
                $address->country_id = $data->country;
                $address->address_type_id = 3;


                if (!$contact->save()) {
                    $errors = $contact->getErrors();
                } else {
                    $address->contact_id = $contact->id;
                    if (!$address->save()) {
                        $errors = array_merge($errors, $address->getErrors());
                    }
                }

                if ($data->contact_label_error) {
                    $errors['contact_label_limit'] = $data->contact_label_error;
                }

                if ($data->contact_label_id == "") {
                    $errors['missing_contact_label'] = "Please select a Contact Type";
                }

                if (trim($data->address1) == "" && trim($data->primary_phone) == "" && trim($data->email) == "") {
                    $errors['missing_address'] = "Address or phone number or an email should be provided";
                }
                if (!empty($errors)) {
                    $transaction->rollback();
                    echo \CJSON::encode(['errors' => $errors]);
                } else {
                    $transaction->commit();
                    echo \CJSON::encode($this->contactStructure($contact));
                }
            }
        }
    }

    /**
     * Lists all Openeyes user contacts for a given search term.
     */
    public function actionOpeneyesContactsWithUser()
    {
        if (\Yii::app()->request->isAjaxRequest) {
            $criteria = new \CDbCriteria();

            if (isset($_GET['term']) && $term = strtolower($_GET['term'])) {
                $term = trim($term);
                $criteria->addSearchCondition('LOWER(contact.last_name)', $term, true, 'OR', 'LIKE');
                $criteria->addSearchCondition('LOWER(contact.first_name)', $term, true, 'OR', 'LIKE');
            }
            $users = \User::model()->with('contact')->findAll($criteria);

            $return = array();
            foreach ($users as $i => $user) {
                if($user->contact === null){
                    $user->contact = new \Contact();
                }
                $return[$i] = $this->contactStructure($user->contact, $user);
                $return[$i]['user_id'] = $user->id;
                $return[$i]['type'] = "Contact";
            }

            $this->renderJSON($return);
        }
    }

    public function actionAllPatientContacts()
    {
        $patient_id = Yii::app()->request->getQuery('code');

        if (strlen($patient_id)===0) {
            return [];
        }

        if (\Yii::app()->request->isAjaxRequest) {
            $criteria = new \CDbCriteria();
            $criteria->join = "join patient_contact_assignment pca ON pca.contact_id = t.id";
            $criteria->join .= " join contact_label cl ON cl.id = t.contact_label_id";

            if (isset($_GET['term']) && $term = strtolower($_GET['term'])) {
                $criteria->addSearchCondition('LOWER(last_name)', $term, true, 'OR');
                $criteria->addSearchCondition('LOWER(first_name)', $term, true, 'OR');
            }

            $criteria->addSearchCondition('pca.patient_id', $patient_id, true, 'OR');

            $contacts = \Contact::model()->findAll($criteria);
            $return = array();
            foreach ($contacts as $contact) {
                $return[] = $this->contactStructure($contact);
            }

            // exception (only in parient contact mode)
            $return[] = [
                'type' => "custom",
                'label' => 'Add a new contact',
            ];
            $this->renderJSON($return);
        }
    }
}
